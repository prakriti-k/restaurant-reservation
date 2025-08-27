<?php
session_start();
require "db.php";
if(!isset($_SESSION['user_id'])){
    header("Location: auth.php");
    exit();
}

// Example menu items
$menu_items = [];
$sql= "SELECT name, price, image, description FROM menu_items";
$result=$conn->query($sql);
if ($result->num_rows>0){
    while($row=$result->fetch_assoc()){
        $menu_items[]=$row;
    }
}

// Sorting
if (isset($_GET['sort'])) {
    if ($_GET['sort'] == 'asc') {
        usort($menu_items, fn($a, $b) => $a['price'] <=> $b['price']);
    } elseif ($_GET['sort'] == 'desc') {
        usort($menu_items, fn($a, $b) => $b['price'] <=> $a['price']);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Restaurant Menu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background: url('https://i.pinimg.com/736x/17/5c/19/175c19f891f4d76b61f5a7dac83ff804.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #e0e1dd;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(13, 27, 42, 0.5);
            z-index: 0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            position: sticky;
            top: 0;
            background: rgba(13,27,42,0.8);
            z-index: 2;
        }
        .back-btn {
            color: #00b4d8;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
        }
        .back-btn i {
            margin-right: 8px;
        }
        .sort-box select {
            padding: 5px;
            border-radius: 6px;
            border: none;
            background: #00b4d8;
            color: #0d1b2a;
            font-weight: bold;
        }
        .menu-container {
            position: relative;
            z-index: 1;
            max-width: 900px;
            margin: auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #00b4d8;
            margin-bottom: 30px;
        }
        .menu-item {
            display: flex;
            background: rgba(27, 38, 59, 0.95);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.5);
            margin-bottom: 30px;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
        }
        .menu-item img {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #00b4d8;
            flex-shrink: 0;
        }
        .menu-info {
            padding: 15px 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(27, 38, 59, 0.85);
            border-radius: 12px;
            margin-left: 20px;
            flex: 1;
        }
        .menu-info h3 {
            margin: 0;
            color: #90e0ef;
        }
        .menu-info p {
            font-size: 14px;
            color: #e0e1dd;
            margin: 10px 0;
        }
        .price {
            font-weight: bold;
            color: #00b4d8;
            font-size: 16px;
        }
        .order-btn-container {
            padding: 15px 20px;
            display: flex;
            align-items: center;
        }
        .order-btn {
            padding: 10px 15px;
            background-color: #00b4d8;
            color: #0d1b2a;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .order-btn:hover {
            background-color: #0077b6;
            color: #e0e1dd;
        }
        @media screen and (max-width: 600px) {
            .menu-item {
                flex-direction: column;
                text-align: center;
            }
            .menu-item img {
                width: 200px;
                height: 200px;
            }
            .menu-info {
                margin-left: 0;
                margin-top: 15px;
            }
            .order-btn-container {
                margin-top: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="javascript:history.back()" class="back-btn"><i class="fas fa-arrow-left"></i> </a>
        <div class="sort-box">
            <form method="get">
                <select name="sort" onchange="this.form.submit()">
                    <option value="">Sort by Price</option>
                    <option value="asc" <?php if(isset($_GET['sort']) && $_GET['sort']=='asc') echo 'selected'; ?>>Low to High</option>
                    <option value="desc" <?php if(isset($_GET['sort']) && $_GET['sort']=='desc') echo 'selected'; ?>>High to Low</option>
                </select>
            </form>
        </div>
    </div>

    <div class="menu-container">
        <h1>Our Menu</h1>
        <?php foreach($menu_items as $item): ?>
            <div class="menu-item">
                <img src="<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                <div class="menu-info">
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                    <div class="price">Rs. <?php echo number_format($item['price']); ?></div>
                </div>
                <div class="order-btn-container">
                    <a href="order.php?item=<?php echo urlencode($item['name']); ?>&price=<?php echo $item['price']; ?>" class="order-btn">Order Now</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
