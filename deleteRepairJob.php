<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Validate the job_id and technician_id from the GET request
$job_id = filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT);
$technician_id = filter_input(INPUT_GET, 'technician_id', FILTER_VALIDATE_INT);

if (!$job_id || !$technician_id) {
    echo "Invalid job ID or technician ID.";
    exit();
}

// Attempt to delete the repair job
if (deleteRepairJob($pdo, $job_id)) {
    header("Location: viewRepairJobs.php?technician_id=" . $technician_id);
    exit;
} else {
    echo "Failed to delete the repair job. Please try again.";
}
?>
