<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: auth.php");
    exit();
}

include 'db.php';
$user_id = $_SESSION['user_id'];

// Get reservation ID from URL
$reservation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch reservation details for this user
$stmt = $conn->prepare("SELECT * FROM reservations WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $reservation_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$reservation = $result->fetch_assoc();

if(!$reservation){
    echo "Reservation not found or you do not have permission to view it.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reservation Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        background: url('https://i.pinimg.com/1200x/5a/1c/84/5a1c848f7698895cd59bb4d7bdfb3fcf.jpg') no-repeat center center fixed;
        background-size: cover;
        color: #e0e1dd;
        position: relative;
    }

    /* Dark overlay to make text readable */
    body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(13, 27, 42, 0.75);
        z-index: 0;
    }

    .details-container {
        max-width: 600px;
        margin: 50px auto;
        background: rgba(27, 38, 59, 0.95);
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.5);
        position: relative;
        z-index: 1;
    }

    h1 {
        color: #00b4d8;
        text-align: center;
        margin-bottom: 30px;
    }

    p {
        font-size: 18px;
        margin: 10px 0;
    }

    .back-btn {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 15px;
        background: #00b4d8;
        color: #0d1b2a;
        text-decoration: none;
        border-radius: 8px;
    }
    .back-btn:hover {
        background: #0077b6;
        color: #e0e1dd;
    }
</style>

</head>
<body>
    <div class="details-container">
        <h1>Reservation Details</h1>

        <p><strong>Table #:</strong> <?php echo isset($reservation['table_number']) ? $reservation['table_number'] : "-"; ?></p>

        <p><strong>Date & Time:</strong> 
            <?php 
                echo (!empty($reservation['reservation_time']) && strtotime($reservation['reservation_time']) !== false) 
                    ? date("Y-m-d H:i", strtotime($reservation['reservation_time'])) 
                    : "-"; 
            ?>
        </p>

        <p><strong>Number of People:</strong> 
            <?php echo isset($reservation['people']) && $reservation['people'] != "" ? $reservation['people'] : "-"; ?>
        </p>

        <p><strong>Special Requests:</strong> 
            <?php echo !empty($reservation['requests']) ? htmlspecialchars($reservation['requests']) : "-"; ?>
        </p>

        <p><strong>Created At:</strong> 
            <?php 
                echo (!empty($reservation['created_at']) && strtotime($reservation['created_at']) !== false) 
                    ? date("Y-m-d H:i", strtotime($reservation['created_at'])) 
                    : "-"; 
            ?>
        </p>

        <a href="view_reservations.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Reservations</a>
    </div>
</body>
</html>
