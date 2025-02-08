<?php
include '../components/connect.php';

if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('location:login.php');
    exit();
}

if (isset($_POST['update'])) {

    $select_seller = $conn->prepare("SELECT * FROM `sellers` WHERE id = ? LIMIT 1");
    $select_seller->execute([$seller_id]);
    $fetch_seller = $select_seller->fetch(PDO::FETCH_ASSOC);

    $prev_pass = $fetch_seller['password'];
    $prev_image = $fetch_seller['image'];


    $name = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8');

    // Update Name
    if (!empty($name)) {
        $update_name = $conn->prepare("UPDATE `sellers` SET name = ? WHERE id = ?");
        $update_name->execute([$name, $seller_id]);
        $success_msg[] = 'Username updated successfully';
    }


    if (!empty($email)) {
        $select_email = $conn->prepare("SELECT email FROM `sellers` WHERE email = ? AND id != ?");
        $select_email->execute([$email, $seller_id]);

        if ($select_email->rowCount() > 0) {
            $warning_msg[] = 'Email already taken!';
        } else {
            $update_email = $conn->prepare("UPDATE `sellers` SET email = ? WHERE id = ?");
            $update_email->execute([$email, $seller_id]);
            $success_msg[] = 'Email updated successfully';
        }
    }

    // Profile Image Update
    $image = $_FILES['image']['name'] ?? '';
    $image = preg_replace('/[^a-zA-Z0-9._-]/', '', basename($image));

    if (!empty($image)) {
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $rename = uniqid() . '.' . $ext;
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_files/' . $rename;

        if ($image_size > 2000000) {
            $warning_msg[] = 'Image size is too large';
        } else {
            $update_image = $conn->prepare("UPDATE `sellers` SET `image` = ? WHERE id = ?");
            $update_image->execute([$rename, $seller_id]);
            move_uploaded_file($image_tmp_name, $image_folder);

            // Delete old image if exists
            if (!empty($prev_image) && $prev_image != $rename) {
                unlink('../uploaded_files/' . $prev_image);
            }
            $success_msg[] = 'Image updated successfully';
        }
    }


    $old_pass = $_POST['old_pass'] ?? '';

    if (!password_verify($old_pass, $prev_pass)) {
        $warning_msg[] = 'Old password not matched';
    } else {
        $new_pass = $_POST['new_pass'] ?? '';
        $cpass = $_POST['cpass'] ?? '';

        if ($new_pass !== $cpass) {
            $warning_msg[] = 'Confirm password does not match';
        } else {
            $hashed_new_pass = password_hash($new_pass, PASSWORD_BCRYPT);
            $update_pass = $conn->prepare("UPDATE `sellers` SET password = ? WHERE id = ?");
            $update_pass->execute([$hashed_new_pass, $seller_id]);
            $success_msg[] = 'Password updated successfully';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FloSun - flower website seller update profile page</title>
    <!-- box icon cdn link -->
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
</head>

<body>

    <?php include '../components/admin_header.php'; ?>


    <div class="banner">
        <div class="detail">
            <h1>update profile</h1>
            <p>"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do <br>
                eiusmod tempor incididunt ut labore et dolore magna aliqua."</p>
            <span><a href="dashboard.php">admin</a><i class="bx bx-right-arrow-alt"></i>update profile</span>
        </div>
    </div>

    <section class="form-container">
        <form action="" method="post" enctype="multipart/form-data" class="register">
            <div class="img-box">
                <img src="../uploaded_files/<?= $fetch_profile['image']; ?>">
            </div>
            <h3>update profile</h3>
            <div class="flex">
                <div class="col">
                    <div class="input-field">
                        <p>your name</p>
                        <input type="text" name="name" placeholder="<?= $fetch_profile['name']; ?>" class="box">
                    </div>
                    <div class="input-field">
                        <p>your email</p>
                        <input type="email" name="email" placeholder="<?= $fetch_profile['email']; ?>" class="box">
                    </div>
                    <div class="input-field">
                        <p>update profile</p>
                        <input type="file" name="image" accept="image/*" class="box">
                    </div>
                </div>
                <div class="col">
                    <div class="input-field">
                        <p>old password</p>
                        <input type="password" name="old_pass" placeholder="enter your old password" class="box">
                    </div>
                    <div class="input-field">
                        <p>new password</p>
                        <input type="password" name="new_pass" placeholder="enter your new password" class="box">
                    </div>
                    <div class="input-field">
                        <p>confirm password</p>
                        <input type="password" name="cpass" placeholder="confirm password" class="box">
                    </div>
                </div>
            </div>
            <input type="submit" name="update" class="btn" value="update profile">
        </form>
    </section>




    <?php include '../components/admin_footer.php'; ?>

    <!-- sweetalert cdn link -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <!-- custom js link -->
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <!-- alert -->
    <?php include '../components/alert.php'; ?>
</body>

</html>