<?php
include '../../functions.php'; // Include functions file
include '../partials/header.php';

// Define navigation paths
$logoutPage = '../logout.php';
$dashboardPage = '../dashboard.php';
$studentPage = 'register.php';  // Fixed relative path
$subjectPage = './subject/add.php';
include '../partials/side-bar.php';

// Fetch student data based on student_id from GET
$student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;

if ($student_id) {
    // Fetch student information using the getStudentById function
    $student_data = getStudentById($student_id);
    if (!$student_data) {
        echo "Student not found.";
    }
} else {
    echo "No student ID provided.";
}

?>

<div class="col-md-9 col-lg-10">
    <h3 class="text-left mb-5 mt-5">Attach Subject to Student</h3>

    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Attach Subject</li>
        </ol>
    </nav>

    <!-- Display student info -->
    <?php if ($student_data): ?>
    <div class="border p-5">
        <p class="text-left fs-4">Selected Student Information</p>
        <ul class="text-left">
            <li><strong>Student ID:</strong> <?= htmlspecialchars($student_data['student_id']) ?></li>
            <li><strong>Name:</strong> <?= htmlspecialchars($student_data['first_name']) . ' ' . htmlspecialchars($student_data['last_name']) ?></li>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Form for attaching subjects (example) -->
    <form method="POST" action="assign-grade.php">
        <label for="subject_id">Select Subject</label>
        <select name="subject_id" id="subject_id" class="form-control">
            <!-- Example subjects, this should be dynamically generated from your subjects table -->
            <option value="1">Math 101</option>
            <option value="2">English 102</option>
        </select>
        
        <button type="submit" class="btn btn-primary mt-3">Attach Subject</button>
    </form>
</div>

<?php include '../partials/footer.php'; ?>
