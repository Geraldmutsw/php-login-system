<?php
    // DATABASE CONNECTION
    require_once "../server.php";
    // INITIATING ERROR VARIABLES
    $username_error = "";
    $password_error = "";
    $error = "";
    // PROCESSING FORM DATA
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
            $sanitized_username = $sanitized_username;
        }
        // VALIDATE PASSWORD
        $password = trim($_POST["password"]);
        if (empty($password)) {
            http_response_code(400); // BAD REQUEST
            $password_error = "Please enter a valid password.";     
        } else {
            $password = $password;
        }
        // CHECK FOR ERRORS BEFORE PROCESSING FORM DATA
        if (empty($username_error) && empty($password_error)) {
            // VALIDATE USERNAME AND PASSWORD
            $sql = "SELECT * FROM accounts WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $sanitized_username);
            $stmt->execute();
            $result = $stmt->get_result();
            // CHECK RECORDS IN THE DATABASE/
        if ($result->num_rows === 1) {
            $account = $result->fetch_assoc();
            if (password_verify($password, $account["password"])) {
                // CHECK USER ROLE AND REDIRECT
                if ($account['is_admin'] === 1) {
                    // START A SECURE SESSION
                    session_start([
                        'cookie_lifetime' => 60,
                        'cookie_httponly' => true,
                        'use_strict_mode' => true,
                        'cookie_secure' => true,
                        'cookie_samesite' => 'Lax',
                    ]);
                    // STORE USER ID IN THE SESSION
                    $account_id = $account['account_id'];
                    $_SESSION['account_id'] = $account_id;
                    // ADMIN DASHBOARD
                    header("Location: ../management/");
                } else {
                    // START A SECURE SESSION
                    session_start([
                        'cookie_lifetime' => 60,
                        'cookie_httponly' => true,
                        'use_strict_mode' => true,
                        'cookie_secure' => true,
                        'cookie_samesite' => 'Lax',
                    ]);
                    // STORE USER ID AND USERNAME IN THE SESSION
                    $account_id = $account['account_id'];
                    $_SESSION['account_id'] = $account_id;
                    // USER DASHBOARD
                    header("Location: ../dashboard/");
                }
                exit;
            } else {
                http_response_code(401); // UNAUTHORIZED
                $error = "You have entered an invalid username or password";
            }
        } else {
            http_response_code(404); // NOT FOUND
            $error = "User not found, please make sure your cridentials are correct";
        }
            $stmt->close();
        }
        
    }
?>
<?php include("./header.php") ?>
<?php include("../components/navbar.php") ?>
<?php include("./footer.php") ?>