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
function renderBreadcrumbs($breadcrumbs) {
    $lastIndex = count($breadcrumbs) - 1;
    echo '<nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">';
    foreach ($breadcrumbs as $index => $breadcrumb) {
        if ($index === $lastIndex) {
            echo '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($breadcrumb['label']) . '</li>';
        } else {
            echo '<li class="breadcrumb-item"><a href="' . htmlspecialchars($breadcrumb['link']) . '">' . htmlspecialchars($breadcrumb['label']) . '</a></li>';
        }
    }
    echo '</ol></nav>';
}
function authenticateUser($email, $password) {
    $conn = dbConnect(); // Connect to the database

    // Hash the password for matching
    $hashedPassword = md5($password); 

    // Query to check user credentials
    $query = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $hashedPassword);
    $stmt->execute();
    $result = $stmt->get_result();

    // If user exists, return user data
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $user; // Return user data
    }

    // If no user is found, return false
    $stmt->close();
    $conn->close();
    return false;
}
// Function to get all subjects checkboxes for the student
function getAllSubjectsCheckboxes($student_id) {
    // Fetch all subjects from the database
    $subjects = fetchSubjects(); // Assuming fetchSubjects() is defined and fetches all subjects from the DB
    $checkboxHtml = '';

    // Loop through all subjects and create a checkbox for each one
    foreach ($subjects as $subject) {
        $checkboxHtml .= '<div class="form-check">';
        $checkboxHtml .= '<input class="form-check-input" type="checkbox" name="subjects[]" value="' . htmlspecialchars($subject['subject_code']) . '" id="subject_' . htmlspecialchars($subject['subject_code']) . '">';
        $checkboxHtml .= '<label class="form-check-label" for="subject_' . htmlspecialchars($subject['subject_code']) . '">';
        $checkboxHtml .= htmlspecialchars($subject['subject_name']);
        $checkboxHtml .= '</label>';
        $checkboxHtml .= '</div>';
    }

    return $checkboxHtml;
}
function fetchAssignSubjects($student_id) {
    $conn = dbConnect(); // Ensure this function is defined to get your database connection

    // Join the `students_subjects` table with `subjects` to fetch subject details
    $query = "
        SELECT s.subject_code, s.subject_name, ss.grade
        FROM students_subjects ss
        JOIN subjects s ON ss.subject_id = s.id
        WHERE ss.student_id = ?
    ";

    // Prepare the query
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id); // Bind the student_id parameter
    $stmt->execute();

    // Fetch the results
    $result = $stmt->get_result();
    $assignedSubjects = $result->fetch_all(MYSQLI_ASSOC);

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    return $assignedSubjects;
}
function assignSubjectsToStudent($student_id, $subjects) {
    $conn = dbConnect(); // Ensure the connection is valid
    
    // Loop through the selected subjects and assign each one to the student
    foreach ($subjects as $subject_code) {
        // Fetch subject by its code to get the corresponding subject_id
        $query = "SELECT id FROM subjects WHERE subject_code = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $subject_code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            $conn->close();
            return false;  // Subject code not found, return failure
        }

        $subject = $result->fetch_assoc();
        $subject_id = $subject['id'];

        // Insert the subject for the student
        $insertQuery = "
            INSERT INTO students_subjects (student_id, subject_id, grade) 
            VALUES (?, ?, ?)
        ";

        // Prepare the statement
        $stmt = $conn->prepare($insertQuery);
        if (!$stmt) {
            $conn->close();
            return false;  // Query preparation failed
        }

        // Default grade to 0.00
        $default_grade = 0.00;

        // Bind parameters
        $stmt->bind_param("iis", $student_id, $subject_id, $default_grade);

        // Execute the statement
        if (!$stmt->execute()) {
            $stmt->close();
            $conn->close();
            return false;  // Execution failed
        }
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    return true;  // Success
}
function detachSubject($student_id, $subject_id, $redirect_url) {
    // Establish database connection
    $conn = dbConnect();

    if (!$conn) {
        echo "<p class='text-danger'>Database connection failed.</p>";
        return;
    }

    // Prepare the delete query
    $query = "DELETE FROM students_subjects WHERE student_id = ? AND subject_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        echo "<p class='text-danger'>Error preparing statement: " . $conn->error . "</p>";
        return;
    }

    // Bind parameters
    $stmt->bind_param("ii", $student_id, $subject_id);

    // Execute the query
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Redirect if detachment is successful
            header("Location: $redirect_url");
            exit;
        } else {
            // Record not found or already deleted
            echo "<p class='text-warning'>No record found to detach. It might have already been removed.</p>";
        }
    } else {
        // Execution failed
        echo "<p class='text-danger'>Error executing query: " . $stmt->error . "</p>";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}

// Define the displayMessage function
function displayMessage($message) {
    return '<div class="alert alert-success">' . htmlspecialchars($message) . '</div>';
}
function getAssignedSubjectsCheckboxes($student_id) {
    $assignedSubjects = fetchAssignSubjects($student_id);
    $html = '';
    foreach ($assignedSubjects as $subject) {
        if (!isset($subject['subject_id']) || !isset($subject['subject_name'])) {
            continue; // Skip invalid entries
        }
        $html .= '<div class="form-check">';
        $html .= '<input type="checkbox" class="form-check-input" name="subjects[]" value="' . htmlspecialchars($subject['subject_id']) . '">';
        $html .= '<label class="form-check-label">' . htmlspecialchars($subject['subject_name']) . '</label>';
        $html .= '</div>';
    }
    return $html;
}

function detachSubjectsFromStudent($student_id, $subjects) {
    $conn = dbConnect();
    $query = "DELETE FROM students_subjects WHERE student_id = ? AND subject_id = ?";
    $stmt = $conn->prepare($query);

    foreach ($subjects as $subject_id) {
        $stmt->bind_param("ii", $student_id, $subject_id);
        $stmt->execute();
    }

    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    $conn->close();

    return $affectedRows > 0;
}
// Function to get data from the database based on the field and key
// Function to get data from the database based on the field and key
function GETdata($conn, $key) {
    // Check if we are retrieving student data
    if ($key == 'student_id' || $key == 'firstname' || $key == 'lastname') {
        $student_id = $_GET['student_id'];
        $sql = "SELECT student_id, first_name, last_name FROM students WHERE student_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();

        if ($key == 'student_id') {
            return $student['student_id'] ?? 'N/A';
        } elseif ($key == 'firstname') {
            return $student['first_name'] ?? 'N/A';
        } elseif ($key == 'lastname') {
            return $student['last_name'] ?? 'N/A';
        }
    }
    // Check if we are retrieving subject data
    elseif ($key == 'subject_id' || $key == 'subject_name') {
        // Ensure that subject_id is passed
        if (isset($_GET['subject_id'])) {
            $subject_id = $_GET['subject_id'];
            $sql = "SELECT subject_code, subject_name FROM subjects WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $subject_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $subject = $result->fetch_assoc();

            if ($key == 'subject_id') {
                return $subject['subject_code'] ?? 'N/A';
            } elseif ($key == 'subject_name') {
                return $subject['subject_name'] ?? 'N/A';
            }
        }
        return 'N/A'; // Default return if subject_id is not passed
    }
    return 'N/A'; // Default return if no matching key
}




// In your functions.php or a separate file (e.g., db.php)
$host = 'localhost';
$username = 'root';
$password = ''; // Use your database password
$database = 'dct-ccs-finals';

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



















?>
