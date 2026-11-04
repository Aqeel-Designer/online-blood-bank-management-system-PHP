<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

include "sidebar_header.php";
include "../config.php";

// Handle Approve, Reject, or Donated Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['donation_id'])) {
    $donation_id = $_POST['donation_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $updateQuery = "UPDATE donation_requests SET status = 'approved' WHERE donation_id = $donation_id";
        $conn->query($updateQuery);
    } elseif ($action === 'reject') {
        $updateQuery = "UPDATE donation_requests SET status = 'rejected' WHERE donation_id = $donation_id";
        $conn->query($updateQuery);
    } elseif ($action === 'donated') {
        // Update status to donated
        $updateQuery = "UPDATE donation_requests SET status = 'donated' WHERE donation_id = $donation_id";
        $conn->query($updateQuery);

        // Fetch record details to insert into donation_history
        $fetchQuery = "SELECT username, donor_email, bloodtype, unitsdonated, location, donationdate 
                       FROM donation_requests 
                       WHERE donation_id = $donation_id";
        $result = $conn->query($fetchQuery);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Insert into donation_history
            $insertQuery = "INSERT INTO donation_history (username, donor_email, bloodtype, unitsdonated, location, donationdate, recorded_at)
                            VALUES (
                                '{$row['username']}',
                                '{$row['donor_email']}',
                                '{$row['bloodtype']}',
                                '{$row['unitsdonated']}',
                                '{$row['location']}',
                                '{$row['donationdate']}',
                                NOW()
                            )";
            $conn->query($insertQuery);

            //  Update Blood Inventory
            $blood = $row['bloodtype'];
            $units = (int)$row['unitsdonated'];
            $location = $row['location'];

            // Check if matching blood+location exists
            $checkInv = "SELECT id, units_available FROM blood_inventory 
                     WHERE bloodtype = '$blood' AND location = '$location' LIMIT 1";
            $invResult = $conn->query($checkInv);

            if ($invResult && $invResult->num_rows > 0) {
                // Update existing row
                $invRow = $invResult->fetch_assoc();
                $newUnits = $invRow['units_available'] + $units;

                $updateInv = "UPDATE blood_inventory 
                          SET units_available = $newUnits 
                          WHERE id = {$invRow['id']}";
                $conn->query($updateInv);
            } else {
                // Insert new row
                $addInv = "INSERT INTO blood_inventory (bloodtype, units_available, location)
                       VALUES ('$blood', $units, '$location')";
                $conn->query($addInv);
            }
        }
    }

    header("Location: manage_donation_requests.php");
    exit();
}

// Fetch all donation requests
$sql = "SELECT username, donation_id, donor_email, bloodtype, unitsdonated, location, donationdate, requested_at, status 
        FROM donation_requests 
        ORDER BY donation_id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Donating Requests - Admin</title>
    <link rel="stylesheet" href="../Recipient/recipient_style.css">
    <link rel="stylesheet" href="admin_style.css">
</head>

<body>

    <section id="manage-requests" style="max-width: 1100px;">
        <h2>Manage Donating Requests</h2>

        <?php if ($result && $result->num_rows > 0): ?>
            <table class="requests-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Blood Type</th>
                        <th>Units</th>
                        <th>Location</th>
                        <th>Donation Date</th>
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
                            <td><?php echo ucfirst($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['donor_email']); ?></td>
                            <td><?php echo htmlspecialchars($row['bloodtype']); ?></td>
                            <td><?php echo htmlspecialchars($row['unitsdonated']); ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td><?php echo htmlspecialchars($row['donationdate']); ?></td>
                            <td><?php echo htmlspecialchars($row['requested_at']); ?></td>
                            <td>
                                <span class="status <?php echo strtolower($row['status']); ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['status'] === 'pending'): ?>
                                    <button class="approve-btn" onclick="openConfirmModal(<?php echo $row['donation_id']; ?>, 'approve')">Approve</button>
                                    <button class="reject-btn" onclick="openConfirmModal(<?php echo $row['donation_id']; ?>, 'reject')">Reject</button>
                                    <button class="donated-btn" onclick="openConfirmModal(<?php echo $row['donation_id']; ?>, 'donated')">Donated</button>
                                <?php elseif ($row['status'] === 'approved'): ?>
                                    <button class="donated-btn" style="margin-bottom: 0px;" onclick="openConfirmModal(<?php echo $row['donation_id']; ?>, 'donated')">Donated</button>
                                <?php else: ?>
                                    <em>No Action</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-results">No donation requests found.</p>
        <?php endif; ?>
    </section>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle"></h3>
            <p>Are you sure you want to <span id="actionWord"></span> this request?</p>
            <form method="POST" id="actionForm">
                <input type="hidden" name="donation_id" id="modalDonationId">
                <input type="hidden" name="action" id="modalAction">
                <div class="modal-buttons">
                    <button type="submit" class="confirm-btn" id="confirmButton"></button>
                    <button type="button" class="cancel-btn" onclick="closeConfirmModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openConfirmModal(donationId, actionType) {
            document.getElementById('confirmModal').style.display = 'flex';
            document.getElementById('modalDonationId').value = donationId;
            document.getElementById('modalAction').value = actionType;

            let actionWord = '';
            if (actionType === 'approve') actionWord = 'approve';
            else if (actionType === 'reject') actionWord = 'reject';
            else if (actionType === 'donated') actionWord = 'mark as donated';

            document.getElementById('actionWord').innerText = actionWord;
            document.getElementById('modalTitle').innerText = actionWord.charAt(0).toUpperCase() + actionWord.slice(1);
            document.getElementById('confirmButton').innerText = actionWord.charAt(0).toUpperCase() + actionWord.slice(1);
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }
    </script>

</body>

</html>