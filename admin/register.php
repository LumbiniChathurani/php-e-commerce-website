<?php
include '../components/connect.php';

if (isset($_POST['register'])) {
    $id = unique_id();
    $name = $_POST['name'];
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Get the plain-text passwords
    $pass = $_POST['pass'];
    $cpass = $_POST['cpass'];

    // Compare the passwords before hashing
    if ($pass !== $cpass) {
        $warning_msg[] = 'Confirm password does not match';
    } else {
        // If passwords match, hash the password
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

        $image = $_FILES['image']['name'];
        $image = htmlspecialchars($image, ENT_QUOTES, 'UTF-8');
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $rename = unique_id() . '.' . $ext;
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_files/' . $rename;

        // Check if email already exists
        $select_seller = $conn->prepare("SELECT * FROM `sellers` WHERE email=?");
        $select_seller->execute([$email]);

        if ($select_seller->rowCount() > 0) {
            $warning_msg[] = 'Email already exists';
        } else {
            // Insert the new seller into the database
            $insert_seller = $conn->prepare("INSERT INTO `sellers`(id, name, email, password, image) VALUES(?,?,?,?,?)");
            $insert_seller->execute([$id, $name, $email, $hashed_pass, $rename]);

            // Move the uploaded image to the folder
            move_uploaded_file($image_tmp_name, $image_folder);

            // Success message
            $success_msg[] = 'New seller registered! Please login now';
        }
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
    <title>FloSun - flower website seller registration page</title>
</head>

<body>
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data" class="register">
            <h3>register now</h3>
            <div class="flex">
                <div class="col">
                    <div class="input-field">
                        <p>your name <span>*</span></p>
                        <input type="text" name="name" placeholder="enter your name" maxlength="50" required class="box">
                    </div>
                    <div class="input-field">
                        <p>your email <span>*</span></p>
                        <input type="email" name="email" placeholder="enter your email" maxlength="50" required class="box" autocomplete="off">
                    </div>
                </div>
                <div class="col">
                    <div class="input-field">
                        <p>your password <span>*</span></p>
                        <input type="password" name="pass" placeholder="enter your password" maxlength="50" required class="box" autocomplete="new-password">
                    </div>
                    <div class="input-field">
                        <p>confirm password <span>*</span></p>
                        <input type="password" name="cpass" placeholder="enter your password" maxlength="50" required class="box" autocomplete="new-password">
                    </div>
                </div>
            </div>
            <div class="input-field">
                <p>select profile <span>*</span></p>
                <input type="file" name="image" accept="image/*" required class="box">
            </div>
            <p class="link">already have an account ? <a href="login.php">login now</a></p>
            <input type="submit" name="register" class="btn" value="register now">
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