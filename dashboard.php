<?php
session_start();
include('db.php');

// Assuming the student's ID is stored in session
$student_id = $_SESSION['student_id']; 

// Fetch student profile status from the database
$query = "SELECT profile_status FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($profile_status);
$stmt->fetch();
$stmt->close();

// Fetch applied colleges from the database
$query = "SELECT college_name FROM applied_colleges WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($college_name);

$applied_colleges = [];
while ($stmt->fetch()) {
    $applied_colleges[] = $college_name;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Student Dashboard</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="#">Student Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Student Applied to Colleges</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">My College</a>
        </li>
        <!-- Applied Colleges List -->
        <li class="nav-item">
          <a class="nav-link" href="#">Applied Colleges: <?= implode(", ", $applied_colleges) ?></a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Profile Progress -->
<div class="container mt-5">
    <h2>Profile Completion</h2>
    <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: <?= $profile_status ?>%" aria-valuenow="<?= $profile_status ?>" aria-valuemin="0" aria-valuemax="100">
            <?= $profile_status ?>% Complete
        </div>
    </div>
</div>

<!-- Button to trigger the modal -->
<button type="button" class="btn btn-primary mt-4" data-bs-toggle="modal" data-bs-target="#collegeModal">
    Select Colleges
</button>

<!-- Modal for selecting colleges -->
<div class="modal fade" id="collegeModal" tabindex="-1" aria-labelledby="collegeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="collegeModalLabel">Select Colleges (Max 5)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="apply_colleges.php">
            <div class="form-group">
                <label for="colleges">Select Colleges</label>
                <select id="colleges" name="colleges[]" multiple class="form-control" size="5">
                    <?php
                    // Fetch list of colleges from the database
                    $college_query = "SELECT * FROM colleges";
                    $result = $conn->query($college_query);
                    while ($college = $result->fetch_assoc()) {
                        echo "<option value='{$college['name']}'>{$college['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
