<?php
include '../../functions.php'; // Include the functions
include '../partials/header.php';

// Define navigation paths
$logoutPage = '../logout.php';
$dashboardPage = '../dashboard.php';
$studentPage = '../student/register.php';
$subjectPage = './add.php';
include '../partials/side-bar.php';

// Retrieve the subject by its code
$subject_data = getSubjectByCode($_GET['subject_code']);
?>

<div class="col-md-9 col-lg-10">
    <h3 class="text-left mb-5 mt-5">Edit Subject</h3>

    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="add.php">Add Subject</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Subject</li>
        </ol>
    </nav>

    <!-- Update Subject Logic -->
    <?php
    if (isPost()) {
        $subject_code = $subject_data['subject_code']; // Use the current subject code
        $subject_name = postData('subject_name'); // Get the updated subject name
        updateSubject($subject_code, $subject_name, "./add.php");
    }
    ?>

    <!-- Edit Subject Form -->
    <div class="card p-4 mb-5">
        <form method="POST">
            <!-- Subject Code (disabled) -->
            <div class="mb-3">
                <label for="subject_code" class="form-label">Subject Code</label>
                <input type="text" class="form-control" id="subject_code" name="subject_code" value="<?= htmlspecialchars($subject_data['subject_code']) ?>" disabled>
            </div>

            <!-- Subject Name -->
            <div class="mb-3">
                <label for="subject_name" class="form-label">Subject Name</label>
                <input type="text" class="form-control" id="subject_name" name="subject_name" value="<?= htmlspecialchars($subject_data['subject_name']) ?>">
            </div>

            <!-- Update Button -->
            <button type="submit" class="btn btn-primary btn-sm w-100">Update Subject</button>
        </form>
    </div>
</div>

<?php
include '../partials/footer.php';
?>
