<?php 
require_once __DIR__ . '/../core/dbConfig.php';  
require_once __DIR__ . '/../core/models.php';  
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Redirect to login page if not logged in
    exit();
}

// Handle technician insertion
if (isset($_POST['insertTechnicianBtn'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $dateOfBirth = $_POST['dateOfBirth'];
    $specialization = $_POST['specialization'];
    $addedBy = $_SESSION['user_id']; // Set `added_by` to the logged-in user's ID

    // Call the insertTechnician function with `added_by`
    if (insertTechnician($pdo, $firstName, $lastName, $dateOfBirth, $specialization, $addedBy)) {
        header("Location: ../index.php"); // Redirect after successful insertion
        exit;
    } else {
        echo "Error: Unable to add technician.";
    }
}

// Update the technician
if (updateTechnician($pdo, $firstName, $lastName, $dateOfBirth, $specialization, $lastUpdatedBy, $technician_id)) {
    echo "<p>Technician updated successfully!</p>";
    // Redirect or provide feedback as needed
} else {
    // Fetch the error information
    $errorInfo = $pdo->errorInfo();
    echo "<p>Error updating the technician: " . htmlspecialchars($errorInfo[2]) . "</p>";
}

// Handle technician deletion
if (isset($_POST['deleteTechnicianBtn'])) {
    $technician_id = filter_input(INPUT_GET, 'technician_id', FILTER_VALIDATE_INT);
    
    if (deleteTechnician($pdo, $technician_id)) {
        header("Location: ../index.php");
        exit;
    } else {
        echo "Error: Unable to delete technician.";
    }
}

// Handle adding repair job
if (isset($_POST['insertRepairJobBtn'])) {
    $clientName = $_POST['clientName'];
    $mouseModel = $_POST['mouseModel'];
    $repairDescription = $_POST['repairDescription'];
    $technician_id = $_POST['technician_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO repair_jobs (client_name, mouse_model, repair_description, technician_id, added_by) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$clientName, $mouseModel, $repairDescription, $technician_id, $user_id])) {
        // Redirect to the view page or wherever appropriate after success
        header("Location: ../viewRepairJobs.php?technician_id=" . $technician_id);
        exit();
    } else {
        echo "Error: Could not add repair job.";
    }
}

// Handle editing repair job
if (isset($_POST['editRepairJobBtn'])) {
    $job_id = filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT);
    $clientName = $_POST['clientName'];
    $mouseModel = $_POST['mouseModel'];
    $repairDescription = $_POST['repairDescription'];
    $status = $_POST['status'];
    $lastUpdatedBy = $_SESSION['user_id'];

    // Update repair job details
    if (updateRepairJob($pdo, $clientName, $mouseModel, $repairDescription, $status, $lastUpdatedBy, $job_id)) {
        header("Location: ../viewRepairJobs.php?technician_id=" . $_GET['technician_id']);
        exit;
    } else {
        echo "Error: Unable to update repair job.";
    }
}

// Handle deleting repair job
if (isset($_POST['deleteRepairJobBtn'])) {
    $job_id = filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT);

    if (deleteRepairJob($pdo, $job_id)) {
        header("Location: ../viewRepairJobs.php?technician_id=" . $_GET['technician_id']);
        exit;
    } else {
        echo "Error: Unable to delete repair job.";
    }
}

// If nothing matched, redirect to the index
header("Location: ../index.php");
exit();
?>
