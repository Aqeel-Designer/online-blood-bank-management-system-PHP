<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

include "sidebar_header.php";
include "../config.php";

$email = $_SESSION['email'];

// Fetch username
$userQuery = "SELECT username FROM users WHERE email='$email'";
$userResult = $conn->query($userQuery);

if ($userResult && $userResult->num_rows > 0) {
    $username = $userResult->fetch_assoc()['username'];
} else {
    $username = "User";
}

/* Total Blood Inventory */
$inventoryQuery = "
    SELECT bloodtype, SUM(units_available) AS total_units 
    FROM blood_inventory 
    GROUP BY bloodtype
";
$inventoryResult = $conn->query($inventoryQuery);

/*  Pending Requests  */
$pendingDonorRegs      = $conn->query("SELECT COUNT(*) AS total FROM users WHERE status = 'pending'")->fetch_assoc()['total'];
$pendingDonorRequests  = $conn->query("SELECT COUNT(*) AS total FROM donation_requests WHERE status = 'pending'")->fetch_assoc()['total'];
$pendingRecipientReqs  = $conn->query("SELECT COUNT(*) AS total FROM blood_requests WHERE status = 'pending'")->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../Recipient/recipient_style.css">
    <link rel="stylesheet" href="admin_stlye.css">

    <style>
        /* Inventory Grid  */
        .inventory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }

        .inventory-box {
            background: #ffe3eeff;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: rgba(0, 0, 0, 0.08) 0 2px 6px;
        }

        .inventory-box h3 {
            margin: 0;
            font-size: 22px;
            color: crimson;
        }

        .inventory-box p {
            font-size: 18px;
            margin-top: 10px;
            color: #444;
        }

        /*  Pending Requests Grid  */
        .pending-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }

        .pending-box {
            background: #ffe3eeff;
            padding: 22px;
            border-radius: 12px;
            text-align: center;
            box-shadow: rgba(0, 0, 0, 0.08) 0 2px 6px;
        }

        .pending-box h3 {
            color: #333;
            margin-bottom: 8px;
            font-size: 20px;
        }

        .pending-box p {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .manage-btn {
            background: crimson;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.2s ease;
        }

        .manage-btn:hover {
            background: #b3102d;
        }
    </style>

    <title>Admin Dashboard</title>
</head>

<body>



    <section id="dashboard">
        <h2>Hello <?php echo htmlspecialchars($username); ?>, Welcome to the Admin Dashboard</h2>
        <p style="text-align: center; font-size:17px;">Use the sidebar to navigate.</p>
    </section>

    <!-- Blood Inventory Overview -->
    <section id="dasboard">
        <h2>Blood Inventory Overview</h2>
        <div class="inventory-grid">

            <?php
            if ($inventoryResult && $inventoryResult->num_rows > 0) {
                while ($row = $inventoryResult->fetch_assoc()) {
                    echo "
                        <div class='inventory-box'>
                            <h3>{$row['bloodtype']}</h3>
                            <p>Total Units: <b>{$row['total_units']}</b></p>
                        </div>
                    ";
                }
            } else {
                echo "<p style='text-align:center;'>No inventory records found.</p>";
            }
            ?>

        </div>
    </section>

    <!-- Pending Requests -->
    <section id="dasboard">
        <h2>Pending Requests</h2>

        <div class="pending-grid">

            <div class="pending-box">
                <h3>Pending Donor Registrations</h3>
                <p><?php echo $pendingDonorRegs; ?></p>
                <a href="manage_donors.php" class="manage-btn">Manage</a>
            </div>

            <div class="pending-box">
                <h3>Pending Donor Requests</h3>
                <p><?php echo $pendingDonorRequests; ?></p>
                <a href="manage_donation_requests.php" class="manage-btn">Manage</a>
            </div>

            <div class="pending-box">
                <h3>Pending Recipient Requests</h3>
                <p><?php echo $pendingRecipientReqs; ?></p>
                <a href="manage_receiving_requests.php" class="manage-btn">Manage</a>
            </div>

        </div>
    </section>



</body>

</html>