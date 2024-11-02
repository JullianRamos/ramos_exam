<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

if (isset($_GET['technician_id'])) {
    $technician_id = $_GET['technician_id'];
    $deleted_by = $_SESSION['user_id']; // Track the user performing the deletion

    // Call the delete function with the ID of the user deleting the technician
    if (deleteTechnician($pdo, $technician_id, $deleted_by)) {
        header("Location: index.php"); // Redirect to index after deletion
        exit();
    } else {
        echo "Error deleting technician.";
    }
}
?>
