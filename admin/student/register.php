<?php
session_start();
ob_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

include '../partials/header.php'; 
include '../partials/side-bar.php';
include '../../functions.php'; 

$errors = [];
$conn = dbConnect(); // Connect to the database

// Initialize the student data array if it doesn't exist
if (!isset($_SESSION['student_data'])) {
    $_SESSION['student_data'] = [];
}

// Process the form submission for registering a student
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get student data from the form
    $student_data = [
        'student_id' => trim($_POST['student_id']),
        'first_name' => trim($_POST['first_name']),
        'last_name' => trim($_POST['last_name'])
    ];

    // Validate input data
    $errors = validateStudentData($student_data);

    // Check for duplicate student ID in the database
    if (empty($errors)) {
        $errors = checkDuplicateStudentData($student_data['student_id'], $conn);
    }

    // If no errors, insert data into the database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO students (student_id, first_name, last_name) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $student_data['student_id'], $student_data['first_name'], $student_data['last_name']);
        
        if ($stmt->execute()) {
            // Clear output buffer before redirecting
            ob_end_clean();
            $_SESSION['success_message'] = 'Student registered successfully!';
            header("Location: register.php");
            exit();
        } else {
            $errors[] = 'Failed to add student. Please try again.';
        }
        $stmt->close();
    }
}

// Fetch all students from the database
$students = [];
$result = $conn->query("SELECT * FROM students");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Store the fetched students in the session so they are available on page reload
$_SESSION['student_data'] = $students;

$conn->close();

?>

<!-- Template Files here -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5"> 
    <?php 
    // Display errors if any
    if (!empty($errors)) {
        echo displayErrors($errors);
    }

    // Display success message if a student was registered successfully
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success">'. $_SESSION['success_message'] .'</div>';
        unset($_SESSION['success_message']);
    }
    ?>
   
    <h1 class="h2">Register a New Student</h1>        
    
    <div class="row mt-5">
        <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
            <!-- Floating Label for Student ID -->
            <div class="form-floating mb-3">
                <input type="number" class="form-control" id="student_id" name="student_id" 
                       placeholder="Student ID" value="<?php echo htmlspecialchars($student_data['student_id'] ?? ''); ?>" >
                <label for="student_id">Student ID</label>
            </div>

            <!-- Floating Label for First Name -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="first_name" name="first_name" 
                       placeholder="First Name" value="<?php echo htmlspecialchars($student_data['first_name'] ?? ''); ?>" >
                <label for="first_name">First Name</label>
            </div>

            <!-- Floating Label for Last Name -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="last_name" name="last_name" 
                       placeholder="Last Name" value="<?php echo htmlspecialchars($student_data['last_name'] ?? ''); ?>" >
                <label for="last_name">Last Name</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Add Student</button>
        </form>

        <!-- List of Registered Students with Gray Border -->             
        <div class="border border-secondary-1 p-5">
            <h5>Student List</h5>
            <hr>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($_SESSION['student_data']) && is_array($_SESSION['student_data'])): ?>
                        <?php foreach ($_SESSION['student_data'] as $index => $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                                <td>
                                    <!-- Edit Button -->
                                    <a href="edit.php?index=<?php echo $index; ?>" class="btn btn-info btn-sm">Edit</a>

                                    <!-- Delete Button -->
                                    <a href="delete.php?index=<?php echo $index; ?>" class="btn btn-danger btn-sm">Delete</a>

                                    <!-- Attach Subject -->
                                    <a href="attach-subject.php?index=<?php echo $index; ?>" class="btn btn-warning btn-sm">Attach Subject</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No students found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>    
</main>

<?php
include '../partials/footer.php'; 
ob_end_flush(); 
?>
