<?php
include '../../functions.php'; // Include the functions
include '../partials/header.php';

// Include the database connection
$conn = new mysqli('localhost', 'root', '', 'dct-ccs-finals'); // Adjust your DB credentials if necessary
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$logoutPage = '../logout.php';
$dashboardPage = '../dashboard.php';
$studentPage = '.register.php';
$subjectPage = './subject/add.php';
include '../partials/side-bar.php';

// Fetch student and subject details based on student_id and subject_code
$student_id = GETdata($conn, 'student_id');
$subject_code = GETdata($conn, 'subject_code'); // Using subject_code

// Query to fetch student details
$student_query = "SELECT student_id, first_name, last_name FROM students WHERE student_id = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$student_result = $stmt->get_result();
$student = $student_result->fetch_assoc();
$stmt->close();

// Query to fetch subject details based on subject_code
$subject_query = "SELECT subject_code, subject_name FROM subjects WHERE subject_code = ?";
$stmt = $conn->prepare($subject_query);
$stmt->bind_param("s", $subject_code); // Use subject_code in the query
$stmt->execute();
$subject_result = $stmt->get_result();
$subject = $subject_result->fetch_assoc();
$stmt->close();

// Check if the subject exists for the student
if (!$subject) {
    $subject_error = "No subject found with the provided subject code.";
}

// Check if the form is submitted
if (isPost()) {
    // Detach the subject from the student
    detachSubject($student_id, $subject_code, 'attach-subject.php?student_id=' . $student_id);
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
            <li><strong>Student ID:</strong> <?= htmlspecialchars($student['student_id'] ?: 'N/A') ?></li>
            <li><strong>First Name:</strong> <?= htmlspecialchars($student['first_name'] ?: 'N/A') ?></li>
            <li><strong>Last Name:</strong> <?= htmlspecialchars($student['last_name'] ?: 'N/A') ?></li>

            <!-- Display Subject Info -->
            <?php if (isset($subject_error)): ?>
                <li><strong>Subject Code:</strong> <?= $subject_error ?></li>
                <li><strong>Subject Name:</strong> <?= $subject_error ?></li>
            <?php else: ?>
                <li><strong>Subject Code:</strong> <?= htmlspecialchars($subject['subject_code'] ?: 'N/A') ?></li>
                <li><strong>Subject Name:</strong> <?= htmlspecialchars($subject['subject_name'] ?: 'N/A') ?></li>
            <?php endif; ?>
        </ul>

        <!-- Confirmation Form -->
        <form method="POST" class="text-left">
            <a href="attach-subject.php?student_id=<?= htmlspecialchars($student['student_id']) ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Delete Subject from Student</button>
        </form>
    </div>
</div>

<?php
include '../partials/footer.php';
?>
