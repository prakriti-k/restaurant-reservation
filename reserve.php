<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: auth.php");
    exit();
}

include 'db.php';

$message = "";
$maxTables = 10; // Total tables in the restaurant

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $user_id = $_SESSION['user_id'];
    $name = $_SESSION['user_name'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $people = (int)$_POST['people'];
    $requests = $_POST['requests'];
    $tableNumber = $_POST['table_number'];

    $reservationDateTime = $date . ' ' . $time;

    // Fetch reserved tables for this datetime
    $stmt = $conn->prepare("SELECT table_number FROM reservations WHERE reservation_time = ?");
    $stmt->bind_param("s", $reservationDateTime);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservedTables = [];
    while($row = $result->fetch_assoc()){
        $reservedTables[] = $row['table_number'];
    }

    // Check if chosen table is already reserved
    if(in_array($tableNumber, $reservedTables)){
        $message = "Sorry, Table #$tableNumber is already reserved at this time. Please choose another.";
    } else {
        $stmt = $conn->prepare("INSERT INTO reservations (user_id, table_number, reservation_time, people, requests) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisis", $user_id, $tableNumber, $reservationDateTime, $people, $requests);
        if($stmt->execute()){
            $message = "Your Table #$tableNumber has been reserved successfully for $people people at $time on $date!";
        } else {
            $message = "Error reserving table. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reserve Table - Restaurant</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background: #0d1b2a; color: #e0e1dd; display: flex; }
        .sidebar { width: 220px; background: #1b263b; height: 100vh; display: flex; flex-direction: column; align-items: center; padding-top: 30px; box-shadow: 2px 0 8px rgba(0,0,0,0.5); }
        .sidebar h2 { color: #00b4d8; margin-bottom: 40px; text-align: center; }
        .sidebar a { width: 80%; display: flex; align-items: center; text-decoration: none; color: #e0e1dd; background: #0d1b2a; padding: 12px 15px; margin: 10px 0; border-radius: 8px; transition: all 0.3s ease; }
        .sidebar a:hover { background: #00b4d8; color: #0d1b2a; }
        .sidebar a i { margin-right: 12px; font-size: 18px; }
        .main-content { flex: 1; min-height: 100vh; padding: 50px; display: flex; flex-direction: column; align-items: center; position: relative; }
        h1 { color: #00b4d8; margin-bottom: 20px; text-shadow: 1px 1px 6px rgba(0,0,0,0.7); }
        form { background: #1b263b; padding: 30px; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.5); width: 400px; display: flex; flex-direction: column; }
        input, select, textarea { padding: 12px; margin: 10px 0; border-radius: 8px; border: none; font-size: 16px; }
        textarea { resize: none; }
        .btn { background: #00b4d8; color: #0d1b2a; border: none; padding: 15px; border-radius: 8px; font-weight: bold; font-size: 16px; cursor: pointer; transition: all 0.3s ease; }
        .btn:hover { background: #0077b6; color: #e0e1dd; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; backdrop-filter: blur(5px); background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #1b263b; margin: 15% auto; padding: 30px; border-radius: 15px; width: 400px; text-align: center; color: #fff; box-shadow: 0 8px 20px rgba(0,0,0,0.7); }
        .modal-content p { font-size: 18px; margin-bottom: 20px; }
        .close { color: #e0e1dd; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: #00b4d8; }
        .images { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin-bottom: 40px; }
        .images img { width: 180px; height: 120px; object-fit: cover; border-radius: 12px; box-shadow: 0 6px 15px rgba(0,0,0,0.5); transition: transform 0.3s; }
        .images img:hover { transform: scale(1.05); }
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
    <h1>Reserve a Table</h1>

    <!-- Modal -->
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <span id="closeModal" class="close">&times;</span>
            <p id="modalMessage"></p>
        </div>
    </div>

    <!-- Stylish images -->
    <div class="images">
        <img src="https://i.pinimg.com/736x/32/24/24/322424f8095fa9a1e8764f9056b9b3fe.jpg" alt="Restaurant Interior">
        <img src="https://i.pinimg.com/736x/a3/d5/bd/a3d5bd5a70e6055d2682bf357a9bddf3.jpg" alt="Dining Table">
        <img src="https://www.shutterstock.com/image-photo/served-table-italian-food-seafood-600nw-1678594945.jpg" alt="Food Presentation">
    </div>

    <form id="reserveForm" method="POST" action="reserve.php">
        <label>Date:</label>
        <input type="text" id="datePicker" name="date" placeholder="Select a date" required>

        <label>Time:</label>
        <input type="text" id="timePicker" name="time" placeholder="Select time" required>

        <label>Number of People:</label>
        <input type="number" id="people" name="people" min="1" max="10" required>

        <label>Table Number:</label>
        <select name="table_number" required>
            <?php
            // Default: show availability for today if no date chosen yet
            $today = date("Y-m-d") . " " . date("H:i");
            $stmt = $conn->prepare("SELECT table_number FROM reservations WHERE reservation_time = ?");
            $stmt->bind_param("s", $today);
            $stmt->execute();
            $result = $stmt->get_result();
            $reservedNow = [];
            while($row = $result->fetch_assoc()){
                $reservedNow[] = $row['table_number'];
            }

            for($i=1; $i<=$maxTables; $i++){
                if(in_array($i, $reservedNow)){
                    echo "<option value='$i' disabled>Table $i (Reserved)</option>";
                } else {
                    echo "<option value='$i'>Table $i</option>";
                }
            }
            ?>
        </select>

        <label>Special Requests:</label>
        <textarea name="requests" rows="3" placeholder="Any special requests?"></textarea>

        <button type="submit" class="btn">Reserve</button>
    </form>
</div>

<script>
flatpickr("#datePicker", { minDate: "today", dateFormat: "Y-m-d" });
flatpickr("#timePicker", { enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true, minTime: "10:00", maxTime: "22:00" });

// Front-end validation
document.getElementById('reserveForm').addEventListener('submit', function(e){
    const date = document.getElementById('datePicker').value;
    const time = document.getElementById('timePicker').value;
    const people = document.getElementById('people').value;
    if(date === "" || time === "" || people === "" || people < 1 || people > 10){
        alert("Please fill all fields correctly.");
        e.preventDefault();
    }
});

// Modal JS
const modal = document.getElementById("messageModal");
const modalMessage = document.getElementById("modalMessage");
const closeModal = document.getElementById("closeModal");

<?php if($message != ""): ?>
    modalMessage.innerText = "<?php echo $message; ?>";
    modal.style.display = "block";
<?php endif; ?>

closeModal.onclick = function() { modal.style.display = "none"; }
window.onclick = function(event) { if(event.target == modal){ modal.style.display = "none"; } }
</script>

</body>
</html>
