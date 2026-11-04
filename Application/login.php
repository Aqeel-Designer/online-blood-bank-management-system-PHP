<?php

session_start();
include("config.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $row['password'])) {

            // If donor → check approval status
            if ($row['role'] === 'donor') {

                if ($row['status'] === 'pending') {
                    $error = "Your account is pending approval. Please wait for admin approval.";
                } 
                elseif ($row['status'] === 'rejected') {
                    $error = "Your registration was rejected by admin.";
                } 
                elseif ($row['status'] === 'deactivated') {
                    $error = "Your account has been deactivated";
                }
                else {
                    // Approved donor
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['email'] = $row['email'];
                    header("Location: Donor/donor_dashboard.php");
                    exit();
                }
            }
            else {
                // Normal login for recipients, hospitals, admin
                $_SESSION['username'] = $row['username'];
                $_SESSION['email'] = $row['email'];

                if ($row['role'] == 'admin') {
                    header("Location: Admin/admin_dashboard.php");
                    exit();
                }
                elseif ($row['role'] == 'recipient') {
                    header("Location: Recipient/recipient_dashboard.php");
                    exit();
                }
                elseif ($row['role'] == 'hospital') {
                    header("Location: Hospital/hospital_dashboard.php");
                    exit();
                }
            }
        } 
        else {
            $error = "Incorrect email or password!";
        }
    } 
    else {
        $error = "Incorrect email or password!";
    }
}

mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="login_register_style.css">
</head>
<body>
    <div class="container">
        <div class="form-box" id="login-form">

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                <h2>Login</h2>

                <?php if (!empty($error)) { ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php } ?>

                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>

                <button type="submit" name="login">Login</button>

                <p>Don't have an account? <a href="register.php">Register</a></p>

            </form>

        </div>
    </div>
</body>
</html>
