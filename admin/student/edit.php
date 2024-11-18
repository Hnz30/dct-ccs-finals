<?php
session_start();
$pageTitle = "Edit Student";
include '../partials/header.php';  // Include header
include '../../functions.php';

// Check if the student index is provided in the URL
if (isset($_GET['index']) && is_numeric($_GET['index'])) {
    $index = $_GET['index'];
    // Make sure the student exists in the session
    if (!isset($_SESSION['student_data'][$index])) {
        header("Location: register.php");  // Redirect if the student data is not found
        exit;
    }
    $student = $_SESSION['student_data'][$index]; // Get the student data from the session
} else {
    // If index is not found, redirect to the register page or show an error message
    header("Location: register.php");
    exit;
}

// Process the form submission for editing the student
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get updated data from the form (excluding the student_id)
    $updated_data = [
        'student_id' => $student['student_id'],  // Keep the original student_id
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name']
    ];

    // Update the student data in the session directly
    $_SESSION['student_data'][$index] = $updated_data;

    // Redirect to register page after saving changes
    header("Location: register.php");
    exit;
}
?>

<main>
    <div class="container justify-content-between align-items-center col-8">
        <h2 class="m-4">Edit Student</h2>

        <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
            <div class="mb-3">
                <label for="student_id" class="form-label">Student ID</label>
                <!-- Set the student ID field to readonly -->
                <input type="number" class="form-control" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>" required readonly>
            </div>

            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</main>

<?php include '../partials/footer.php'; ?>
