<?php 
session_start(); // Start the session
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the technician's information
$technician_id = filter_input(INPUT_GET, 'technician_id', FILTER_VALIDATE_INT);
$getTechnicianByID = getTechnicianByID($pdo, $technician_id); 

// Handle the form submission to update the technician
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $dateOfBirth = $_POST['dateOfBirth'];
    $specialization = $_POST['specialization'];
    $lastUpdatedBy = $_SESSION['user_id']; // Get the user ID from session

    // Update the technician
    if (updateTechnician($pdo, $firstName, $lastName, $dateOfBirth, $specialization, $lastUpdatedBy, $technician_id)) {
        echo "<p>Technician updated successfully!</p>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.php'; // Redirect to homepage
                }, 2000); // Redirect after 2 seconds
              </script>";
    } else {
        // Fetch the error information
        $errorInfo = $pdo->errorInfo();
        echo "<p>Error updating the technician: " . htmlspecialchars($errorInfo[2]) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Technician</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Edit the Technician!</h1>
    
    <!-- Include the form here -->
    <form action="" method="POST">
        <p>
            <label for="firstName">First Name</label> 
            <input type="text" name="firstName" value="<?php echo htmlspecialchars($getTechnicianByID['first_name']); ?>" required>
        </p>
        <p>
            <label for="lastName">Last Name</label> 
            <input type="text" name="lastName" value="<?php echo htmlspecialchars($getTechnicianByID['last_name']); ?>" required>
        </p>
        <p>
            <label for="dateOfBirth">Date of Birth</label> 
            <input type="date" name="dateOfBirth" value="<?php echo htmlspecialchars($getTechnicianByID['date_of_birth']); ?>" required>
        </p>
        <p>
            <label for="specialization">Specialization</label> 
            <input type="text" name="specialization" value="<?php echo htmlspecialchars($getTechnicianByID['specialization']); ?>" required>
        </p>
        <p>
            <input type="submit" name="editTechnicianBtn" value="Update Technician">
        </p>
    </form>

    <!-- Display the user who edited the technician and timestamp -->
    <p>Last edited by: <?php echo htmlspecialchars($getTechnicianByID['last_updated_by_name'] ?? 'N/A'); ?></p>
    <p>Last updated on: <?php echo htmlspecialchars($getTechnicianByID['last_updated'] ?? 'N/A'); ?></p>

</body>
</html>
