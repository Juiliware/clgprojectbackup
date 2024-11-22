<?php
session_start();
include('db_config.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['admin_name'])) {
    header("Location: admin_login.php");
    exit();
}

$adminName = $_SESSION['admin_name'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['name'], $_POST['description'], $_FILES['image']['name'], $_POST['category'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $category = trim($_POST['category']);
        $image = $_FILES['image']['name'];
        $target = "uploads/" . basename($image);

        if (!file_exists('uploads')) {
            mkdir('uploads', 0777, true);
        }

        if (!empty($image) && $_FILES['image']['error'] == 0) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $sql = "INSERT INTO colleges (image, name, description, category) VALUES (?, ?, ?, ?)";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("ssss", $image, $name, $description, $category);
                    if ($stmt->execute()) {
                        $_SESSION['alert_message'] = "College added successfully.";
                        header("Location: {$_SERVER['PHP_SELF']}");
                        exit();
                    } else {
                        $_SESSION['alert_message'] = "Error adding college: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $_SESSION['alert_message'] = "Database error: " . $conn->error;
                }
            } else {
                $_SESSION['alert_message'] = "Image upload failed. Please select a valid image.";
            }
        } else {
            $_SESSION['alert_message'] = "Image upload failed with error code: " . $_FILES['image']['error'];
        }
    }

    if (isset($_POST['update_id'], $_POST['update_name'], $_POST['update_description'], $_POST['update_category'])) {
        $updateId = intval($_POST['update_id']);
        $updateName = trim($_POST['update_name']);
        $updateDescription = trim($_POST['update_description']);
        $updateCategory = trim($_POST['update_category']);

        if (!empty($_FILES['update_image']['name'])) {
            $updateImage = $_FILES['update_image']['name'];
            $target = "uploads/" . basename($updateImage);
            move_uploaded_file($_FILES['update_image']['tmp_name'], $target);
            $sql = "UPDATE colleges SET name = ?, description = ?, category = ?, image = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $updateName, $updateDescription, $updateCategory, $updateImage, $updateId);
        } else {
            $sql = "UPDATE colleges SET name = ?, description = ?, category = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $updateName, $updateDescription, $updateCategory, $updateId);
        }

        if ($stmt->execute()) {
            $_SESSION['alert_message'] = "College updated successfully.";
            header("Location: {$_SERVER['PHP_SELF']}");
            exit();
        } else {
            $_SESSION['alert_message'] = "Error updating college: " . $stmt->error;
        }
        $stmt->close();
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM colleges WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['alert_message'] = "College deleted successfully.";
    } else {
        $_SESSION['alert_message'] = "Error deleting college: " . $stmt->error;
    }
    $stmt->close();
}

$colleges = fetchColleges($conn);
$conn->close();

function fetchColleges($conn) {
    $sql = "SELECT * FROM colleges";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.10.3/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar-toggle:checked ~ .sidebar {
            transform: translateX(-100%);
        }
        .notification-dropdown {
            z-index: 10;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-100 min-h-screen">
    <div class="flex">
        <input type="checkbox" id="sidebar-toggle" class="hidden sidebar-toggle">
        <aside class="w-64 bg-gray-800 text-white min-h-screen p-4 transition-transform transform sidebar">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">SuperAdmin</h2>
                <label for="sidebar-toggle" class="text-white cursor-pointer lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </label>
            </div>
            <ul class="space-y-1">
                <li onclick="showSection('dashboard')" class="flex items-center hover:bg-gray-700 p-2 rounded cursor-pointer transition duration-300 ease-in-out active:bg-gray-600">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </li>
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center justify-between w-full py-2 px-4 rounded hover:bg-gray-700 transition duration-200">
                        <i class="fas fa-book-open mr-2"></i> Courses
                        <span x-show="open">-</span><span x-show="!open">+</span>
                    </button>
                    <div x-show="open" class="pl-4 space-y-1 mt-1" x-cloak>
                        <a href="#" onclick="showSection('addCourse')" class="block py-1 hover:text-gray-300"><i class="fas fa-plus mr-2"></i> Add Course</a>
                        <a href="#" onclick="showSection('manageCourses')" class="block py-1 hover:text-gray-300"><i class="fas fa-cogs mr-2"></i> Manage Courses</a>
                    </div>
                </div>
                
                
            </ul>
        </aside>

               <div class="flex-grow">
            <header class="flex items-center justify-end bg-white shadow p-3 mb-6">
                <div class="relative flex items-center">
                   
                    Welcome, <?php echo htmlspecialchars($adminName); ?> <i class="fas fa-user-circle text-gray-600 text-2xl"></i> 
                    </button>
                    <div class="ml-6"></div>
                    <div class="relative cursor-pointer">
                        <button id="notificationButton" class="flex items-center text-xl text-gray-600">
                            🔔
                        </button>
                        <div id="notificationPanel" class="absolute right-0 mt-2 w-64 bg-white shadow-md rounded-lg p-4 hidden z-10">
                            <p class="font-bold">1 New Notification</p>
                            <p class="text-sm text-gray-600">Amit applied for admission</p>
                        </div>
                    </div>
                </div>
            </header>
     <!-- Dynamic Content Section -->
            <div id="dynamicContent" class="px-8">
                <!-- Dashboard Cards -->
                <section id="dashboard" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-6 hidden">
                    <div class="bg-white p-4 rounded-lg shadow text-center transform transition duration-500 hover:scale-105 hover:shadow-lg">
                        <h3 class="text-xl font-bold">12</h3>
                        <p class="text-gray-600">Listed Courses</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow text-center transform transition duration-500 hover:scale-105 hover:shadow-lg">
                        <h3 class="text-xl font-bold">5</h3>
                        <p class="text-gray-600">Registered Users</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow text-center transform transition duration-500 hover:scale-105 hover:shadow-lg">
                        <h3 class="text-xl font-bold">1</h3>
                        <p class="text-gray-600">Pending Applications</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow text-center transform transition duration-500 hover:scale-105 hover:shadow-lg">
                        <h3 class="text-xl font-bold">3</h3>
                        <p class="text-gray-600">Selected Students</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow text-center transform transition duration-500 hover:scale-105 hover:shadow-lg">
                        <h3 class="text-xl font-bold">7</h3>
                        <p class="text-gray-600">Rejected Applications</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow text-center transform transition duration-500 hover:scale-105 hover:shadow-lg">
                        <h3 class="text-xl font-bold">5</h3>
                        <p class="text-gray-600">Enquiries</p>
                    </div>
                </section>
                <div class="mb-4">
                    <?php if (isset($_SESSION['alert_message'])): ?>
                        <div class="bg-green-500 text-white p-4 rounded mb-4">
                            <?php echo $_SESSION['alert_message']; unset($_SESSION['alert_message']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <section id="addCourse" class="mb-6 hidden">
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h2 class="text-2xl font-semibold mb-4">Add College</h2>
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">College Name:</label>
                                <input type="text" name="name" required class="border rounded w-full p-2 mt-1 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="mb-4">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
                                <textarea name="description" required class="border rounded w-full p-2 mt-1 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="image" class="block text-sm font-medium text-gray-700">Image:</label>
                                <input type="file" name="image" required class="border rounded w-full p-2 mt-1 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="mb-4">
                                <label for="category" class="block text-sm font-medium text ```php
                                <label for="category" class="block text-sm font-medium text-gray-700">Category:</label>
                                <select name="category" required class="border rounded w-full p-2 mt-1 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="science">Science</option>
                                    <option value="commerce">Commerce</option>
                                    <option value="arts">Arts</option>
                                </select>
                            </div>
                            <div class="flex">
                                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition duration-200 mr-2">Add College</button>
                                <button type="button" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition duration-200" onclick="hideAddCollegeForm()">Cancel</button>
                            </div>
                        </form>
                    </div>
                </section>

                <section id="updateCollegeForm" class="mb-6 hidden">
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h2 class="text-2xl font-semibold mb-4">Update College</h2>
                        <form action="" method="POST" enctype="multipart/form-data">
                            <input type="hidden" id="update_id" name="update_id">
                            <div class="mb-4">
                                <label for="update_name" class="block text-sm font-medium text-gray-700">College Name:</label>
                                <input type="text" id="update_name" name="update_name" required class="border rounded w-full p-2 mt-1 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="mb-4">
                                <label for="update_description" class="block text-sm font-medium text-gray-700">Description:</label>
                                <textarea id="update_description" name="update_description" required class="border rounded w-full p-2 mt-1 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="update_image" class="block text-sm font-medium text-gray-700">Image:</label>
                                <input type="file" id="update_image" name="update_image" class="border rounded w-full p-2 mt-1 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="mb-4">
                                <label for="update_category" class="block text-sm font-medium text-gray-700">Category:</label>
                                <select id="update_category" name="update_category" required class="border rounded w-full p-2 mt-1 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="science">Science</option>
                                    <option value="commerce">Commerce</option>
                                    <option value="arts">Arts</option>
                                </select>
                            </div>
                            <div class="flex">
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-200 mr-2">Update College</button>
                                <button type="button" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition duration-200" onclick="hideUpdateForm()">Cancel</button>
                            </div>
                        </form>
                    </div>
                </section>

               <section id="manageCourses" class="mb-6 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Manage Colleges</h2>

        <!-- Filter Section -->
        <div class="mb-4">
            <label for="categoryFilter" class="block text-sm font-medium text-gray-700">Filter by Category:</label>
            <select id="categoryFilter" class="border rounded w-full p-2 mt-1 focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="filterColleges()">
                <option value="">All</option>
                <option value="science">Science</option>
                <option value="commerce">Commerce</option>
                <option value="arts">Arts</option>
            </select>
        </div>

        <?php if ($colleges->num_rows > 0): ?>
            <table class="min-w-full bg-white rounded-lg shadow-lg overflow-hidden">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-2 px-3 text-left text-sm">ID</th>
                        <th class="py-2 px-3 text-left text-sm">Image</th>
                        <th class="py-2 px-3 text-left text-sm">Name</th>
                        <th class="py-2 px-3 text-left text-sm w-48">Description</th> <!-- Limited width for Description -->
                        <th class="py-2 px-3 text-left text-sm w-32">Category</th> <!-- Limited width for Category -->
                        <th class="py-2 px-3 text-left text-sm w-28">Actions</th> <!-- Fixed width for Actions -->
                    </tr>
                </thead>
                <tbody class="bg-white" id="collegeTableBody">
                    <?php while ($row = $colleges->fetch_assoc()): ?>
                        <tr data-id="<?= $row['id']; ?>" class="hover:bg-gray-100 transition duration-200">
                            <td class="py-1 px-2 border-b border-gray-200 text-sm"><?= $row['id']; ?></td>
                            <td class="py-1 px-2 border-b border-gray-200 text-sm">
                                <img src="uploads/<?= $row['image']; ?>" alt="<?= $row['name']; ?>" class="h-12 w-12 object-cover rounded-lg shadow-sm">
                            </td>
                            <td class="py-1 px-2 border-b border-gray-200 text-sm"><?= $row['name']; ?></td>
                            <td class="py-1 px-2 border-b border-gray-200 text-sm break-words max-w-xs"><?= $row['description']; ?></td> <!-- Limited width for Description -->
                            <td class="py-1 px-2 border-b border-gray-200 text-sm"><?= $row['category']; ?></td>
                            <td class="py-1 px-2 border-b border-gray-200 text-sm">
                                <div class="flex space-x-2 justify-center">
                                    <!-- Update button with icon -->
                                    <button class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 transition duration-200 text-xs" onclick="showUpdateForm(<?= $row['id']; ?>)">
                                        <i class="fas fa-edit"></i> <!-- Edit icon -->
                                    </button>
                                    
                                    <!-- Delete button with icon -->
                                    <a href="?delete=<?= $row['id']; ?>" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 transition duration-200 text-xs">
                                        <i class="fas fa-trash-alt"></i> <!-- Trash icon -->
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-600">No colleges found.</p>
        <?php endif; ?>
    </div>
</section>

            </div>
        </div>
    </div>

    <script>
        function filterColleges() {
            const filterValue = document.getElementById('categoryFilter').value;
            const rows = document.querySelectorAll('#collegeTableBody tr');

            rows.forEach(row => {
                const category = row.cells[4].innerText.toLowerCase(); // Category is in the 5th cell (index 4)
                if (filterValue === "" || category === filterValue) {
                    row.style.display = ""; // Show row
                } else {
                    row.style.display = "none"; // Hide row
                }
            });
        }

        document.getElementById("notificationButton").addEventListener("click", function() {
            const notificationPanel = document.getElementById("notificationPanel");
            notificationPanel.classList.toggle("hidden");
        });

        function showSection(sectionId) {
            const sections = document.querySelectorAll('#dynamicContent > section');
            sections.forEach(section => section.classList.add('hidden'));
            document.getElementById(sectionId).classList.remove('hidden');
        }

        function hideAddCollegeForm() {
            document.getElementById('addCourse').classList.add('hidden');
        }

        function showUpdateForm(id) {
            const row = document.querySelector(`tr[data-id='${id}']`);
            const name = row.cells[2].innerText;
            const description = row.cells[3].innerText;
            const category = row.cells[4].innerText;

            document.getElementById('update_id').value = id;
            document.getElementById('update_name').value = name;
            document.getElementById('update_description').value = description;
            document.getElementById('update_category').value = category;

            document.getElementById('updateCollegeForm').classList.remove('hidden');
        }

        function hideUpdateForm() {
            document.getElementById('updateCollegeForm').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['alert_message'])): ?>
                alert("<?php echo addslashes($_SESSION['alert_message']); unset($_SESSION['alert_message']); ?>");
            <?php endif; ?>
        });
    </script>
</body>
</html>