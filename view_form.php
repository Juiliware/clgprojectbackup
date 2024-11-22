<?php
// Database connection (adjust based on your DB settings)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "college_project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming $id is passed via URL or form
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// Fetching data from the database based on the ID
$sql = "SELECT * FROM biodata WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Directory where files are uploaded
$uploadDirectory = "uploads/";

// Check if the form is in view or update mode
$viewMode = isset($_GET['view']) && $_GET['view'] == 'true';

// Ensure the necessary keys exist in the row array with default empty values if missing
$fields = [
    'aadhar', 'lc', 'marksheet', 'income_cert', 'caste_cert', 'dob', 
    'mother_name', 'caste', 'income', 'name', 'previous_college', 'father_name', 'address', 'gender'
];

foreach ($fields as $field) {
    if (!isset($row[$field])) {
        $row[$field] = '';  // Set default empty value if the field does not exist
    }
}

// Handle form submission if it's an update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$viewMode) {
    // Updating text fields
    $name = $_POST['name'];
    $previous_college = $_POST['previous_college'];
    $dob = $_POST['dob'];
    $father_name = $_POST['father_name'];
    $mother_name = $_POST['mother_name'];
    $caste = $_POST['caste'];
    $income = $_POST['income'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];

    // Handle file uploads and retain old file names if no new file is uploaded
    $aadhar = $_FILES['aadhar']['name'] ? $_FILES['aadhar']['name'] : $row['aadhar'];
    $lc = $_FILES['lc']['name'] ? $_FILES['lc']['name'] : $row['lc'];
    $marksheet = $_FILES['marksheet']['name'] ? $_FILES['marksheet']['name'] : $row['marksheet'];
    $income_cert = $_FILES['income_cert']['name'] ? $_FILES['income_cert']['name'] : $row['income_cert'];
    $caste_cert = $_FILES['caste_cert']['name'] ? $_FILES['caste_cert']['name'] : $row['caste_cert'];

    // Moving uploaded files to the server
    if ($_FILES['aadhar']['tmp_name']) move_uploaded_file($_FILES['aadhar']['tmp_name'], $uploadDirectory . $aadhar);
    if ($_FILES['lc']['tmp_name']) move_uploaded_file($_FILES['lc']['tmp_name'], $uploadDirectory . $lc);
    if ($_FILES['marksheet']['tmp_name']) move_uploaded_file($_FILES['marksheet']['tmp_name'], $uploadDirectory . $marksheet);
    if ($_FILES['income_cert']['tmp_name']) move_uploaded_file($_FILES['income_cert']['tmp_name'], $uploadDirectory . $income_cert);
    if ($_FILES['caste_cert']['tmp_name']) move_uploaded_file($_FILES['caste_cert']['tmp_name'], $uploadDirectory . $caste_cert);

    // Update query for text fields and documents
    $updateSql = "UPDATE biodata SET name = ?, previous_college = ?, dob = ?, father_name = ?, mother_name = ?, caste = ?, income = ?, address = ?, gender = ?, aadhar = ?, lc = ?, marksheet = ?, income_cert = ?, caste_cert = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ssssssisssssssi", $name, $previous_college, $dob, $father_name, $mother_name, $caste, $income, $address, $gender, $aadhar, $lc, $marksheet, $income_cert, $caste_cert, $id);
    $updateStmt->execute();

    echo "Data updated successfully!";
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $viewMode ? 'View Application Form' : 'Update Application Form'; ?></title>
    <link rel="stylesheet" href="path/to/your/css/file.css">
</head>
<body>

<div id="applicationFormContent" class="content-section">
    <h1 class="text-2xl font-semibold text-blue-600"><?php echo $viewMode ? 'View Application Form' : 'Update Application Form'; ?></h1>
    <form method="POST" enctype="multipart/form-data">
        <table style="width: 80%; border: 1px solid #ddd; border-collapse: collapse; margin: 0 auto; padding: 15px;">
            <!-- Personal Information Section -->
<tr><th colspan="2" style="background-color: #f2f2f2; padding: 10px; text-align: left;">Personal Information</th></tr>
<tr><td style="padding: 10px;"><strong>Full Name:</strong></td>
<td style="padding: 10px;">
    <?php if ($viewMode) { echo $row['name']; } else { ?>
        <input type="text" name="name" value="<?php echo $row['name']; ?>" <?php echo $viewMode ? 'readonly' : ''; ?> required />
    <?php } ?>
</td></tr>

            <tr><td style="padding: 10px;"><strong>Date of Birth:</strong></td><td style="padding: 10px;">
    <?php if ($viewMode) { echo $row['dob']; } else { ?>
        <input type="date" name="dob" value="<?php echo $row['dob']; ?>" <?php echo $viewMode ? 'readonly' : ''; ?> required />
    <?php } ?>
</td></tr>
<tr><td style="padding: 10px;"><strong>Gender:</strong></td><td style="padding: 10px;">
    <?php if ($viewMode) { echo $row['gender']; } else { ?>
        <select name="gender" <?php echo $viewMode ? 'disabled' : ''; ?> required>
            <option value="Male" <?php echo ($row['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
            <option value="Female" <?php echo ($row['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
            <option value="Other" <?php echo ($row['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
        </select>
    <?php } ?>
</td></tr>

<!-- Parent's Details Section -->
<tr><th colspan="2" style="background-color: #f2f2f2; padding: 10px; text-align: left;">Parent's Details</th></tr>
<tr><td style="padding: 10px;"><strong>Father's Name:</strong></td><td style="padding: 10px;">
    <?php if ($viewMode) { echo $row['father_name']; } else { ?>
        <input type="text" name="father_name" value="<?php echo $row['father_name']; ?>" <?php echo $viewMode ? 'readonly' : ''; ?> required />
    <?php } ?>
</td></tr>
<tr><td style="padding: 10px;"><strong>Mother's Name:</strong></td><td style="padding: 10px;">
    <?php if ($viewMode) { echo $row['mother_name']; } else { ?>
        <input type="text" name="mother_name" value="<?php echo $row['mother_name']; ?>" <?php echo $viewMode ? 'readonly' : ''; ?> required />
    <?php } ?>
</td></tr>

<!-- Address Section -->
<tr><th colspan="2" style="background-color: #f2f2f2; padding: 10px; text-align: left;">Address</th></tr>
<tr><td colspan="2" style="padding: 10px;">
    <?php if ($viewMode) { echo nl2br($row['address']); } else { ?>
        <textarea name="address" <?php echo $viewMode ? 'readonly' : ''; ?> required><?php echo $row['address']; ?></textarea>
    <?php } ?>
</td></tr>

<!-- Caste and Income Section -->
<tr><th colspan="2" style="background-color: #f2f2f2; padding: 10px; text-align: left;">Caste and Income</th></tr>
<tr><td style="padding: 10px;"><strong>Caste:</strong></td><td style="padding: 10px;">
    <?php if ($viewMode) { echo $row['caste']; } else { ?>
        <input type="text" name="caste" value="<?php echo $row['caste']; ?>" <?php echo $viewMode ? 'readonly' : ''; ?> required />
    <?php } ?>
</td></tr>
<tr><td style="padding: 10px;"><strong>Income:</strong></td><td style="padding: 10px;">
    <?php if ($viewMode) { echo $row['income']; } else { ?>
        <input type="number" name="income" value="<?php echo $row['income']; ?>" <?php echo $viewMode ? 'readonly' : ''; ?> required />
    <?php } ?>
</td></tr>

<!-- Previous College Section -->
<tr><th colspan="2" style="background-color: #f2f2f2; padding: 10px; text-align: left;">Previous College</th></tr>
<tr><td style="padding: 10px;"><strong>Previous College:</strong></td><td style="padding: 10px;">
    <?php if ($viewMode) { echo $row['previous_college']; } else { ?>
        <input type="text" name="previous_college" value="<?php echo $row['previous_college']; ?>" <?php echo $viewMode ? 'readonly' : ''; ?> required />
    <?php } ?>
</td></tr>


            <!-- Documents Section (side by side) -->
            <tr><th colspan="2" style="background-color: #f2f2f2; padding: 10px; text-align: left;">Uploaded Documents</th></tr>
            <tr>
                <td><strong>Aadhar Card:</strong><br>
                    <?php if ($row['aadhar']) { ?>
                        <img src="<?php echo $uploadDirectory . $row['aadhar']; ?>" alt="Aadhar" width="150" height="200">
                    <?php } else { echo "No file uploaded"; } ?>
                    <?php if (!$viewMode) { ?><input type="file" name="aadhar" /><?php } ?>
                </td>
                <td><strong>Leaving Certificate (LC):</strong><br>
                    <?php if ($row['lc']) { ?>
                        <img src="<?php echo $uploadDirectory . $row['lc']; ?>" alt="LC" width="150" height="200">
                    <?php } else { echo "No file uploaded"; } ?>
                    <?php if (!$viewMode) { ?><input type="file" name="lc" /><?php } ?>
                </td>
            </tr>
            <!-- <tr><th colspan="2" style="background-color: #f2f2f2; padding: 10px; text-align: left;">Uploaded Documents</th></tr> -->
<tr><td style="padding: 10px;">
    <strong>Marksheet:</strong><br>
    <?php if ($row['marksheet']) { ?>
        <img src="<?php echo $uploadDirectory . $row['marksheet']; ?>" alt="Marksheet" width="150" height="200">
    <?php } else { echo "No file uploaded"; } ?>
    <?php if (!$viewMode) { ?>
        <input type="file" name="marksheet" />
    <?php } ?>
</td>
<td style="padding: 10px;">
    <strong>Income Certificate:</strong><br>
    <?php if ($row['income_cert']) { ?>
        <img src="<?php echo $uploadDirectory . $row['income_cert']; ?>" alt="Income Certificate" width="150" height="200">
    <?php } else { echo "No file uploaded"; } ?>
    <?php if (!$viewMode) { ?>
        <input type="file" name="income_cert" />
    <?php } ?>
</td></tr>
<tr><td style="padding: 10px;">
    <strong>Caste Certificate:</strong><br>
    <?php if ($row['caste_cert']) { ?>
        <img src="<?php echo $uploadDirectory . $row['caste_cert']; ?>" alt="Caste Certificate" width="150" height="200">
    <?php } else { echo "No file uploaded"; } ?>
    <?php if (!$viewMode) { ?>
        <input type="file" name="caste_cert" />
    <?php } ?>
</td></tr>


            <!-- Submit or Cancel Buttons -->
            <tr>
                <td colspan="2" style="text-align: center; padding: 10px;">
                    <?php if (!$viewMode) { ?>
                        <button type="submit" style="background-color: blue; color: white;">Update</button>
                    <?php } else { ?>
                        <button type="button" onclick="window.history.back();" style="background-color: grey; color: white;">Back</button>
                    <?php } ?>
                </td>
            </tr>
        </table>
        <a href="userdashboard.php">back
    </form>
</div>

</body>
</html>
