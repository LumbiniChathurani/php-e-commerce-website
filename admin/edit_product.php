<?php
include '../components/connect.php';
if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('location:login.php');
    exit(); // Ensure script stops execution after redirection
}

if (isset($_POST['update'])) {
    $p_id = $_POST['product_id'];
    $p_id = filter_var($p_id, FILTER_SANITIZE_STRING);
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $price = $_POST['price'];
    $price = filter_var($price, FILTER_SANITIZE_STRING);
    $detail = $_POST['product_detail'];
    $detail = filter_var($detail, FILTER_SANITIZE_STRING);
    $status = $_POST['status'];
    $status = filter_var($status, FILTER_SANITIZE_STRING);

    if (!empty($_FILES['image']['name'])) {
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_files/' . $image_name;
        move_uploaded_file($image_tmp_name, $image_folder);
        $update_product = $conn->prepare("UPDATE `products` SET name=?, price=?, product_detail=?, status=?, image=? WHERE id=? AND seller_id=?");
        $update_product->execute([$name, $price, $detail, $status, $image_name, $p_id, $seller_id]);
    } else {
        $update_product = $conn->prepare("UPDATE `products` SET name=?, price=?, product_detail=?, status=? WHERE id=? AND seller_id=?");
        $update_product->execute([$name, $price, $detail, $status, $p_id, $seller_id]);
    }

    $success_msg[] = 'Product updated successfully';
}

if (isset($_GET['id'])) {
    $p_id = $_GET['id'];
    $select_product = $conn->prepare("SELECT * FROM `products` WHERE id=? AND seller_id=?");
    $select_product->execute([$p_id, $seller_id]);
    if ($select_product->rowCount() > 0) {
        $fetch_product = $select_product->fetch(PDO::FETCH_ASSOC);
    } else {
        header('location:view_product.php');
        exit();
    }
} else {
    header('location:view_product.php');
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
    <title>FloSun - Edit Product</title>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Edit Product</h1>
            <span><a href="dashboard.php">Admin</a> <i class="bx bx-right-arrow-alt"></i> Edit Product</span>
        </div>
    </div>

    <section style="max-width: 600px; margin: 50px auto; padding: 20px; background: #f9f9f9; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        <div class="heading" style="text-align: center; margin-bottom: 20px;">
            <h1 style="font-size: 24px; color: #333;">Update Product</h1>
            <img src="../image/seperator-removebg-preview (1).png" style="width: 100px;">
        </div>
        <form action="" method="post" enctype="multipart/form-data" style="font-size: 18px;">
            <input type="hidden" name="product_id" value="<?= $fetch_product['id']; ?>">
            <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                <div style="flex: 1; min-width: 250px;">
                    <label style="display: block; font-weight: bold;">Product Name:</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($fetch_product['name']); ?>" required style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                </div>
                <div style="flex: 1; min-width: 250px;">
                    <label style="display: block; font-weight: bold;">Price (Rs.):</label>
                    <input type="text" name="price" value="<?= htmlspecialchars($fetch_product['price']); ?>" required style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                </div>
                <div style="flex: 1; min-width: 250px;">
                    <label style="display: block; font-weight: bold;">Product Detail:</label>
                    <textarea name="product_detail" required style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; height: 80px;"><?= htmlspecialchars($fetch_product['product_detail']); ?></textarea>
                </div>
                <div style="flex: 1; min-width: 250px;">
                    <label style="display: block; font-weight: bold;">Status:</label>
                    <select name="status" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                        <option value="active" <?= $fetch_product['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?= $fetch_product['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            </div>
            <div style="margin-top: 15px;">
                <label style="display: block; font-weight: bold;">Product Image:</label>
                <input type="file" name="image" accept="image/*" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
            </div>
            <div class="flex-btn" style="display: flex; justify-content: space-between; margin-top: 20px;">
                <button type="submit" name="update" class="btn" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">Update</button>
                <a href="view_product.php" class="btn" style="padding: 10px 20px; background: #dc3545; color: white; border-radius: 5px; text-decoration: none; text-align: center;">Cancel</a>
            </div>
        </form>
    </section>


    <?php include '../components/admin_footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>

</html>