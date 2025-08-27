<?php
session_start();
require 'db.php';

// Admin email allowed
$admin_email = "admin@example.com";

// Check if logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email']) || $_SESSION['user_email'] != $admin_email) {
    header("Location: auth.php");
    exit();
}

// Handle menu actions: create, update, delete
if (isset($_POST['create_menu'])) {
    $name = $_POST['name'];
    $price = floatval($_POST['price']);
    $image = $_POST['image'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO menu_items (name, price, image, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $name, $price, $image, $description);
    $stmt->execute();
}

if (isset($_POST['update_menu'])) {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $price = floatval($_POST['price']);
    $image = $_POST['image'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE menu_items SET name=?, price=?, image=?, description=? WHERE id=?");
    $stmt->bind_param("sdssi", $name, $price, $image, $description, $id);
    $stmt->execute();
}

if (isset($_GET['delete_menu'])) {
    $id = intval($_GET['delete_menu']);
    $stmt = $conn->prepare("DELETE FROM menu_items WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Fetch all reservations
$reservations = $conn->query("SELECT r.*, u.name AS user_name, u.email AS user_email 
                              FROM reservations r 
                              JOIN users u ON r.user_id = u.id 
                              ORDER BY r.reservation_time DESC");

// Fetch all users
$users = $conn->query("SELECT * FROM users ORDER BY id ASC");

// Fetch all menu items
$menu_items = $conn->query("SELECT * FROM menu_items ORDER BY id ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Restaurant</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background: url('https://i.pinimg.com/736x/32/24/24/322424f8095fa9a1e8764f9056b9b3fe.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #e0e1dd;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(13, 27, 42, 0.85);
            z-index: 0;
        }
        .container {
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        h1 { text-align: center; color: #00b4d8; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        th, td { padding: 10px; border: 1px solid #00b4d8; text-align: center; color: #e0e1dd; }
        th { background-color: rgba(0,180,216,0.2); }
        tr:hover { background-color: rgba(0,180,216,0.1); }
        .action-btn { padding: 6px 10px; border: none; border-radius: 6px; cursor: pointer; margin: 2px; }
        .edit-btn { background: #00b4d8; color: #0d1b2a; }
        .delete-btn { background: #ff4d6d; color: #fff; }
        .logout-btn { float: right; padding: 8px 12px; background: #ff4d6d; color: #fff; border-radius: 8px; text-decoration: none; margin-top: -50px; }
        .home-btn { float: left; padding: 8px 12px; background: #00b4d8; color: #0d1b2a; border-radius: 8px; text-decoration: none; margin-top: -50px; }
        .form-container { background: rgba(27,38,59,0.85); padding: 20px; border-radius: 12px; margin-bottom: 40px; }
        input, textarea { width: 100%; padding: 10px; margin: 5px 0; border-radius: 8px; border: 1px solid #ccc; }
        button.submit-btn { width: auto; padding: 10px 20px; background: #00b4d8; color: #0d1b2a; border: none; border-radius: 8px; cursor: pointer; }
        button.submit-btn:hover { background: #0077b6; color: #e0e1dd; }
    </style>
</head>
<body>
<div class="container">
    <a href="home.php" class="home-btn"><i class="fas fa-home"></i> Back to Home</a>
    <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>

    <h1>Admin Panel</h1>

    <!-- Reservations -->
    <h2>All Reservations</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Email</th>
            <th>Table #</th>
            <th>Date & Time</th>
            <th>People</th>
            <th>Requests</th>
        </tr>
        <?php while($r = $reservations->fetch_assoc()): ?>
        <tr>
            <td><?php echo $r['id']; ?></td>
            <td><?php echo htmlspecialchars($r['user_name']); ?></td>
            <td><?php echo htmlspecialchars($r['user_email']); ?></td>
            <td><?php echo $r['table_number']; ?></td>
            <td><?php echo $r['reservation_time']; ?></td>
            <td><?php echo isset($r['people']) ? $r['people'] : 0; ?></td>
            <td><?php echo htmlspecialchars($r['requests']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- Users -->
    <h2>All Users</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
        </tr>
        <?php while($u = $users->fetch_assoc()): ?>
        <tr>
            <td><?php echo $u['id']; ?></td>
            <td><?php echo htmlspecialchars($u['name']); ?></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- Menu Items -->
    <h2>Menu Management</h2>
    <div class="form-container">
        <h3>Create / Update Menu Item</h3>
        <form method="POST">
            <input type="hidden" name="id" placeholder="Item ID (for update)">
            <input type="text" name="name" placeholder="Item Name" required>
            <input type="number" name="price" placeholder="Price" required>
            <input type="text" name="image" placeholder="Image URL">
            <textarea name="description" placeholder="Description"></textarea>
            <button type="submit" name="create_menu" class="submit-btn">Create Item</button>
            <button type="submit" name="update_menu" class="submit-btn">Update Item</button>
        </form>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Image URL</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php while($m = $menu_items->fetch_assoc()): ?>
        <tr>
            <td><?php echo $m['id']; ?></td>
            <td><?php echo htmlspecialchars($m['name']); ?></td>
            <td><?php echo number_format($m['price']); ?></td>
            <td><?php echo htmlspecialchars($m['image']); ?></td>
            <td><?php echo htmlspecialchars($m['description']); ?></td>
            <td>
                <a href="?delete_menu=<?php echo $m['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Delete this item?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
