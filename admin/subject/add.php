<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

include '../../functions.php'; // Include your functions file
include '../partials/header.php'; // Include header here
include '../partials/side-bar.php';

// Handle the form submission to add a new subject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $subject_code = postData('subject_code'); // Sanitize input data
    $subject_name = postData('subject_name');

    // Validate form inputs
    if ($subject_code && $subject_name) {
        // Check if subject code already exists
        $conn = dbConnect();
        $query = "SELECT COUNT(*) AS count FROM subjects WHERE subject_code = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $subject_code);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        // If subject code already exists
        if ($row['count'] > 0) {
            $error_message = "Subject Code already exists. Please choose a different code.";
        } else {
            // Insert the subject into the database
            $query = "INSERT INTO subjects (subject_code, subject_name) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $subject_code, $subject_name);

            if ($stmt->execute()) {
                $success_message = "Subject added successfully!";
            } else {
                $error_message = "Failed to add subject.";
            }

            $stmt->close();
        }
        
        $conn->close();
    } else {
        $error_message = "Please fill in both fields.";
    }
}
?>

<!-- Template Files here -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">    
    <h1 class="h2">Add a New Subject</h1>
    <div class="w-100 mt-1">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add a New Subject</li>
        </ol>
    </nav>
</div>
        

    <?php if (isset($success_message)) echo "<div class='alert alert-success'>$success_message</div>"; ?>
    <?php if (isset($error_message)) echo "<div class='alert alert-danger'>$error_message</div>"; ?>

    <div class="row mt-5">
        <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
            <!-- Floating Label for Subject Code -->
            <div class="form-floating mb-3">
                <input type="number" class="form-control bg-light" id="subject_code" name="subject_code" placeholder="Subject Code" >
                <label for="subject_code">Subject Code</label>
            </div>

            <!-- Floating Label for Subject Name -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control bg-light" id="subject_name" name="subject_name" placeholder="Subject Name" >
                <label for="subject_name">Subject Name</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Add Subject</button>
        </form>

        <!-- List of Registered Subjects from Database -->             
        <div class="border border-secondary-1 p-5">
            <h5>Subject List</h5>
            <hr>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch subjects from the database using the fetchSubjects function
                    $subjects = fetchSubjects();
                    foreach ($subjects as $subject):
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($subject['subject_code']); ?></td>
                            <td><?= htmlspecialchars($subject['subject_name']); ?></td>
                            <td>
                                <!-- Edit Button -->
                                <a href="edit.php?subject_code=<?= $subject['subject_code']; ?>" class="btn btn-info btn-sm">Edit</a>

                                <!-- Delete Button -->
                                <a href="delete.php?subject_code=<?= $subject['subject_code']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>    
</main>

<?php
include '../partials/footer.php'; // Include footer here
?>
