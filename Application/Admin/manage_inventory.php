<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

include "sidebar_header.php";
include "../config.php";

/* Add Inventory */
if (isset($_POST['action']) && $_POST['action'] === "add_inventory") {
    $bloodtype = $_POST['bloodtype'];
    $location = $_POST['location'];
    $units = intval($_POST['units']);

    // Check if row exists
    $check = $conn->query("SELECT * FROM blood_inventory WHERE bloodtype='$bloodtype' AND location='$location'");

    if ($check->num_rows > 0) {
        // Update existing row
        $conn->query("UPDATE blood_inventory 
                      SET units_available = units_available + $units 
                      WHERE bloodtype='$bloodtype' AND location='$location'");
    } else {
        // Insert new row
        $conn->query("INSERT INTO blood_inventory (bloodtype, units_available, location) 
                      VALUES ('$bloodtype', '$units', '$location')");
    }

    header("Location: manage_inventory.php");
    exit();
}

/* Add/Remove from Inventory */
if (isset($_POST['action']) && $_POST['action'] === "update_units") {
    $id = $_POST['id'];
    $change = intval($_POST['change']);
    $operation = $_POST['operation'];

    if ($operation === "add") {
        $conn->query("UPDATE blood_inventory 
                      SET units_available = units_available + $change 
                      WHERE id=$id");
    } elseif ($operation === "remove") {
        $conn->query("UPDATE blood_inventory 
                      SET units_available = GREATEST(units_available - $change, 0) 
                      WHERE id=$id");
    }

    header("Location: manage_inventory.php");
    exit();
}

/* Delete Inventory entry */
if (isset($_POST['action']) && $_POST['action'] === "delete_inventory_entry") {
    $id = $_POST['id'];

    $conn->query("DELETE FROM blood_inventory WHERE id=$id");

    header("Location: manage_inventory.php");
    exit();
}


/* Fetch Inventory */
$result = $conn->query("SELECT * FROM blood_inventory ORDER BY bloodtype ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inventory - Admin</title>
    <link rel="stylesheet" href="../Recipient/recipient_style.css">
    <link rel="stylesheet" href="admin_style.css">
</head>

<body>

    <section id="inventory" style="max-width: 900px;">
        <h2>Manage Blood Inventory</h2>

        <!-- Add Inventory Form -->
        <form method="POST" class="add-form">
            <input type="hidden" name="action" value="add_inventory">

            <label>Blood Type:</label>
            <select name="bloodtype" required>
                <option value="">Select</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select>

            <label>Units:</label>
            <input type="number" name="units" min="1" required>

            <label>Location:</label>
            <input type="text" name="location" required>



            <button type="submit" class="reject-btn">Add Units</button>
        </form>

        <hr>

        <!-- Inventory Table -->
        <?php if ($result->num_rows > 0): ?>
            <table class="requests-table">
                <thead>
                    <tr>
                        <th>Blood Type</th>
                        <th>Location</th>
                        <th>Units Available</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['bloodtype']; ?></td>
                            <td><?php echo $row['location']; ?></td>
                            <td><?php echo $row['units_available']; ?></td>
                            <td>
                                <button class="approve-btn" onclick="openModal(<?php echo $row['id']; ?>, 'add')">+</button>
                                <button class="reject-btn" style="background-color: grey;" onclick="openModal(<?php echo $row['id']; ?>, 'remove')">-</button>
                                <button class="reject-btn" onclick="openDeleteModal(<?php echo $row['id']; ?>)">Delete</button>
                            </td>


                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php else: ?>
            <p class="no-results">No inventory available.</p>
        <?php endif; ?>

        <form action="export_blood_inventory_csv.php" method="post" style="display:inline-block;">
            <div><button type="submit" class="download-btn">Download Inventory Report (CSV)</button></div>
        </form>


    </section>

    <!-- Modal -->
    <div id="unitModal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle"></h3>
            <form method="POST">
                <input type="hidden" name="action" value="update_units">
                <input type="hidden" name="id" id="modalId">
                <input type="hidden" name="operation" id="modalOperation">

                <label>Units:</label>
                <input type="number" name="change" min="1" required>

                <div class="modal-buttons">
                    <button type="submit" class="confirm-btn" id="confirmButton"></button>
                    <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Delete Inventory Entry</h3>
            <p>Are you sure you want to delete this entire entry?</p>

            <form method="POST">
                <input type="hidden" name="action" value="delete_inventory_entry">
                <input type="hidden" name="id" id="deleteEntryId">

                <div class="modal-buttons">
                    <button type="submit" class="confirm-btn" style="background:#c82333;">Delete</button>
                    <button type="button" class="cancel-btn" onclick="closeDeleteModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        function openModal(id, operation) {
            document.getElementById("unitModal").style.display = "flex";
            document.getElementById("modalId").value = id;
            document.getElementById("modalOperation").value = operation;

            if (operation === "add") {
                document.getElementById("modalTitle").innerText = "Add Units";
                document.getElementById("confirmButton").innerText = "Add";
            } else {
                document.getElementById("modalTitle").innerText = "Remove Units";
                document.getElementById("confirmButton").innerText = "Remove";
            }
        }

        function closeModal() {
            document.getElementById("unitModal").style.display = "none";
        }

        function openDeleteModal(id) {
            document.getElementById("deleteModal").style.display = "flex";
            document.getElementById("deleteEntryId").value = id;
        }

        function closeDeleteModal() {
            document.getElementById("deleteModal").style.display = "none";
        }
    </script>

</body>

</html>