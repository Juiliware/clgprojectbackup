<?php
session_start();
if ($_SESSION['role'] != 'superadmin') {
    header("Location: login.html");
    exit();
}

include 'db.php';

// Fetch all users
$users_sql = "SELECT * FROM users";
$users_result = mysqli_query($conn, $users_sql);

// Fetch all colleges
$colleges_sql = "SELECT * FROM colleges";
$colleges_result = mysqli_query($conn, $colleges_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Super Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Super Admin Dashboard</h2>
    <hr>

    <!-- User Management Section -->
    <h4>Manage Users</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = mysqli_fetch_assoc($users_result)) { ?>
                <tr>
                    <td><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></td>
                    <td><?php echo ucfirst($user['role']); ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['mobile_number']; ?></td>
                    <td>
                        <!-- Actions such as edit/delete could be added here -->
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- College Management Section -->
    <h4>Manage Colleges</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>College Name</th>
                <th>Type</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($college = mysqli_fetch_assoc($colleges_result)) { ?>
                <tr>
                    <td><?php echo $college['college_name']; ?></td>
                    <td><?php echo ucfirst($college['type']); ?></td>
                    <td><?php echo $college['description']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
