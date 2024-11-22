<?php
// findmore.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "college_project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the category from the URL, default to 'all'
$category = isset($_GET['category']) ? $_GET['category'] : 'all';

// Prepare SQL query using prepared statements
if ($category === 'all') {
    $sql = "SELECT * FROM colleges";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT * FROM colleges WHERE category = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category);
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

$colleges = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $colleges[] = $row;
    }
}

// Close connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find More Colleges - <?= ucfirst($category) ?></title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card {
            margin: 10px;
            border-radius: 15px;
            background-color: #f8f9fa;
            height: 100%;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card img {
            border-radius: 15px 15px 0 0;
            height: 200px;
            object-fit: cover;
        }
        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
        }
        .card-text {
            font-size: 0.95rem;
            color: #6c757d;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        @media (max-width: 768px) {
            .col-md-3 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="my-4"><?= ucfirst($category) ?> Colleges</h1>
        <div class="row">
            <?php foreach ($colleges as $college): ?>
                <div class="col-md-3 p-1">
                    <div class="card">
                        <?php 
                        $baseDir = 'uploads/';
                        $imagePath = $baseDir . htmlspecialchars($college['image']);
                        ?>
                        <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($college['name']) ?>" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($college['name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($college['description']) ?></p>
                            <a href="learnmore.php?id=<?= urlencode($college['id']) ?>" class="btn btn-primary">Learn More</a> <!-- Link to college details page -->
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
