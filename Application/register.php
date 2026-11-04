<?php

include("config.php");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])){
    $username   = $_POST['username'];
    $role       = $_POST['role'];
    $email      = $_POST['email'];
    $password   = $_POST['password'];
    $contactno  = $_POST['contact'];
    $gender     = $_POST['gender'];
    $age        = $_POST['age'];
    $city       = $_POST['city'];
    $hospital   = $_POST['hospital'];
    $bloodtype  = $_POST['bloodtype'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $checkEmail = $conn->query("SELECT email FROM users WHERE email='$email'");
    if ($checkEmail->num_rows > 0){
        $error = "Email is already registered";
    }
    else{
        // If registering as DONOR --> set account to PENDING
        if ($role === "donor") {

            $conn->query("INSERT INTO users 
                (username, role, email, password, contactno, gender, age, city, hospital, bloodtype, status) 
                VALUES 
                ('$username', '$role', '$email', '$hashed_password', '$contactno', '$gender', '$age', '$city', '$hospital', '$bloodtype', 'pending')");

            $success = "donor_pending";
        }
        else {
            // For recipient & hospital → directly active
            $conn->query("INSERT INTO users 
                (username, role, email, password, contactno, gender, age, city, hospital, bloodtype, status) 
                VALUES 
                ('$username', '$role', '$email', '$hashed_password', '$contactno', '$gender', '$age', '$city', '$hospital', '$bloodtype', 'active')");

            $success = "normal";
        }
    }
}

mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Form</title>
    <link rel="stylesheet" href="login_register_style.css">
</head>
<body>
    <div class="container">
        <div class="form-box" id="register-form">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                
                <h2>Register</h2>

                <!-- Show error -->
                <?php if (!empty($error)) { ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php } ?>

                <!-- Donor success popup -->
                <?php 
                if ($success === "donor_pending") {
                    echo '<script>
                            alert("Registration successful! Your account is pending admin approval.");
                            window.location="login.php";
                          </script>';
                }

                if ($success === "normal") {
                    echo '<script>
                            alert("Registration successful! You can now log in.");
                            window.location="login.php";
                          </script>';
                }
                ?>

                <input type="text" name="username" placeholder="Enter Username" required>

                <select name="role" required>
                    <option value="">--Role--</option>
                    <option value="donor">Donor</option>
                    <option value="recipient">Recipient</option>
                    <option value="hospital">Hospital Representative</option>
                </select>

                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="tel" name="contact" placeholder="Enter contact number" required>

                <select name="gender" required>
                    <option value="">--Gender--</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>

                <input type="number" name="age" placeholder="Enter Your Age" min="18" max="100" required>
                <input type="text" name="city" placeholder="Enter Your City" required>
                <input type="text" name="hospital" placeholder="Enter Hospital Name (hospitals only)">
                
                <select name="bloodtype" required>
                    <option value="">--Blood Type--</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>

                <button type="submit" name="register">Register</button>
                <p>Already have an account? <a href="login.php">Login</a></p>
            </form>
        </div>
    </div>
</body>
</html>
