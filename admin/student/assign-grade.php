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
        exit;
    }
} else {
    echo "No student ID provided.";
    exit;
}

// Handle form submission to assign grades
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = $_POST['subject_id'] ?? null;
    $grade = $_POST['grade'] ?? null;

    if ($subject_id && $grade !== null) {
        // Update grade for the student and subject
        $result = assignGradeToSubject($student_id, $subject_id, $grade);

        if ($result) {
            echo displayMessage("Grade successfully assigned to the subject!");
        } else {
            echo displayErrors(["Failed to assign grade. Please try again."]);
        }
    } else {
        echo displayErrors(["Please select a subject and enter a grade."]);
    }
}

// Fetch all available subjects or subjects already assigned to the student
$assignedSubjects = fetchAssignSubjects($student_id);
?>

<div class="col-md-9 col-lg-10">
    <h3 class="text-left mb-5 mt-5">Assign Grade to Subject</h3>

    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Assign Grade</li>
        </ol>
    </nav>

    <!-- Display student info -->
    <div class="border p-5">
        <p class="text-left fs-4">Selected Student Information</p>
        <ul class="text-left">
            <li><strong>Student ID:</strong> <?= htmlspecialchars($student_data['student_id']) ?></li>
            <li><strong>Name:</strong> <?= htmlspecialchars($student_data['first_name']) . ' ' . htmlspecialchars($student_data['last_name']) ?></li>
        </ul>
    </div>

    <!-- Form for assigning grades -->
    <form method="POST" action="assign-grade.php?student_id=<?= urlencode($student_data['student_id']) ?>" class="text-left">
        <label for="subject_id">Select Subject</label>
        <select name="subject_id" id="subject_id" class="form-control">
            <option value="">-- Select Subject --</option>
            <?php foreach ($assignedSubjects as $subject): ?>
                <option value="<?= htmlspecialchars($subject['subject_id']) ?>">
                    <?= htmlspecialchars($subject['subject_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="grade" class="mt-3">Enter Grade</label>
        <input type="text" name="grade" id="grade" class="form-control" placeholder="Enter Grade" required>

        <button type="submit" class="btn btn-primary mt-3">Assign Grade</button>
    </form>
</div>

<?php include '../partials/footer.php'; ?>

<?php
// Function to assign grade to a subject for the student
function assignGradeToSubject($student_id, $subject_id, $grade) {
    $conn = dbConnect();

    // Check if the student already has this subject
    $query = "SELECT * FROM students_subjects WHERE student_id = ? AND subject_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $student_id, $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the grade for the existing subject
        $updateQuery = "UPDATE students_subjects SET grade = ? WHERE student_id = ? AND subject_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sii", $grade, $student_id, $subject_id);
        $success = $stmt->execute();
    } else {
        // Insert a new record if the subject is not assigned yet
        $insertQuery = "INSERT INTO students_subjects (student_id, subject_id, grade) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("iis", $student_id, $subject_id, $grade);
        $success = $stmt->execute();
    }

    $stmt->close();
    $conn->close();

    return $success;
}
?>
