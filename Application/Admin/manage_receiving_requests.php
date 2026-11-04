<?php
session_start();

/*if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}*/

include "sidebar_header.php";
include "../config.php";

// Handle Approve or Reject Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {


        // Get requested blood type and units
        $fetchReq = "SELECT bloodtype, units FROM blood_requests WHERE request_id = $request_id";
        $reqResult = $conn->query($fetchReq);

        if ($reqResult && $reqResult->num_rows > 0) {
            $reqRow = $reqResult->fetch_assoc();
            $bloodtype = $reqRow['bloodtype'];
            $unitsNeeded = $reqRow['units'];

            // Fetch available units from inventory
            $invQuery = "SELECT units_available FROM blood_inventory WHERE bloodtype = '$bloodtype'";
            $invResult = $conn->query($invQuery);

            if ($invResult && $invResult->num_rows > 0) {
                $invRow = $invResult->fetch_assoc();
                $available = $invRow['units_available'];

                if ($available < $unitsNeeded) {
                    // Not enough inventory 
                    echo "<script>alert('Not enough blood units available in inventory!'); 
                      window.location='manage_receiving_requests.php';</script>";
                    exit();
                } else {
                    // Deduct units from inventory
                    $newUnits = $available - $unitsNeeded;
                    $updateInv = "UPDATE blood_inventory SET units_available = $newUnits 
                              WHERE bloodtype = '$bloodtype'";
                    $conn->query($updateInv);
                }
            }
        }

        // Approve Request
        $updateQuery = "UPDATE blood_requests SET status = 'approved' WHERE request_id = $request_id";
    } elseif ($action === 'reject') {
        $updateQuery = "UPDATE blood_requests SET status = 'rejected' WHERE request_id = $request_id";
    }

    $conn->query($updateQuery);
    header("Location: manage_receiving_requests.php");
    exit();
}

// Fetch all requests
$sql = "SELECT * FROM blood_requests ORDER BY request_id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Requests - Admin</title>
    <link rel="stylesheet" href="../Recipient/recipient_style.css">
    <link rel="stylesheet" href="admin_style.css">
</head>

<body>

    <section id="manage-requests" style="max-width: 1100px;">
        <h2>Manage Blood Requests</h2>

        <?php if ($result && $result->num_rows > 0): ?>
            <table class="requests-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Requester Type</th>
                        <th>Email</th>
                        <th>Blood Type</th>
                        <th>Units</th>
                        <th>Location</th>
                        <th>Requested At</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = 1;
                    while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo str_pad($counter++, 3, "0", STR_PAD_LEFT); ?></td>
                            <td><?php echo ucfirst($row['username'])
                                ?>
                            </td>
                            <td><?php echo ucfirst($row['requester_type']); ?></td>
                            <td>
                                <?php
                                if ($row['requester_type'] === 'recipient') echo $row['recipient_email'];
                                else echo $row['hospital_email'];
                                ?>
                            </td>
                            <td><?php echo $row['bloodtype']; ?></td>
                            <td><?php echo $row['units']; ?></td>
                            <td>
                                <?php
                                /*if ($row['requester_type'] === 'recipient') */ echo $row['location'];
                                /*else echo $row['hospital_name'];*/
                                ?>
                            </td>
                            <td><?php echo $row['requested_at']; ?></td>
                            <td><span class="status <?php echo strtolower($row['status']); ?>"><?php echo ucfirst($row['status']); ?></span></td>
                            <td>
                                <?php if ($row['status'] === 'pending'): ?>
                                    <button class="approve-btn" onclick="openConfirmModal(<?php echo $row['request_id']; ?>, 'approve')">Approve</button>
                                    <button class="reject-btn" onclick="openConfirmModal(<?php echo $row['request_id']; ?>, 'reject')">Reject</button>
                                <?php else: ?>
                                    <em>No Action</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-results">No blood requests found.</p>
        <?php endif; ?>
    </section>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle"></h3>
            <p>Are you sure you want to <span id="actionWord"></span> this request?</p>
            <form method="POST" id="actionForm">
                <input type="hidden" name="request_id" id="modalRequestId">
                <input type="hidden" name="action" id="modalAction">
                <div class="modal-buttons">
                    <button type="submit" class="confirm-btn" id="confirmButton"></button>
                    <button type="button" class="cancel-btn" onclick="closeConfirmModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openConfirmModal(requestId, actionType) {
            document.getElementById('confirmModal').style.display = 'flex';
            document.getElementById('modalRequestId').value = requestId;
            document.getElementById('modalAction').value = actionType;

            const actionWord = actionType === 'approve' ? 'approve' : 'reject';
            document.getElementById('actionWord').innerText = actionWord;
            document.getElementById('modalTitle').innerText = actionWord.charAt(0).toUpperCase() + actionWord.slice(1) + " Request";
            document.getElementById('confirmButton').innerText = actionWord.charAt(0).toUpperCase() + actionWord.slice(1);
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }
    </script>

</body>

</html>