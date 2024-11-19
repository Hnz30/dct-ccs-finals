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

function checkDuplicateStudentData($student_id, $conn) {
    // Ensure student_id is a string
    if (!is_string($student_id)) {
        return ["Invalid Student ID format."];
    }

    // Query to check if student_id already exists
    $query = "SELECT COUNT(*) AS count FROM students WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch the count
    $row = $result->fetch_assoc();
    $stmt->close();

    // Return an error if a duplicate is found
    if ($row['count'] > 0) {
        return ["Student ID already exists in the database."];
    }

    return []; // No errors
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
// Function to display errors
function displayErrors($errors) {
    $errorHtml = '<div class="alert alert-danger">';
    $errorHtml .= '<ul>';
    foreach ($errors as $error) {
        $errorHtml .= '<li>' . htmlspecialchars($error) . '</li>';
    }
    $errorHtml .= '</ul>';
    $errorHtml .= '</div>';
    return $errorHtml;
}


