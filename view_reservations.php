<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: auth.php");
    exit();
}

include 'db.php';
$user_id = $_SESSION['user_id'];

// Cancel reservation if requested
if(isset($_GET['cancel_id'])){
    $cancel_id = intval($_GET['cancel_id']);
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cancel_id, $user_id);
    $stmt->execute();
    header("Location: view_reservations.php"); // refresh page
    exit();
}

// Fetch all reservations of this user
$stmt = $conn->prepare("SELECT * FROM reservations WHERE user_id = ? ORDER BY reservation_time DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Reservations - Restaurant</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: url('https://i.pinimg.com/736x/32/24/24/322424f8095fa9a1e8764f9056b9b3fe.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #e0e1dd;
            display: flex;
            position: relative;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(13, 27, 42, 0.75);
            z-index: 0;
        }
        .sidebar {
            width: 220px;
            background: #1b263b;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 30px;
            box-shadow: 2px 0 8px rgba(0,0,0,0.5);
            position: relative;
            z-index: 1;
        }
        .sidebar h2 { color: #00b4d8; margin-bottom: 40px; text-align: center; }
        .sidebar a { width: 80%; display: flex; align-items: center; text-decoration: none; color: #e0e1dd; background: #0d1b2a; padding: 12px 15px; margin: 10px 0; border-radius: 8px; transition: all 0.3s ease; }
        .sidebar a:hover { background: #00b4d8; color: #0d1b2a; }
        .sidebar a i { margin-right: 12px; font-size: 18px; }

        .main-content {
            flex: 1;
            min-height: 100vh;
            padding: 50px;
            position: relative;
            z-index: 1;
        }
        h1 { color: #00b4d8; margin-bottom: 30px; text-shadow: 1px 1px 6px rgba(0,0,0,0.7); text-align: center; }

        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(27, 38, 59, 0.9);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.5);
        }
        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #00b4d8;
            color: #e0e1dd;
        }
        th { background-color: #0d1b2a; }
        tr:hover { background-color: rgba(0,180,216,0.2); }

        /* Action buttons */
        .action-btn {
            padding: 8px 12px;
            margin: 3px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .view-btn { background-color: #00b4d8; color: #0d1b2a; }
        .view-btn:hover { background-color: #0077b6; color: #e0e1dd; }
        .cancel-btn { background-color: #ff4d6d; color: #fff; }
        .cancel-btn:hover { background-color: #e60039; }

        .no-reservations {
            text-align: center;
            margin-top: 50px;
            font-size: 18px;
            color: #90e0ef;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2><?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
    <a href="home.php"><i class="fas fa-home"></i> Home</a>
    <a href="reserve.php"><i class="fas fa-chair"></i> Reserve Table</a>
    <a href="view_reservations.php"><i class="fas fa-calendar-check"></i> My Reservations</a>
    <a href="menu.php"><i class="fas fa-utensils"></i> View Menu</a>
    <?php if($_SESSION['user_name'] == "admin"){ ?>
        <a href="admin_panel.php"><i class="fas fa-user-cog"></i> Admin Panel</a>
    <?php } ?>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">
    <h1>My Reservations</h1>

    <?php if($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Table #</th>
                <th>Date & Time</th>
                <th>Number of People</th>
                <th>Special Requests</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['table_number']; ?></td>
                    <td><?php echo date("Y-m-d H:i", strtotime($row['reservation_time'])); ?></td>
                    <td><?php echo isset($row['people']) ? $row['people'] : "-"; ?></td>
                    <td><?php echo !empty($row['requests']) ? htmlspecialchars($row['requests']) : "-"; ?></td>
                    <td>
                        <a href="reservation_details.php?id=<?php echo $row['id']; ?>" class="action-btn view-btn"><i class="fas fa-eye"></i> View</a>
                        <a href="view_reservations.php?cancel_id=<?php echo $row['id']; ?>" class="action-btn cancel-btn" onclick="return confirm('Are you sure you want to cancel this reservation?');"><i class="fas fa-times"></i> Cancel</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p class="no-reservations">You have no reservations yet. Reserve a table now!</p>
    <?php endif; ?>
</div>

</body>
</html>
