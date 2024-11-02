<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Sanitize and validate the job ID from GET request
$job_id = filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT);
if (!$job_id) {
    echo "Invalid job ID.";
    exit();
}

// Get the repair job details based on job ID
$repairJob = getRepairJobByID($pdo, $job_id);
if (!$repairJob) {
    echo "Repair job not found.";
    exit();
}

// Handle the form submission to update the repair job
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $clientName = $_POST['clientName'];
    $mouseModel = $_POST['mouseModel'];
    $repairDescription = $_POST['repairDescription'];
    $status = $_POST['status'];
    $lastUpdatedBy = $_SESSION['user_id']; // Use user_id for updating

    // Update the repair job
    if (updateRepairJob($pdo, $clientName, $mouseModel, $repairDescription, $status, $job_id, $lastUpdatedBy)) {
        // Success message
        echo "<p>Repair job updated successfully!</p>";
    } else {
        echo "<p>Error updating the repair job. Please try again.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Repair Job</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Edit Repair Job</h1>
    
    <form action="" method="POST">
        <p>
            <label for="clientName">Client Name</label> 
            <input type="text" name="clientName" value="<?php echo htmlspecialchars($repairJob['client_name']); ?>" required>
        </p>
        <p>
            <label for="mouseModel">Mouse Model</label> 
            <input type="text" name="mouseModel" value="<?php echo htmlspecialchars($repairJob['mouse_model']); ?>" required>
        </p>
        <p>
            <label for="repairDescription">Repair Description</label> 
            <textarea name="repairDescription" required><?php echo htmlspecialchars($repairJob['repair_description']); ?></textarea>
        </p>
        <p>
            <label for="status">Status</label>
            <select name="status" required>
                <option value="Pending" <?php echo $repairJob['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="In Progress" <?php echo $repairJob['status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                <option value="Completed" <?php echo $repairJob['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                <option value="Cancelled" <?php echo $repairJob['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
        </p>
        <p>
            <input type="hidden" name="added_by" value="<?php echo htmlspecialchars($repairJob['added_by_name']); ?>"> <!-- Track who added the job -->
            <input type="submit" name="editRepairJobBtn" value="Update Repair Job">
        </p>
    </form>

    <h2>Repair Job Details</h2>
    <p><strong>Added By:</strong> <?php echo htmlspecialchars($repairJob['added_by_name']); ?></p>
    <p><strong>Last Updated By:</strong> <?php echo htmlspecialchars($repairJob['last_updated_by_name']); ?></p>
    <p><strong>Last Updated:</strong> <?php echo htmlspecialchars($repairJob['last_updated']); ?></p>

    <p>
        <a href="index.php"><button>Back to Homepage</button></a> <!-- Button to go back to homepage -->
    </p>
</body>
</html>
