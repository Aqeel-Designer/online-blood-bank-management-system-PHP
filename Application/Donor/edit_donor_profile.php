<?php
session_start();

/*if (!isset($_SESSION['email'])) {
    header("Location: ../login.php"); 
    exit();
}*/

include "sidebar_header.php";
include '../config.php';

$email = $_SESSION['email'] ?? null;

if (!$email) {
  die("You are not logged in.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {
  $username = $_POST['username'];
  $contactno = $_POST['contactno'];
  $gender = $_POST['gender'];
  $age = $_POST['age'];
  $city = $_POST['city'];
  $bloodtype = $_POST['bloodtype'];

  $sql = "UPDATE users 
            SET username='$username', contactno='$contactno', gender='$gender', age='$age', city='$city', bloodtype='$bloodtype' 
            WHERE email='$email'";

  if ($conn->query($sql) === TRUE) {
    header("Location: donor_profile.php");
    exit();
  } else {
    echo "Error updating profile: " . $conn->error;
  }
}

$sql = "SELECT username, email, contactno, gender, age, city, bloodtype 
        FROM users 
        WHERE email='$email'";

$result = $conn->query($sql);
$user = $result->fetch_assoc();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Edit Profile</title>
  <link href="https://fonts.googleapis.com/css?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="recipient_style.css">
  <style>
    .edit-container {
      width: 90%;
      max-width: 700px;
      margin: 30px auto;
      background: #fff;
      padding: 22px;
      border-radius: 8px;
      box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
      font-family: "Poppins", Arial, sans-serif;
    }

    .edit-container h1 {
      text-align: center;
      color: crimson;
      margin-bottom: 18px;
    }

    form {
      display: grid;
      gap: 14px;
    }

    label {
      font-weight: 600;
      color: #444;
    }

    input,
    select {
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ddd;
      background: #f9f9f9;
      font-size: 15px;
      width: 100%;
    }

    input:disabled {
      background: #eee;
      color: #666;
    }

    .button-group {
      display: flex;
      justify-content: center;
      gap: 12px;
      margin-top: 12px;
    }

    button {
      display: inline-block;
      padding: 10px 20px;
      background: crimson;
      border-radius: 6px;
      border: none;
      cursor: pointer;
      font-size: 16px;
      color: white;
      font-weight: 500;
      transition: 0.3s;
    }

    button:hover {
      background: rgb(253, 80, 80);
    }

    .cancel-btn {
      background: gray;
    }

    .cancel-btn:hover {
      background: #555;
    }
  </style>
</head>

<body>
  <div class="edit-container">
    <h1>Edit Profile</h1>
    <form method="POST">
      <label>Username</label>
      <input type="text" name="username" value="<?php echo $user['username']; ?>" required>

      <label>Email</label>
      <input type="text" name="email" value="<?php echo $user['email']; ?>" disabled>

      <label>Contact No</label>
      <input type="text" name="contactno" value="<?php echo $user['contactno']; ?>">

      <label>Gender</label>
      <select name="gender" required>
        <option value="Male" <?php if ($user['gender'] == "Male") echo "selected"; ?>>Male</option>
        <option value="Female" <?php if ($user['gender'] == "Female") echo "selected"; ?>>Female</option>
      </select>

      <label>Age</label>
      <input type="number" name="age" value="<?php echo $user['age']; ?>">

      <label>City</label>
      <input type="text" name="city" value="<?php echo $user['city']; ?>">

      <label>Blood Type</label>
      <select name="bloodtype" required>
        <option value="A+" <?php if ($user['bloodtype'] == "A+") echo "selected"; ?>>A+</option>
        <option value="A-" <?php if ($user['bloodtype'] == "A-") echo "selected"; ?>>A-</option>
        <option value="B+" <?php if ($user['bloodtype'] == "B+") echo "selected"; ?>>B+</option>
        <option value="B-" <?php if ($user['bloodtype'] == "B-") echo "selected"; ?>>B-</option>
        <option value="AB+" <?php if ($user['bloodtype'] == "AB+") echo "selected"; ?>>AB+</option>
        <option value="AB-" <?php if ($user['bloodtype'] == "AB-") echo "selected"; ?>>AB-</option>
        <option value="O+" <?php if ($user['bloodtype'] == "O+") echo "selected"; ?>>O+</option>
        <option value="O-" <?php if ($user['bloodtype'] == "O-") echo "selected"; ?>>O-</option>
      </select>

      <div class="button-group">
        <button type="submit" name="update">Save Changes</button>
        <a href="recipient_profile.php"><button type="button" class="cancel-btn">Cancel</button></a>
      </div>
    </form>
  </div>
</body>

</html>