<?php 
    if (!isset($_SESSION)) {
        session_start();
    }
    if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > 60) {
        session_destroy();
        header("Location: index.php");
    }
    include("./header.php");
    // COMPONENTS
    include("./footer.php");
    $_SESSION['last_activity'] = time();
?>