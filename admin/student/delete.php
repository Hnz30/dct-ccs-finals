<?php
session_start();
$pageTitle = "Delete Student";
include '../partials/header.php';  // Include header
include '../../functions.php';     // Correct the path to functions.php

// Retrieve student data using index from session or redirect if not found
if (isset($_GET['index']) && is_numeric($_GET['index'])) {
    $index = $_GET['index'];

    // Get the student data by index
    $student = getSelectedStudentData($index); // This function needs to be defined in functions.php
    if (!$student) {
        // If the student is not found, redirect to the register page
        header("Location: register.php");
        exit;
    }
} else {
    // If no index is provided in the URL, redirect to the register page
    header("Location: register.php");
    exit;
}

// Handle form submission to delete student data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete student from the session data
    unset($_SESSION['student_data'][$index]);
    // Reindex the array after deletion to avoid gaps in the session array
    $_SESSION['student_data'] = array_values($_SESSION['student_data']);
    
    // Redirect back to the register page after deletion
    header("Location: register.php");
    exit;
}
?>

<main>
    <div class="container justify-content-between align-items-center col-6">
        
        <h3 class="mt-4">Delete a Student</h3>

        <!-- Breadcrumb Navigation -->
        <div class="w-100 mt-5">
            <div class="container justify-content-between align-items-center bg-light p-2 border r-4 ">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Delete Student</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="border border-secondary-1 p-4 mt-3">
            <!-- Confirm Deletion Form -->
            <form method="POST" action="">
                <div class="mb-2">
                    <label class="form-label">Are you sure you want to delete the following student record?</label> 
                    <ul style="list-style-type:disc;">
                        <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></li>
                        <li><strong>First Name:</strong> <?php echo htmlspecialchars($student['first_name']); ?></li>
                        <li><strong>Last Name:</strong> <?php echo htmlspecialchars($student['last_name']); ?></li>
                    </ul>
                    <!-- Buttons for Submit and Cancel -->
                    <div>
                        <a href="register.php" class="btn btn-secondary btn-sm">Cancel</a> <!-- Cancel button with gray background -->
                        <button type="submit" class="btn btn-primary btn-sm">Delete Student Record</button> <!-- Delete button -->
                    </div>  
                </div>
            </form>
        </div>
    </div>
</main>

<?php
include '../partials/footer.php';  // Include footer
?>
