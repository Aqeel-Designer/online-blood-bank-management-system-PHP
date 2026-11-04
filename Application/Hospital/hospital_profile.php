<?php
session_start();
/*if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}*/

include "sidebar_header.php";
include "../config.php";

$email = $_SESSION['email'];
$sql = "SELECT username, email, contactno, gender, age, city, bloodtype , hospital
        FROM users 
        WHERE email = '$email'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Recipient Profile</title>
  <link href="https://fonts.googleapis.com/css?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="Recipient/recipient_style.css">
</head>

<body>

  <section>
    <div class="header-container">
      <h1>My Profile Details</h1>
      <a href="edit_hospital_profile.php">
        <button class="edit-btn">Edit Profile</button>
      </a>
    </div>

    <?php if ($user): ?>
      <table class="profile-table">
        <tr>
          <th>Username</th>
          <td><?php echo $user['username']; ?></td>
        </tr>
        <tr>
          <th>Email</th>
          <td><?php echo $user['email']; ?></td>
        </tr>
        <tr>
          <th>Contact Number</th>
          <td><?php echo $user['contactno']; ?></td>
        </tr>
        <tr>
          <th>Gender</th>
          <td><?php echo $user['gender']; ?></td>
        </tr>
        <tr>
          <th>Age</th>
          <td><?php echo $user['age']; ?></td>
        </tr>
        <tr>
          <th>City</th>
          <td><?php echo $user['city']; ?></td>
        </tr>
        <tr>
          <th>Hospital</th>
          <td><?php echo $user['hospital']; ?></td>
        </tr>
        <tr>
          <th>Blood Type</th>
          <td><?php echo $user['bloodtype']; ?></td>
        </tr>
      </table></br>
    <?php else: ?>
      <p class="no-results">No profile data found.</p>
    <?php endif; ?>
  </section>
  </div>
</body>

</html>