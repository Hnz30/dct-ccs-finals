<?php
// All project functions should be placed here

// Database connection function
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

// Function to check if the request is a POST request
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

// Function to validate student data
function validateStudentData($data) {
    $errors = [];

    if (empty($data['student_id']) || !is_numeric($data['student_id'])) {
        $errors[] = "Student ID must be a valid number.";
    }

    if (empty($data['first_name'])) {
        $errors[] = "First name is required.";
    }

    if (empty($data['last_name'])) {
        $errors[] = "Last name is required.";
    }

    return $errors;
}

// Function to check for duplicate student data
// Function to check if student_id already exists in the database
function checkDuplicateStudentData($student_id, $conn) {
    $errors = [];
    $query = "SELECT COUNT(*) AS count FROM students WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    if ($count > 0) {
        $errors[] = "Student ID already exists. Please choose a different ID.";
    }
    $stmt->close();
    return $errors;
}


// Fetch a student by student_id
function getStudentById($student_id) {
    $conn = dbConnect(); 
    
    $query = "SELECT * FROM students WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $student_id);  // Changed from "i" to "s" for string type
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }

    $stmt->close();
    $conn->close();
}

// Fetch all subjects from the database
function fetchSubjects() {
    $conn = dbConnect();
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
    $conn = dbConnect();
    $query = "SELECT * FROM subjects WHERE subject_code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $subject_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $subject = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    return $subject;
}

// Delete a subject by its code
function deleteSubject($subject_code, $redirect) {
    $conn = dbConnect();
    $query = "DELETE FROM subjects WHERE subject_code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $subject_code);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $stmt->close();
        $conn->close();
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
    $conn = dbConnect();
    $query = "UPDATE subjects SET subject_name = ? WHERE subject_code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $subject_name, $subject_code);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $stmt->close();
        $conn->close();
        header("Location: $redirect");
        exit;
    } else {
        $stmt->close();
        $conn->close();
        echo "<p class='text-danger'>Failed to update the subject. Please try again.</p>";
    }
}

// Update subject grade for a student
function updateSubjectGrade($student_id, $subject_id, $grade, $redirect_url) {
    $conn = dbConnect();
    
    // Prepare the SQL query to update the grade
    $query = "UPDATE students_subjects SET grade = ? WHERE student_id = ? AND subject_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("dii", $grade, $student_id, $subject_id); // "d" for double, "ii" for integers
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        header("Location: $redirect_url");
        exit;
    } else {
        echo "<p class='text-danger'>Failed to assign grade. Please try again.</p>";
    }
    
    $stmt->close();
    $conn->close();
}
// Function to display error messages
function displayErrors($errors) {
    $errorHtml = '<div class="alert alert-danger"><ul>';
    foreach ($errors as $error) {
        $errorHtml .= '<li>' . htmlspecialchars($error) . '</li>';
    }
    $errorHtml .= '</ul></div>';
    return $errorHtml;
}

?>
