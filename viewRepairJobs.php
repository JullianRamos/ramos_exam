<?php 
require_once 'core/dbConfig.php';
require_once 'core/models.php'; 
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Sanitize and validate technician_id from GET request
$technician_id = filter_input(INPUT_GET, 'technician_id', FILTER_VALIDATE_INT);
if (!$technician_id) {
    echo "Invalid technician ID.";
    exit();
}

// Get technician information
$getTechnicianInfo = getTechnicianByID($pdo, $technician_id);
if (!$getTechnicianInfo) {
    echo "Technician not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Repair Jobs</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <a href="index.php">Return to home</a>
        <h1>Technician: <?php echo htmlspecialchars($getTechnicianInfo['username']); ?></h1>

        <!-- Add New Repair Job Form -->
        <h2>Add New Repair Job</h2>
        <form action="core/handleForms.php" method="POST">
            <p>
                <label for="clientName">Client Name</label> 
                <input type="text" name="clientName" required>
            </p>
            <p>
                <label for="mouseModel">Mouse Model</label> 
                <input type="text" name="mouseModel" required>
            </p>
            <p>
                <label for="repairDescription">Repair Description</label> 
                <textarea name="repairDescription" required></textarea>
            </p>
            <input type="hidden" name="technician_id" value="<?php echo $technician_id; ?>">
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>"> <!-- Track added_by user -->
            <p>
                <input type="submit" name="insertRepairJobBtn" value="Add Repair Job">
            </p>
        </form>

        <!-- Display Repair Jobs List -->
        <h2>Repair Jobs</h2>
        <table>
          <tr>
            <th>Job ID</th>
            <th>Client Name</th>
            <th>Mouse Model</th>
            <th>Repair Description</th>
            <th>Status</th>
            <th>Date Added</th>
            <th>Action</th>
          </tr>
          <?php 
          $repairJobs = getRepairJobsByTechnician($pdo, $technician_id); 
          foreach ($repairJobs as $job): 
          ?>
          <tr>
            <td><?php echo htmlspecialchars($job['job_id']); ?></td>	  	
            <td><?php echo htmlspecialchars($job['client_name']); ?></td>	  	
            <td><?php echo htmlspecialchars($job['mouse_model']); ?></td>	  	
            <td><?php echo htmlspecialchars($job['repair_description']); ?></td>	  	
            <td><?php echo htmlspecialchars($job['status']); ?></td>
            <td><?php echo htmlspecialchars($job['date_added']); ?></td>
            <td>
                <a href="editRepairJob.php?job_id=<?php echo $job['job_id']; ?>&technician_id=<?php echo $technician_id; ?>">Edit</a>
                <a href="deleteRepairJob.php?job_id=<?php echo $job['job_id']; ?>&technician_id=<?php echo $technician_id; ?>">Delete</a>
            </td>	  	
          </tr>
          <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
