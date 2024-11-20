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
// Check if the request method is POST
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}
// Fetch all subjects from the database
function fetchSubjects() {
    $conn = dbConnect(); // Use your database connection function
    $subjects = [];
    $result = $conn->query("SELECT * FROM subjects");

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $subjects[] = $row;
        }
    }

    $conn->close();
    return $subjects;
}
// Retrieve and sanitize POST data
function postData($key) {
    return isset($_POST[$key]) ? htmlspecialchars(trim($_POST[$key])) : null;
}
// Fetch a single subject by its code
function getSubjectByCode($subject_code) {
    $conn = dbConnect(); // Connect to the database
    $query = "SELECT * FROM subjects WHERE subject_code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $subject_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $subject = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    return $subject; // Return the subject or null if not found
}

// Delete a subject by its code
function deleteSubject($subject_code, $redirect) {
    $conn = dbConnect(); // Connect to the database
    $query = "DELETE FROM subjects WHERE subject_code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $subject_code);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $stmt->close();
        $conn->close();
        // Redirect to the provided page after deletion
        header("Location: $redirect");
        exit;
    } else {
        $stmt->close();
        $conn->close();
        echo "<p class='text-danger'>Failed to delete the subject. Please try again.</p>";
    }
}


// Update a subject in the database
function updateSubject($subject_code, $subject_name, $redirect) {
    $conn = dbConnect(); // Database connection
    $query = "UPDATE subjects SET subject_name = ? WHERE subject_code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $subject_name, $subject_code);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Redirect on success
        $stmt->close();
        $conn->close();
        header("Location: $redirect");
        exit;
    } else {
        // Handle failure
        $stmt->close();
        $conn->close();
        echo "<p class='text-danger'>Failed to update the subject. Please try again.</p>";
    }
}








