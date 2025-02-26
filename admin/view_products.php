<?php
include '../components/connect.php';
if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('location:login.php');
    exit(); // Ensure script stops execution after redirection
}
if (isset($_POST['delete'])) {
    $p_id = $_POST['product_id'];
    $p_id = filter_var($p_id, FILTER_SANITIZE_STRING);

    $delete_product = $conn->prepare("DELETE FROM `products` WHERE id=?");
    $delete_product->execute([$p_id]);

    $success_msg[] = 'product deleted successfully';
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Boxicons CDN Link -->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
    <title>FloSun - View Products</title>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>View Products</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod<br>
                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam</p>
            <span><a href="dashboard.php">Admin</a> <i class="bx bx-right-arrow-alt"></i> View Products</span>
        </div>
    </div>

    <section class="show_products">
        <div class="heading">
            <h1>Your Products</h1>
            <img src="../image/seperator-removebg-preview (1).png">
        </div>
        <div class="box-container">
            <?php
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE seller_id = ?");
            $select_products->execute([$seller_id]);

            if ($select_products->rowCount() > 0) {
                while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
            ?>
                    <form action="" method="post" class="box">
                        <input type="hidden" name="product_id" value="<?= $fetch_products['id']; ?>">
                        <?php if (!empty($fetch_products['image'])) { ?>
                            <img src="../uploaded_files/<?= $fetch_products['image']; ?>" class="image">
                        <?php } ?>
                        <div class="status" style="color: <?= ($fetch_products['status'] == 'active') ? 'limegreen' : 'red'; ?>;">
                            <?= htmlspecialchars($fetch_products['status']); ?>
                        </div>
                        <p class="price">Rs.<?= htmlspecialchars($fetch_products['price']); ?>/-</p>
                        <div class="content">
                            <div class="title"> <?= htmlspecialchars($fetch_products['name']); ?> </div>
                            <div class="product-detail"> <?= htmlspecialchars($fetch_products['product_detail']); ?> </div>
                            <div class="flex-btn">
                                <a href="edit_product.php?id=<?= $fetch_products['id']; ?>" class="btn">Edit</a>
                                <button type="submit" name="delete" class="btn" onclick="return confirm('Delete this product?');">Delete</button>
                                <!--<a href="view_product.php?post_id=<?= $fetch_products['id']; ?>" class="btn">View Product</a> -->
                            </div>
                        </div>
                    </form>
            <?php
                }
            } else {
                echo '<div class="empty">
                        <p>No products added yet!<br>
                        <a href="add_product.php" class="btn" style="margin-top:1rem;">Add Product</a></p>
                      </div>';
            }
            ?>
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