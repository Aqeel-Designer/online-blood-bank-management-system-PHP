<?php
session_start();

/*if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}*/

include "sidebar_header.php";
include "../config.php";

// Handle Approve, Reject, Deactivate, Reactivate
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $updateQuery = "UPDATE users SET status = 'approved' WHERE id = $user_id";
        $conn->query($updateQuery);
    } elseif ($action === 'reject') {
        $updateQuery = "UPDATE users SET status = 'rejected' WHERE id = $user_id";
        $conn->query($updateQuery);
    } elseif ($action === 'deactivate') {
        $updateQuery = "UPDATE users SET status = 'deactivated' WHERE id = $user_id";
        $conn->query($updateQuery);
    } elseif ($action === 'reactivate') {
        $updateQuery = "UPDATE users SET status = 'approved' WHERE id = $user_id";
        $conn->query($updateQuery);
    }

    header("Location: manage_donors.php");
    exit();
}

// Fetch pending donors
$pending_query = "SELECT id, username, email, city, bloodtype, status 
                  FROM users 
                  WHERE role='donor' AND status='pending'
                  ORDER BY id ASC";
$pending_result = $conn->query($pending_query);

// Fetch approved or deactivated donors
$registered_query = "SELECT id, username, email, city, bloodtype, status 
                     FROM users 
                     WHERE role='donor' AND (status='approved' OR status='deactivated')
                     ORDER BY id ASC";
$registered_result = $conn->query($registered_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Donors - Admin</title>
    <link rel="stylesheet" href="../Recipient/recipient_style.css">
    <link rel="stylesheet" href="admin_style.css">

    <style>
        /* Status color  */
        .status.approved {
            color: green;
            font-weight: 600;
        }

        .status.pending {
            color: orange;
            font-weight: 600;
        }

        .status.rejected {
            color: red;
            font-weight: 600;
        }

        .status.deactivated {
            color: #777;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <section id="pending-donors" style="max-width:1000px;">
        <h2>New Donor Requests</h2>

        <?php if ($pending_result && $pending_result->num_rows > 0): ?>
            <table class="requests-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Location</th>
                        <th>Blood Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $counter = 1;
                    while ($row = $pending_result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo str_pad($counter++, 3, "0", STR_PAD_LEFT); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['city']); ?></td>
                            <td><?php echo htmlspecialchars($row['bloodtype']); ?></td>

                            <td>
                                <span class="status pending">Pending</span>
                            </td>

                            <td>
                                <button class="approve-btn"
                                    onclick="openConfirmModal(<?php echo $row['id']; ?>, 'approve')">
                                    Approve
                                </button>

                                <button class="reject-btn"
                                    onclick="openConfirmModal(<?php echo $row['id']; ?>, 'reject')">
                                    Reject
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php else: ?>
            <p class="no-results">No pending donor registrations.</p>
        <?php endif; ?>
    </section>

    <hr style="margin:40px 0; opacity:0.3;">

    <section id="registered-donors" style="max-width:1000px;">
        <h2>Registered Donors</h2>

        <?php if ($registered_result && $registered_result->num_rows > 0): ?>
            <table class="requests-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Location</th>
                        <th>Blood Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $counter = 1;
                    while ($row = $registered_result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo str_pad($counter++, 3, "0", STR_PAD_LEFT); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['city']); ?></td>
                            <td><?php echo htmlspecialchars($row['bloodtype']); ?></td>

                            <td>
                                <span class="status <?php echo strtolower($row['status']); ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>

                            <td>
                                <?php if ($row['status'] === 'approved'): ?>
                                    <button class="reject-btn"
                                        onclick="openConfirmModal(<?php echo $row['id']; ?>, 'deactivate')">
                                        Deactivate
                                    </button>

                                <?php elseif ($row['status'] === 'deactivated'): ?>
                                    <button class="approve-btn"
                                        onclick="openConfirmModal(<?php echo $row['id']; ?>, 'reactivate')">
                                        Reactivate
                                    </button>

                                <?php else: ?>
                                    <em>No Action</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php else: ?>
            <p class="no-results">No registered donors found.</p>
        <?php endif; ?>
    </section>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle"></h3>
            <p>Are you sure you want to <span id="actionWord"></span> this donor?</p>

            <form method="POST" id="actionForm">
                <input type="hidden" name="user_id" id="modalUserId">
                <input type="hidden" name="action" id="modalAction">

                <div class="modal-buttons">
                    <button type="submit" class="confirm-btn" id="confirmButton"></button>
                    <button type="button" class="cancel-btn" onclick="closeConfirmModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openConfirmModal(userId, actionType) {
            document.getElementById('confirmModal').style.display = 'flex';
            document.getElementById('modalUserId').value = userId;
            document.getElementById('modalAction').value = actionType;

            let word = '';
            if (actionType === 'approve') word = 'approve';
            else if (actionType === 'reject') word = 'reject';
            else if (actionType === 'deactivate') word = 'deactivate';
            else if (actionType === 'reactivate') word = 'reactivate';

            document.getElementById('actionWord').innerText = word;
            document.getElementById('modalTitle').innerText = word.charAt(0).toUpperCase() + word.slice(1);
            document.getElementById('confirmButton').innerText = word.charAt(0).toUpperCase() + word.slice(1);
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }
    </script>

</body>

</html>