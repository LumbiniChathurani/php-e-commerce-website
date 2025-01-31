<?php
include '../components/connect.php';

if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Dashboard</title>
    <!-- box icon cdn link -->
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
</head>

<body>

    <?php include '../components/admin_header.php'; ?>


    <div class="banner">
        <div class="detail">
            <h1>dashboard</h1>
            <p>"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do <br>
                eiusmod tempor incididunt ut labore et dolore magna aliqua."</p>
            <span><a href="dashboard.php">admin</a><i class="bx bx-right-arrow-alt"></i>dashboard</span>
        </div>
    </div>

    <section class="dashboard">
        <div class="heading">
            <h1>dashboard</h1>
            <img src="../image/seperator-removebg-preview (1).png" width="100%" class="heading2">
        </div>
        <div class="box-container">
            <div class="box">
                <h3>welcome !</h3>
                <p><?= $fetch_profile['name']; ?></p>
                <a href="update.php" class="btn">update profile</a>
            </div>
            <div class="box">
                <?php
                $select_message = $conn->prepare("SELECT * FROM `message`");
                $select_message->execute();
                $number_of_msg = $select_message->rowCount();
                ?>
                <h3><?= $number_of_msg; ?></h3>
                <p>unread messages</p>
                <a href="admin_message.php" class="btn">see messages</a>
            </div>
            <div class="box">
                <?php
                $select_product = $conn->prepare("SELECT * FROM `products` WHERE seller_id = ?");
                $select_product->execute([$seller_id]);
                $num_of_product = $select_product->rowCount();
                ?>
                <h3><?= $num_of_product; ?></h3>
                <p>products added</p>
                <a href="add_product.php" class="btn">add new product</a>
            </div>
            <div class="box">
                <?php
                $select_active_product = $conn->prepare("SELECT * FROM `products` WHERE seller_id = ? AND status=?");
                $select_active_product->execute([$seller_id, 'active']);
                $num_of_active_product = $select_active_product->rowCount();
                ?>
                <h3><?= $num_of_active_product; ?></h3>
                <p>total active products</p>
                <a href="view_product.php" class="btn">view active product</a>
            </div>
            <div class="box">
                <?php
                $select_deactive_product = $conn->prepare("SELECT * FROM `products` WHERE seller_id = ? AND status=?");
                $select_deactive_product->execute([$seller_id, 'active']);
                $num_of_deactive_product = $select_deactive_product->rowCount();
                ?>
                <h3><?= $num_of_deactive_product; ?></h3>
                <p>total active products</p>
                <a href="view_product.php" class="btn">view deactive product</a>
            </div>
            <div class="box">
                <?php
                $select_users = $conn->prepare("SELECT * FROM `users`");
                $select_users->execute();
                $num_of_users = $select_users->rowCount();
                ?>
                <h3><?= $num_of_users; ?></h3>
                <p>total registered users</p>
                <a href="user_accounts.php" class="btn">view users</a>
            </div>
        </div>
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