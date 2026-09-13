<?php
session_start();

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "user_auth";


$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = filter_var($_POST['fullname'], FILTER_SANITIZE_STRING);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $occupation = filter_var($_POST['occupation'], FILTER_SANITIZE_STRING);
    $experience = filter_var($_POST['experience'], FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!$fullname || !$username || !$email || !$password || !$confirm_password) {
        $error = "Please fill in all required fields!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long!";
    } else {

        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error = "Username or email already exists. Please use a different one!";
        } else {
  
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

   
            $stmt = $conn->prepare("INSERT INTO users (fullname, username, email, phone, occupation, experience, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $fullname, $username, $email, $phone, $occupation, $experience, $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Account created successfully! Please login.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Error during registration. Please try again.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Animal Health Information</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="auth.css">
</head>
<body>
    <div class="auth-form-container">
        <h2 class="form-title"><i class="fas fa-user-plus"></i> Create Account</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <label for="fullname">Full Name <span class="required">*</span></label>
                <input type="text" id="fullname" name="fullname" required>
            </div>
            <div class="form-group">
    <label for="username">Username <span class="required">*</span></label>
    <input type="text" id="username" name="username" required>
</div>


            <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" pattern="[0-9]{11}" placeholder="03XXXXXXXXX">
            </div>

            <div class="form-group">
                <label for="occupation">Occupation <span class="required">*</span></label>
                <select id="occupation" name="occupation" required>
                    <option value="">Select your occupation</option>
                    <option value="veterinarian">Veterinarian</option>
                    <option value="farmer">Farmer</option>
                    <option value="pet_owner">Pet Owner</option>
                    <option value="student">Student</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="experience">Experience with Animals</label>
                <select id="experience" name="experience">
                    <option value="">Select your experience level</option>
                    <option value="beginner">Beginner (0-2 years)</option>
                    <option value="intermediate">Intermediate (2-5 years)</option>
                    <option value="advanced">Advanced (5+ years)</option>
                    <option value="professional">Professional</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <input type="password" id="password" name="password" required 
                       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                       title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Sign Up
                </button>
            </div>

            <div class="form-footer">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </form>
    </div>
</body>
</html>
