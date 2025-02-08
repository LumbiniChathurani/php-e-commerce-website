<?php
include '../components/connect.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Get the plain-text password from the form
    $pass = $_POST['pass'];

    // Prepare and execute the query to fetch the user details
    $select_seller = $conn->prepare("SELECT * FROM `sellers` WHERE email = ? LIMIT 1");
    $select_seller->execute([$email]);
    $row = $select_seller->fetch(PDO::FETCH_ASSOC);

    var_dump($pass);
    var_dump($row['password']);

    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
    var_dump($hashed_pass);

    if ($row) { // If email exists
        echo "raw password is: ", $pass, " , hashed: ", $row['password'];
        if (password_verify($pass, $row['password'])) { // Verify password
            echo "password verify ok";
            setcookie('seller_id', $row['id'], time() + 60 * 60 * 24 * 30, '/');
            header('location:dashboard.php');
            exit();
        } else {
            echo "password verify failed";
            $warning_msg[] = 'Incorrect email or password!';
        }
    } else {
        $warning_msg[] = 'Incorrect email or password!';
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- box icon cdn link -->
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
    <title>FloSun - flower website seller login page</title>
</head>

<body>
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data" class="login">
            <h3>Login Now</h3>
            <div class="input-field">
                <p>Your Email <span>*</span></p>
                <input type="email" name="email" placeholder="Enter your email" maxlength="50" required class="box" autocomplete="off">
            </div>

            <div class="input-field">
                <p>Your Password <span>*</span></p>
                <input type="password" name="pass" placeholder="Enter your password" maxlength="50" required class="box" autocomplete="new-password">
            </div>

            <p class="link">Don't have an account? <a href="register.php">Register now</a></p>

            <input type="submit" name="login" class="btn" value="Login Now">
        </form>
    </div>
    <!-- sweet alert cdn link -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <!-- custom js link -->
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <!-- alert -->
    <?php include '../components/alert.php'; ?>
</body>

</html>