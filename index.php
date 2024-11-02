<?php
session_start();
require_once 'core/dbConfig.php';
require_once 'core/models.php';

// Redirect to login page if user is not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Display welcome message with logged-in user's username
echo "<h1>Welcome, " . htmlspecialchars($_SESSION['username']) . "!</h1>";

// If the form is submitted, add a new technician with the logged-in user's ID as `added_by`
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insertTechnicianBtn'])) {
    $username = $_POST['username'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $dateOfBirth = $_POST['dateOfBirth'];
    $specialization = $_POST['specialization'];
    $added_by = $_SESSION['user_id']; // The logged-in user's ID

    // Insert the new technician into the database
    $stmt = $pdo->prepare("INSERT INTO repair_technicians (username, first_name, last_name, date_of_birth, specialization, added_by) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$username, $firstName, $lastName, $dateOfBirth, $specialization, $added_by])) {
        echo "<p>Technician added successfully!</p>";
    } else {
        echo "<p>Error: Could not add technician.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mouse Repair Services - Technician Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Mouse Repair Services</h1>

        <h2>Add New Technician</h2>
        <form action="index.php" method="POST">
            <p>
                <label for="username">Username</label> 
                <input type="text" name="username" required>
            </p>
            <p>
                <label for="firstName">First Name</label> 
                <input type="text" name="firstName" required>
            </p>
            <p>
                <label for="lastName">Last Name</label> 
                <input type="text" name="lastName" required>
            </p>
            <p>
                <label for="dateOfBirth">Date of Birth</label> 
                <input type="date" name="dateOfBirth" required>
            </p>
            <p>
                <label for="specialization">Specialization</label> 
                <input type="text" name="specialization" required>
            </p>
            <p>
                <input type="submit" name="insertTechnicianBtn" value="Add Technician">
            </p>
        </form>
        
        <h2>Technicians List</h2>
        <?php $technicians = getAllTechnicians($pdo); ?>
        <ul>
            <?php foreach ($technicians as $technician): ?>
            <li>
                <?php echo htmlspecialchars($technician['username']); ?> - 
                <a href="viewRepairJobs.php?technician_id=<?php echo $technician['technician_id']; ?>">View Jobs</a> |
                <a href="editTechnician.php?technician_id=<?php echo $technician['technician_id']; ?>">Edit</a> |
                <a href="deleteTechnician.php?technician_id=<?php echo $technician['technician_id']; ?>">Delete</a>
            </li>
            <?php endforeach; ?>
        </ul>

        <!-- Logout link -->
        <p><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
