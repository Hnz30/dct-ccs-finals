<?php
session_start();
ob_start(); // Start output buffering to avoid premature output

// Database connection
$conn = new mysqli('localhost', 'root', '', 'dct-ccs-finals');

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

// Ensure student data exists in the session
if (!isset($_SESSION['student_data'])) {
    $_SESSION['student_data'] = [];
}

// Check if a valid index is passed
if (isset($_GET['index']) && is_numeric($_GET['index'])) {
    $index = (int)$_GET['index']; // Sanitize input

    // Ensure the student exists in the session
    if (!isset($_SESSION['student_data'][$index])) {
        header("Location: register.php"); // Redirect if the student doesn't exist
        exit();
    }

    // Get the student data for display
    $student = $_SESSION['student_data'][$index];
} else {
    header("Location: register.php"); // Redirect if no index is provided
    exit();
}

// Handle form submission for deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete the student from the database
    $student_id = $student['student_id']; // Use student_id from the session data
    
    // Prepare the SQL delete query
    $sql = "DELETE FROM students WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $student_id); // 's' is for string (student_id)

    if ($stmt->execute()) {
        // After successful deletion, redirect back to the register page
        header("Location: register.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

ob_end_flush(); // Flush the output buffer to the browser
?>

<?php include '../partials/header.php'; // Include header ?>
<?php include '../partials/side-bar.php'; // Include sidebar ?>
<div class="col-md-9 col-lg-10">

<h3 class="text-left mb-5 mt-5">Delete a Student</h3>

    <!-- Breadcrumbs Section -->
    <div class="w-100 mt-1">
        <div class="border border-secondary-1 p-10 mb-1">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="register.php">Register Students</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Delete Student</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Delete Student Form -->
    <div class="row mt-5">
        <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
            <div class="mb-2">
                <label class="form-label fs-5">Are you sure you want to delete the following student record?</label> 
                <ul style="list-style-type:disc;">
                    <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></li>
                    <li><strong>First Name:</strong> <?php echo htmlspecialchars($student['first_name']); ?></li>
                    <li><strong>Last Name:</strong> <?php echo htmlspecialchars($student['last_name']); ?></li>
                </ul>

                <!-- Buttons for Submit and Cancel -->
                <div>
                    <a href="register.php" class="btn btn-secondary btn-m">Cancel</a> 
                    <button type="submit" class="btn btn-primary btn-m">Delete Student Record</button>
                </div>  
            </div>
        </form>
    </div>    
</main>

<?php include '../partials/footer.php'; // Include footer ?>
