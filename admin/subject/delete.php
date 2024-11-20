<?php
ob_start(); // Start output buffering

include '../../functions.php'; // Include the functions
include '../partials/header.php';

$logoutPage = '../logout.php';
$dashboardPage = '../dashboard.php';
$studentPage = '../student/register.php';
$subjectPage = './add.php';
include '../partials/side-bar.php';

// Check if subject_code is provided in the URL
if (isset($_GET['subject_code']) && !empty($_GET['subject_code'])) {
    $subject_code = $_GET['subject_code'];

    // Fetch subject data by subject_code
    $subject_data = getSubjectByCode($subject_code);

    // If subject data exists, proceed with deletion
    if ($subject_data) {
        if (isPost()) {
            // Delete the subject from the database and redirect
            deleteSubject($subject_data['subject_code'], './add.php');
        }
    } else {
        // Subject not found in the database
        $error = "Subject not found.";
    }
} else {
    // No subject_code provided
    $error = "No subject code provided.";
}
?>

<div class="col-md-9 col-lg-10">
    <h3 class="text-left mb-5 mt-5">Delete Subject</h3>

    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="add.php">Add subject</a></li>
            <li class="breadcrumb-item active" aria-current="page">Delete Subject</li>
        </ol>
    </nav>

    <!-- Display error message if there's an issue -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($subject_data)): ?>
        <!-- Confirmation Message -->
        <p class="text-left">Are you sure you want to delete the following subject record?</p>
        <ul class="text-left">
            <li><strong>Subject Code:</strong> <?= htmlspecialchars($subject_data['subject_code']) ?></li>
            <li><strong>Subject Name:</strong> <?= htmlspecialchars($subject_data['subject_name']) ?></li>
        </ul>

        <!-- Confirmation Form -->
        <form method="POST" class="text-left">
            <a href="add.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Delete Subject Record</button>
        </form>
    <?php endif; ?>
</div>

<?php
include '../partials/footer.php';

ob_end_flush(); // End output buffering and send the content to the browser
?>
