<?php
    // DATABASE CONNECTION
    require_once "../server.php";
    // INITIATING ERROR VARIABLES
    $username_error = "";
    $email_error = "";
    $password_error = "";
    $confirm_password_error = "";
    $success = "";
    $error = "";
    // PROCESSING FORM DATA
    if($_SERVER["REQUEST_METHOD"] === "POST") {
        // VALIDATE USERNAME
        $regex_text_pattern = '/^[a-zA-Z0-9]+$/';
        $username = trim($_POST["username"]);
        $sanitized_username = mysqli_real_escape_string($conn, $username);
        if(empty($sanitized_username)) {
            http_response_code(400); // BAD REQUEST
            $username_error = "Please enter a valid username to proceed.";
        } elseif (!preg_match($regex_text_pattern, $sanitized_username)) {
            http_response_code(400); // BAD REQUEST
            $username_error = "Username can only contain letters, numbers, and underscores."; 
        } else {
            // CHECK IF USERNAME ALREADY EXISTS
            $sql = "SELECT username FROM accounts WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $sanitized_username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                http_response_code(409); // CONFLICT
                $error = "Username already in use, please try a different username.";
            } else {
                $sanitized_username = $sanitized_username;
            }
                $stmt->close();
        }
        // VALIDATE EMAIL ADDRESS
        $regex_email_pattern = "/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\\.[a-zA-Z0-9-.]+$/";
        $email = trim($_POST["email"]);
        $sanitized_email = mysqli_real_escape_string($conn, $email);
        if(empty($sanitized_email)) {
            http_response_code(400); // BAD REQUEST
            $email_error = "Please enter a valid email to proceed.";
        } elseif (!preg_match($regex_email_pattern, $sanitized_email)) {
            http_response_code(400); // BAD REQUEST
            $email_error = "You have entered an invalid email address."; 
        } else {
            // CHECK IF EMAIL ALREADY EXISTS
            $sql = "SELECT email FROM accounts WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $sanitized_email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                http_response_code(409); // CONFLICT
                $error = "Email address already in use, please try different email address.";
            } else {
                $sanitized_email = $sanitized_email;
            }
                $stmt->close();
        }
         // VALIDATE PASSWORD
        if (empty(trim($_POST["password"]))) {
            http_response_code(400); // BAD REQUEST
            $password_error = "Please enter a valid password.";     
        } elseif (strlen(trim($_POST["password"])) < 8) {
            http_response_code(400); // BAD REQUEST
            $password_error = "Password must have atleast 8 characters.";
        } else {
            $password = trim($_POST["password"]);
        }
        
        // VALIDATE CONFIRMATION PASSWORD
        if (empty(trim($_POST["confirm_password"]))) {
            http_response_code(400); // BAD REQUEST
            $confirm_password_error = "Please confirm your password.";     
        } else {
            $confirm_password = trim($_POST["confirm_password"]);
            if (empty($password_error) && ($password != $confirm_password)){
                http_response_code(400); // BAD REQUEST
                $confirm_password_error = "Your passwords do not match.";
            }
        }
        // CHECK FOR ERRORS BEFORE PROCESSING FORM DATA
        if (empty($username_error) && empty($email_error) && empty($password_error) && empty($confirm_password_error) && empty($error)) {
            // HASH PASSWORD
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // INSERT NEW USER
            $sql = "INSERT INTO accounts (username, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $sanitized_username, $sanitized_email, $hashed_password);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                http_response_code(201); // CREATED
                $success = "Your account has been created successfully!";
            } else {
                http_response_code(500); // SERVER ERROR
                $error = "An error has occured while trying to create your account.";
            }
                $stmt->close();
        }
        
    }
?>
<?php include("./header.php") ?>
    <div class="container">
        <form id="registerForm" action="" method="POST">
            <h2 class="text-center mb-3">Register</h2>
            <p class="text-center">Please fill in the required details to create an account.</p>
            <div class="form-group mb-3">
                <label for="username" class="mb-1">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Username">
            </div>
            <div class="form-group mb-3">
                <label for="email" class="mb-1">Email</label>
                <input type="email" name="username" id="email" class="form-control" placeholder="Email">
                <small class="form-text text-muted">We will never share your email with third parties</small>
            </div>
            <div class="form-group mb-3">
                <label for="password" class="mb-1">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Password">
            </div>
            <div class="form-group mb-3">
                <label for="password" class="mb-1">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Password">
            </div>
            <button type="submit" name="submit" id="submitButton" class="btn btn-primary btn-block">Register</button>
            <p class="text-center mb-5">Already have an account? <a href="./login.php">Login</a></p>
            <small>By registering you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></small>
        </form>
    </div>
<?php include("./footer.php") ?>