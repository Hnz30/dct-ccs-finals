<?php       
// All project functions should be placed here

function dbConnect() {
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'dct-ccs-finals';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

return $conn;
}


?>



<?php

// Function to validate student data
function validateStudentData($data) {
    $errors = [];

    // Validate student ID (must be numeric and not empty)
    if (empty($data['student_id']) || !is_numeric($data['student_id'])) {
        $errors[] = "Student ID must be a valid number.";
    }

    // Validate first name (must not be empty)
    if (empty($data['first_name'])) {
        $errors[] = "First name is required.";
    }

    // Validate last name (must not be empty)
    if (empty($data['last_name'])) {
        $errors[] = "Last name is required.";
    }

    return $errors;
}

// Example function to check for duplicate student data
function checkDuplicateStudentData($student_data) {
    // For example, assume we check an array in the session
    // This would need to be replaced with database queries in a real app
    if (!empty($_SESSION['student_data'])) {
        foreach ($_SESSION['student_data'] as $existing_student) {
            if ($existing_student['student_id'] == $student_data['student_id']) {
                return true; // Duplicate student ID found
            }
        }
    }
    return false; // No duplicate found
}
// Update student data
function updateStudentData($index, $student_data) {
    // Check if the student data is valid (you can add further validation here if needed)
    if (isset($_SESSION['student_data'][$index])) {
        // Update student data in the session
        $_SESSION['student_data'][$index] = $student_data;
        return true;
    }
    return false;  // Return false if the student data was not found
}
// Function to get student data by student_id or index from session
function getSelectedStudentData($index) {
    if (isset($_SESSION['student_data'][$index])) {
        return $_SESSION['student_data'][$index];
    }
    return null;  // Return null if student is not found
}

