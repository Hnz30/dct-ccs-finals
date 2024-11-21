<?php
include '../../functions.php'; 
include '../partials/header.php';

$logoutPage = '../logout.php';
$dashboardPage = '../dashboard.php';
$studentPage = '../student/register.php';
$subjectPage = './subject/add.php';
include '../partials/side-bar.php';

// Check if student_id is passed in the URL
if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    // Fetch student information
    $student_data = getStudentById($student_id);
    if (!$student_data) {
        echo "Student not found.";
        exit();
    }
} else {
    echo "No student ID provided.";
    exit();
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

    <!-- Form Handling -->
    <?php
    if (isPost()) {
    // Check if any subjects were selected
        if (isset($_POST['subjects']) && !empty($_POST['subjects'])) {
        $subjects = $_POST['subjects'];
        
        // Assign subjects to student and check for success
        $result = assignSubjectsToStudent($student_data['student_id'], $subjects);
        
        if ($result) {
            echo displayMessage("Subjects successfully assigned to the student!");
        } else {
            echo displayErrors(["Failed to assign subjects. Please try again."]);
        }
    } else {
        echo displayErrors(["No subjects selected."]);
    }
}
?>


    <div class="border p-5">
        <!-- Display Selected Student Information -->
        <h4 class="text-left mb-2 mt-5">Selected Student Information</h4>
        <ul class="text-left">
            <li><strong>Student ID:</strong> <?= htmlspecialchars($student_data['student_id']) ?></li>
            <li><strong>Name:</strong> <?= htmlspecialchars($student_data['first_name']) .' '. htmlspecialchars($student_data['last_name']) ?></li>
        </ul>
        <hr>

        <!-- Subject Selection Form -->
        <form method="POST" class="text-left">
            <h5>Select Subjects:</h5>
            <?php
                // Display checkboxes for all available subjects
                echo getAllSubjectsCheckboxes($student_data['student_id']);
            ?>
            <button type="submit" class="btn btn-primary mt-3">Attach Subjects</button>
        </form>
    </div>

    <!-- Display Assigned Subjects -->
    <div class="card p-4 mt-5 mb-5">
        <h3 class="card-title text-left">Assigned Subjects</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Grade</th>
                    <th>Option</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            // Fetch assigned subjects
            $assignedSubjects = fetchAssignSubjects($student_data['student_id']);
            if (!empty($assignedSubjects)): ?>
                <?php foreach ($assignedSubjects as $subject): ?>
                    <tr>
                        <td><?= htmlspecialchars($subject['subject_code']) ?></td>
                        <td><?= htmlspecialchars($subject['subject_name']) ?></td>
                        <td><?= htmlspecialchars($subject['grade']) ?></td>
                        <td>
                        <?php
                            // Ensure that subject_id and student_id are set and valid before using them
                            $subject_id = isset($subject['subject_id']) ? urlencode($subject['subject_id']) : '';
                            $student_id = isset($student_data['student_id']) ? urlencode($student_data['student_id']) : '';
                            ?>

                            <a href="dettach-subject.php?subject_id=<?= isset($subject['subject_id']) ? urlencode($subject['subject_id']) : '' ?>&student_id=<?= isset($student_data['student_id']) ? urlencode($student_data['student_id']) : '' ?>" class="btn btn-danger btn-sm">Dettach Subject</a>
                            <a href="assign-grade.php?subject_id=<?= $subject_id ?>&student_id=<?= $student_id ?>" class="btn btn-success btn-sm">Assign Grade</a>

                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No subjects assigned.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php
include '../partials/footer.php';
?>
