<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: auth.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home - Restaurant Reservation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0d1b2a;
            color: #e0e1dd;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 220px;
            background: #1b263b;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 30px;
            box-shadow: 2px 0 8px rgba(0,0,0,0.5);
        }
        .sidebar h2 {
            color: #00b4d8;
            margin-bottom: 40px;
            text-align: center;
        }
        .sidebar a {
            width: 80%;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #e0e1dd;
            background: #0d1b2a;
            padding: 12px 15px;
            margin: 10px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .sidebar a:hover {
            background: #00b4d8;
            color: #0d1b2a;
        }
        .sidebar a i {
            margin-right: 12px;
            font-size: 18px;
        }

        /* Main content */
        .main-content {
            flex: 1;
            min-height: 100vh;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Top to bottom alignment */
            align-items: center;
        }

        /* Top greeting */
        .greeting {
            text-align: center;
        }
        .greeting h1 {
            font-size: 36px;
            color: #00b4d8;
            margin: 0;
        }
        .greeting h2 {
            font-size: 48px;
            font-weight: bold;
            color: #90e0ef;
            margin: 10px 0 40px 0;
        }

        /* Images */
        .images {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .images img {
            width: 400px;
            height: 300px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.5);
            transition: transform 0.3s;
        }
        .images img:hover {
            transform: scale(1.05);
        }

        /* Quotes */
        .quotes {
            font-style: italic;
            color: #90e0ef;
            margin-top: 50px;
            font-size: 20px;
            text-align: center;
        }

        /* Buttons below sidebar */
        .btn {
            display: inline-block;
            padding: 15px 30px;
            margin: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #0d1b2a;
            background: #00b4d8;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #0077b6;
            color: #e0e1dd;
        }

    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2><?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
    <a href="reserve.php"><i class="fas fa-chair"></i> Reserve Table</a>
    <a href="view_reservations.php"><i class="fas fa-calendar-check"></i> My Reservations</a>
    <a href="menu.php"><i class="fas fa-utensils"></i> View Menu</a>
    <?php if($_SESSION['user_name'] == "admin"){ ?>
        <a href="admin_panel.php"><i class="fas fa-user-cog"></i> Admin Panel</a>
    <?php } ?>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">

    <!-- Top greeting -->
    <div class="greeting">
        <h1>Fork & Feast Restaurant</h1>

        <h3>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h3>
        
    </div>

    <!-- Images -->
    <div class="images">
        <img src="https://www.shutterstock.com/image-photo/served-table-italian-food-seafood-600nw-1678594945.jpg" alt="Restaurant Image 1">
        <img src="https://i.pinimg.com/736x/32/24/24/322424f8095fa9a1e8764f9056b9b3fe.jpg" alt="Restaurant Image 2">
        <img src="https://i.pinimg.com/736x/a3/d5/bd/a3d5bd5a70e6055d2682bf357a9bddf3.jpg" alt="Restaurant Image 3">
    </div>


    <div class="quotes">
        <p>“Where taste meets elegance.”</p>
        <p>“Step into a culinary experience like no other—book now.” 🍴</p>
        
    </div>

</div>

</body>
</html>
