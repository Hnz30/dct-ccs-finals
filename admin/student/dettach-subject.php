<?php
include '../../functions.php'; // Include the functions
include '../partials/header.php';

// Include the database connection file
$conn = new mysqli('localhost', 'root', '', 'dct-ccs-finals'); // Adjust your DB credentials if necessary
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$logoutPage = '../logout.php';
$dashboardPage = '../dashboard.php';
$studentPage = '.register.php';
$subjectPage = './subject/add.php';
include '../partials/side-bar.php';

// Check if the form is submitted
if(isPost()){
    // Detach the subject from the student by calling the `detachSubject` function
    detachSubject(GETdata($conn, 'student_id'), GETdata($conn, 'subject_id'), 'attach-subject.php?student_id=' . GETdata($conn, 'student_id'));
}
?>

<div class="col-md-9 col-lg-10">
    <h3 class="text-left mb-5 mt-5">Delete a Subject</h3>

    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Delete Subject</li>
        </ol>
    </nav>

    <div class="border p-5">
        <!-- Confirmation Message -->
        <p class="text-left">Are you sure you want to detach this subject from this student record?</p>
        <ul class="text-left">
            <li><strong>Student ID:</strong> <?= htmlspecialchars(GETdata($conn, "student_id") ?: 'N/A') ?></li>
            <li><strong>First Name:</strong> <?= htmlspecialchars(GETdata($conn, "firstname") ?: 'N/A') ?></li>
            <li><strong>Last Name:</strong> <?= htmlspecialchars(GETdata($conn, "lastname") ?: 'N/A') ?></li>
            <li><strong>Subject Code:</strong> <?= htmlspecialchars(GETdata($conn, "subject_id") ?: 'N/A') ?></li>
            <li><strong>Subject Name:</strong> <?= htmlspecialchars(GETdata($conn, "subject_name") ?: 'N/A') ?></li>
        </ul>

        <!-- Confirmation Form -->
        <form method="POST" class="text-left">
            <a href="attach-subject.php?student_id=<?= htmlspecialchars(GETdata($conn, 'student_id')) ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Delete Subject from Student</button>
        </form>
    </div>
</div>

<?php
include '../partials/footer.php';
?>
