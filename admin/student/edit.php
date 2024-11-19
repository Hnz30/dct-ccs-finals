<?php
session_start();
ob_start(); // Start output buffering to avoid premature output

$pageTitle = "Edit Student";

// Database connection
$conn = new mysqli('localhost', 'root', '', 'dct-ccs-finals');

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Include header and sidebar
include '../partials/header.php';  // Include header
include '../../functions.php';
include '../partials/side-bar.php';

// Check if the student index is provided in the URL
if (isset($_GET['index']) && is_numeric($_GET['index'])) {
    $index = $_GET['index'];

    // Ensure the student exists in the session
    if (!isset($_SESSION['student_data'][$index])) {
        header("Location: register.php");  // Redirect if the student data is not found
        exit();
    }
    $student = $_SESSION['student_data'][$index]; // Get the student data from the session
} else {
    // If index is not found, redirect to the register page or show an error message
    header("Location: register.php");
    exit();
}

// Process the form submission for editing the student
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get updated data from the form
    $student_id = $_POST['student_id'];  // Student ID is passed in the form, but we won't change it
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];

    // Prepare the SQL update query using the primary key (`student_id`)
    $sql = "UPDATE students SET first_name = ?, last_name = ? WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $first_name, $last_name, $student_id);

    if ($stmt->execute()) {
        // After successful update, redirect to register page
        header("Location: register.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

ob_end_flush(); // Flush the output buffer to the browser
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5"> 
    <h2 class="m-4">Edit Student</h2>

        <!-- Breadcrumbs Section -->
        <div class="w-100 mt-1">
        <div class="border border-secondary-1 p-10 mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="register.php">Register Students</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Delete Student</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
        <div class="mb-3">
            <label for="student_id" class="form-label">Student ID</label>
            <!-- Set the student ID field to readonly since it won't change -->
            <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>" required readonly>
        </div>

        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
        </div>
        <a href="register.php" class="btn btn-secondary btn-m">Cancel</a> 
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</main>

<?php include '../partials/footer.php'; ?>
