<?php
session_start();
include('db_connection.php');

$college_id = $_SESSION['college_id'];

// Fetch applications for this college
$query = "SELECT applications.*, users.first_name, users.last_name 
          FROM applications 
          JOIN users ON applications.student_id = users.user_id 
          WHERE college_id = '$college_id'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>College Dashboard</h1>
    
    <h2>Applications Received</h2>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="application-card">
            <p>Student: <?php echo $row['first_name']; ?> <?php echo $row['last_name']; ?></p>
            <p>Status: <?php echo $row['status']; ?></p>
            <form action="update_application_status.php" method="post">
                <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>">
                <select name="status">
                    <option value="Pending" <?php echo ($row['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="Approved" <?php echo ($row['status'] == 'Approved') ? 'selected' : ''; ?>>Approve</option>
                    <option value="Rejected" <?php echo ($row['status'] == 'Rejected') ? 'selected' : ''; ?>>Reject</option>
                </select>
                <button type="submit">Update Status</button>
            </form>
        </div>
    <?php endwhile; ?>

</body>
</html>
