<?php  

// Insert a new technician, including the `added_by` field
function insertTechnician($pdo, $username, $first_name, $last_name, $date_of_birth, $specialization, $added_by) {
    $sql = "INSERT INTO repair_technicians (username, first_name, last_name, date_of_birth, specialization, added_by) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$username, $first_name, $last_name, $date_of_birth, $specialization, $added_by]);
}

// Update an existing technician, including `last_updated_by` and updating the `last_updated` timestamp
function updateTechnician($pdo, $firstName, $lastName, $dateOfBirth, $specialization, $lastUpdatedBy, $technicianId) {
    $stmt = $pdo->prepare("
        UPDATE repair_technicians 
        SET first_name = ?, last_name = ?, date_of_birth = ?, specialization = ?, last_updated_by = ?, last_updated = NOW() 
        WHERE technician_id = ?
    ");
    return $stmt->execute([$firstName, $lastName, $dateOfBirth, $specialization, $lastUpdatedBy, $technicianId]);
}


function deleteTechnician($pdo, $technician_id, $deleted_by) {
    // Delete related repair jobs first
    $pdo->prepare("DELETE FROM repair_jobs WHERE technician_id = ?")->execute([$technician_id]);

    // Update the `last_updated_by` and `last_updated` before deletion in case you want to log the delete action
    $updateStmt = $pdo->prepare("UPDATE repair_technicians SET last_updated_by = ?, last_updated = NOW() WHERE technician_id = ?");
    $updateStmt->execute([$deleted_by, $technician_id]);

    // Delete the technician
    $stmt = $pdo->prepare("DELETE FROM repair_technicians WHERE technician_id = ?");
    return $stmt->execute([$technician_id]);
}

// Get all technicians
function getAllTechnicians($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM repair_technicians");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get a specific technician by ID
function getTechnicianByID($pdo, $technician_id) {
    $stmt = $pdo->prepare("
        SELECT 
            rt.*, 
            u.username AS last_updated_by_name 
        FROM 
            repair_technicians rt
        LEFT JOIN 
            users u ON rt.last_updated_by = u.user_id
        WHERE 
            rt.technician_id = ?
    ");
    $stmt->execute([$technician_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}




// Get repair jobs associated with a specific technician
function getRepairJobsByTechnician($pdo, $technician_id) {
    $stmt = $pdo->prepare("SELECT * FROM repair_jobs WHERE technician_id = ?");
    $stmt->execute([$technician_id]);
    return $stmt->fetchAll();
}

// Insert a new repair job, including the `added_by` field
function insertRepairJob($pdo, $client_name, $mouse_model, $repair_description, $technician_id, $added_by) {
    $stmt = $pdo->prepare("INSERT INTO repair_jobs (client_name, mouse_model, repair_description, technician_id, added_by) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$client_name, $mouse_model, $repair_description, $technician_id, $added_by]);
}

// Get a specific repair job by ID
function getRepairJobByID($pdo, $job_id) {
    $stmt = $pdo->prepare("
        SELECT 
            r.*, 
            u1.username AS added_by_name, 
            u2.username AS last_updated_by_name
        FROM 
            repair_jobs r
        LEFT JOIN 
            users u1 ON r.added_by = u1.user_id
        LEFT JOIN 
            users u2 ON r.last_updated_by = u2.user_id
        WHERE 
            r.job_id = ?
    ");
    $stmt->execute([$job_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Update an existing repair job, including `last_updated_by` and updating the `last_updated` timestamp
function updateRepairJob($pdo, $clientName, $mouseModel, $repairDescription, $status, $job_id, $lastUpdatedBy) {
    $stmt = $pdo->prepare("
        UPDATE repair_jobs 
        SET 
            client_name = ?, 
            mouse_model = ?, 
            repair_description = ?, 
            status = ?, 
            last_updated = NOW(), 
            last_updated_by = ?
        WHERE 
            job_id = ?
    ");
    return $stmt->execute([$clientName, $mouseModel, $repairDescription, $status, $lastUpdatedBy, $job_id]);
}


// Delete a specific repair job
function deleteRepairJob($pdo, $job_id) {
    $stmt = $pdo->prepare("DELETE FROM repair_jobs WHERE job_id = ?");
    return $stmt->execute([$job_id]);
}

function addRepairJob($pdo, $clientName, $mouseModel, $repairDescription, $status, $addedBy) {
    $stmt = $pdo->prepare("INSERT INTO repair_jobs (client_name, mouse_model, repair_description, status, added_by) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$clientName, $mouseModel, $repairDescription, $status, $addedBy]);
}

?>