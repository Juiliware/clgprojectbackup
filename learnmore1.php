<?php
// learnmore.php

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

// Check if the college ID is set in the URL
if (isset($_GET['id'])) {
    $college_id = $_GET['id'];

    // Fetch college data based on ID
    $sql = "SELECT * FROM colleges WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $college = $result->fetch_assoc();
    } else {
        echo "No college found with this ID.";
        exit();
    }
    $stmt->close();

    // Fetch unique categories for filtering gallery images
    $sql = "SELECT DISTINCT category FROM gallery WHERE college_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Prepare an array to store the categories
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
    $stmt->close();

    // Fetch facilities from the database for the specific college
    $sql = "SELECT * FROM facilities WHERE college_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Prepare an array to store facilities
    $facilities = [];
    while ($row = $result->fetch_assoc()) {
        $facilities[] = $row;
    }
    $stmt->close();

    // Fetch alumni data based on college ID
    $sql = "SELECT * FROM alumni WHERE college_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Prepare an array to store alumni data
    $alumni = [];
    while ($row = $result->fetch_assoc()) {
        $alumni[] = $row;
    }
    $stmt->close();

   // Fetch images from slideimg table
$sql = "SELECT image_path FROM slideimg";
$result = $conn->query($sql);

$images = [];
if ($result->num_rows > 0) {
    // Store images in an array
    while ($row = $result->fetch_assoc()) {
        $images[] = $row['image_path'];
    }
} else {
    echo "No images found in the slideimg table.";
    exit();
}

 // Fetch gallery images based on college ID
    $sql = "SELECT * FROM gallery WHERE college_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Prepare an array to store gallery images
    $gallery_images = [];
    while ($row = $result->fetch_assoc()) {
        $gallery_images[] = $row;
    }
    $stmt->close();
} else {
    echo "No ID provided.";
    exit();
}

// Close the connection after all queries are executed
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Information Portal</title>
       <!-- Tailwind CSS -->
       <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Custom styles */
        .grey-background {
            background-color: #f0f0f0;
            /* Light grey background */
        }

        .full-height {
            height: 100%;
            /* Ensure full height for grid items */
        }

        /* Fixed image size */
        .gallery-image {
            width: 100%;
            /* Full width within the grid item */
            height: 200px;
            /* Fixed height */
            object-fit: cover;
            /* Ensure the image covers the area without distortion */
            border-radius: 0.5rem;
            /* Rounded corners */
            transition: transform 0.3s ease;
            /* Smooth scaling transition */
        }

        /* Hover effect for gallery images */
        .gallery-item {
            position: relative;
            /* For positioning the overlay */
            overflow: hidden;
            /* To hide overflow */
        }

        .gallery-image:hover {
            transform: scale(1.05);
            /* Slight zoom effect */
        }

        /* Overlay for the image name */
        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 165, 0, 0.5);
            /* Orange overlay */
            color: white;
            /* Text color */
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            /* Start hidden */
            transition: opacity 0.3s ease;
            /* Smooth transition */
            border-radius: 0.5rem;
            /* Rounded corners for overlay */
        }

        .gallery-item:hover .image-overlay {
            opacity: 1;
            /* Show on hover */
        }

                /* Custom styles */
        .equal-height {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .mission-card,
        .vision-card {
            flex: 1; /* Allow both cards to grow equally */
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Space out content evenly */
        }

        .card-content {
            flex-grow: 1; /* Allow content to grow */
            display: flex;
            flex-direction: column;
            justify-content: center; /* Center content vertically */
            text-align: center; /* Center text */
        }

    </style>

</head>

<body class="bg-gray-100 text-gray-800">
   <!-- Header Section -->
<header style="z-index: 1; position: relative;">
    <div class="col-md-12 text-white bg-black py-2">
        <!-- Contact and Social Media -->
        <div class="container flex flex-wrap justify-between items-center text-center sm:text-left">
            <!-- Contact Info -->
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
                <a href="tel:+1234567890" class="text-gray-200 hover:text-indigo-500 flex items-center justify-center">
                    <i class="fas fa-phone-alt mr-2"></i> +1234567890
                </a>
                <a href="mailto:info@college.com" class="text-gray-200 hover:text-indigo-500 flex items-center justify-center">
                    <i class="fas fa-envelope mr-2"></i> info@college.com
                </a>
            </div>
            <!-- Social Media Links -->
            <div class="flex space-x-4 mt-2 sm:mt-0 w-full sm:w-auto justify-center">
                <a href="https://facebook.com" class="text-blue-600 hover:text-indigo-500" aria-label="Facebook">
                    <i class="fab fa-facebook fa-lg"></i>
                </a>
                <a href="https://linkedin.com" class="text-blue-700 hover:text-indigo-500" aria-label="LinkedIn">
                    <i class="fab fa-linkedin fa-lg"></i>
                </a>
                <a href="https://instagram.com" class="text-pink-500 hover:text-indigo-500" aria-label="Instagram">
                    <i class="fab fa-instagram fa-lg"></i>
                </a>
                <a href="https://twitter.com" class="text-blue-400 hover:text-indigo-500" aria-label="Twitter">
                    <i class="fab fa-twitter fa-lg"></i>
                </a>
                <a href="https://youtube.com" class="text-red-600 hover:text-indigo-500" aria-label="YouTube">
                    <i class="fab fa-youtube fa-lg"></i>
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Navigation Section -->
<nav id="navbar" class="sticky top-0 flex justify-between items-center py-2 px-4 bg-white shadow-md z-50">
    <a href="#" class="text-xl font-bold text-indigo-600">College Info Portal</a>
    <button id="mobileMenuButton" class="block md:hidden text-gray-600 focus:outline-none" aria-label="Toggle Menu">
        <i class="fas fa-bars"></i>
    </button>
    <ul id="navMenu" class="hidden md:flex space-x-6">
        <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-500">Home</a></li>
        <li class="relative group">
            <a href="#" class="text-sm text-gray-600 hover:text-indigo-500">Colleges</a>
            <ul class="absolute left-0 hidden group-hover:block bg-white shadow-lg rounded-lg mt-2 py-2 w-48">
                <li><a href="#" class="block px-4 py-2 hover:bg-indigo-100">Public Colleges</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-indigo-100">Private Colleges</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-indigo-100">Community Colleges</a></li>
            </ul>
        </li>
        <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-500">About Us</a></li>
        <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-500">Contact Us</a></li>
    </ul>
    
<!-- Login Button -->
<button id="mainBtn" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 focus:outline-none">
    Login
</button>

</div>
</nav>
    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden justify-center items-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-96">
            <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-100">Login</h2>
            <form>
                <div class="mb-4">
                    <label class="block text-gray-600 dark:text-gray-300 mb-2">Username</label>
                    <input type="text"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md py-2 px-4 focus:outline-none focus:ring focus:ring-indigo-200" />
                </div>
                <div class="mb-4">
                    <label class="block text-gray-600 dark:text-gray-300 mb-2">Password</label>
                    <input type="password"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md py-2 px-4 focus:outline-none focus:ring focus:ring-indigo-200" />
                </div>
                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-md">Login</button>
            </form>
            <button id="closeModal"
                class="mt-4 text-gray-600 dark:text-gray-300 hover:text-indigo-500 focus:outline-none">
                Close
            </button>
        </div>
    </div>

    <section class="container w-100 h-96 mx-auto my-10 px-4">
    <div id="fixedSizeCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000" data-bs-wrap="true"> <!-- Wrap set to true for continuous loop -->
        <div class="carousel-inner h-96">
            <?php
            // Loop through images and generate carousel slides
            foreach ($images as $index => $image) {
                $activeClass = ($index === 0) ? 'active' : ''; // Set the first slide as active
                echo '<div class="carousel-item ' . $activeClass . '">';
                echo '<img src="' . htmlspecialchars($image) . '" alt="Image ' . ($index + 1) . '" class="d-block w-100 h-96 object-cover">';
                echo '</div>';
            }
            ?>
        </div>



<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>


  
<div class="container mx-auto px-0 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
        
        <!-- Text and Buttons Section -->
        <div class="p-6 bg-gray-100 h-full flex flex-col">
            <div class="flex-grow">
                <h1 class="text-4xl font-bold mb-4">WELCOME TO <?php echo ucfirst(htmlspecialchars($college['name'])); ?></h1>
                <hr class="border-orange-500 border-t-2 mb-4">
                <p class="mb-4 text-justify">
                    <?php echo ucfirst(htmlspecialchars($college['description'])); ?>
                </p>
            </div>
                <div class="flex space-x-4 mt-auto"> <!-- Flexbox for buttons with margin-top auto to push it down -->
                    <a href="#" class="btn btn-primary">Explore About SGJC</a>
                    <a href="#" class="btn btn-secondary">Contact Us</a>
                </div>
            </div>

            <!-- Second Grid with Full Image -->
            <div class="p-0 m-0 full-height"> <!-- Remove padding and margin -->
                <?php 
                        $baseDir = 'uploads/';
                        $imagePath = $baseDir . htmlspecialchars($college['image']);
                        // Store $colleges in session
                        
                        ?>
    <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($college['name']) ?>"  class="w-full h-full object-cover" />
                <!-- Full width and height with object-cover -->
            </div>
        </div>
    </div>

    
    <section class="container w-5/6 mx-auto my-10 px-4">
    <div class="bg-white p-8 rounded-lg shadow-md">
        <h3 class="text-3xl font-bold text-gray-800 mb-4 text-center">Our Vision and Mission</h3>

        <div class="flex flex-col md:flex-row justify-between items-stretch gap-8">
    <!-- Mission -->
    <div class="flex-1 bg-blue-100 p-6 rounded-lg shadow-md transition-transform transform hover:scale-105 flex flex-col">
        <div class="flex-grow text-center mb-4">
            <i class="fas fa-bullseye text-blue-500 fa-3x"></i> <!-- Font Awesome icon for Mission -->
        </div>
        <h4 class="text-xl font-semibold text-gray-800 mb-3 text-center">Our Mission</h4>
        <p class="text-gray-700 text-center flex-grow"><?= htmlspecialchars($college['mission']) ?></p>
    </div>
    <!-- Vision -->
    <div class="flex-1 bg-green-100 p-6 rounded-lg shadow-md transition-transform transform hover:scale-105 flex flex-col">
        <div class="flex-grow text-center mb-4">
            <i class="fas fa-eye text-green-500 fa-3x"></i> <!-- Font Awesome icon for Vision -->
        </div>
        <h4 class="text-xl font-semibold text-gray-800 mb-3 text-center">Our Vision</h4>
        <p class="text-gray-700 text-center flex-grow"><?= htmlspecialchars($college['vision']) ?></p>
    </div>
</div>
    </div>
</section>
<!-- Campus Life and Facilities Section -->
<section class="container w-5/6 mx-auto my-10 px-4">
    <div class="bg-white p-8 rounded-lg shadow-md">
        <h3 class="text-3xl font-bold mb-6 text-center text-gray-800">Campus Life & Facilities</h3>
        <p class="text-gray-700 mb-4 text-center">
            Experience a vibrant campus life filled with opportunities for growth, engagement, and connection. Our community offers:
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <?php if (!empty($facilities)): ?>
                <?php foreach ($facilities as $facility): ?>
                    <div class="bg-gray-50 p-6 rounded-lg shadow-lg text-left transition-transform transform hover:scale-105 hover:shadow-xl border border-gray-200">
                        <img src="<?= htmlspecialchars($facility['image']) ?>" alt="<?= htmlspecialchars($facility['name']) ?>" class="w-full h-32 object-cover rounded-t-lg mb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-2"><?= htmlspecialchars($facility['name']) ?></h4>
                        <p class="text-gray-600"><?= htmlspecialchars($facility['description']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-600 text-center">No facilities found for this college.</p>
            <?php endif; ?>
        </div>
    </div>
</section>    <!-- Alumni Network Section -->
<section class="container w-5/6 mx-auto my-10 px-4">
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-8 rounded-lg shadow-lg text-white">
        <h3 class="text-3xl font-bold mb-4 text-center">Alumni Network</h3>
        <p class="text-lg mb-6 text-center">
            Join a strong and growing alumni network. Our graduates work at leading organizations, and through our alumni association, you can benefit from networking, mentoring, and exclusive career resources.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($alumni as $alum) { ?>
                <div class="bg-white text-gray-800 p-6 rounded-lg shadow-md transition-transform transform hover:scale-105">
                    <img class="w-16 h-16 rounded-full mx-auto mb-4" src="<?php echo $alum['image_url']; ?>" alt="<?php echo $alum['name']; ?>">
                    <h4 class="text-xl font-semibold text-center"><?php echo $alum['name']; ?></h4>
                    <p class="text-center"><?php echo $alum['designation']; ?> at <?php echo $alum['company']; ?></p>
                    <p class="mt-2 text-gray-600 text-center">"<?php echo $alum['testimonial']; ?>"</p>
                </div>
            <?php } ?>
        </div>

        <div class="text-center mt-6">
            <a href="#" class="bg-white text-blue-500 font-bold py-2 px-4 rounded hover:bg-gray-100 transition">
                Join the Alumni Network
            </a>
        </div>
    </div>
</section>
<!-- Gallery Section -->
<!-- Gallery Section -->
<section>
<div class="container mx-auto py-8">
    <h2 class="text-center text-3xl font-bold mb-2">Campus Life</h2>
    <p class="text-center mb-6">Explore the vibrant campus life through our facilities and activities.</p>

    <!-- Filter Buttons -->
    <div class="text-center mb-4">
        <button class="btn btn-outline-primary filter-button" data-filter="all">All</button>
        <?php foreach ($categories as $category) { ?>
            <button class="btn btn-outline-primary filter-button" data-filter="<?php echo $category; ?>">
                <?php echo ucfirst($category); ?>
            </button>
        <?php } ?>
    </div>

    <!-- Gallery Images -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php foreach ($gallery_images as $image) { ?>
            <div class="gallery-item <?php echo $image['category']; ?>">
                <img src="<?php echo $image['image_url']; ?>" alt="<?php echo $image['category']; ?>" class="gallery-image" />
                <div class="image-overlay"><?php echo ucfirst($image['category']); ?></div>
            </div>
        <?php } ?>
    </div>
</div>
</section>

<!-- Footer Section -->
<footer class="bg-gray-900 text-white py-4 w-full">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row md:space-x-8">
            <div class="w-full md:w-1/4 mb-4">
                <h5 class="font-bold text-lg mb-2">Our Logo</h5>
                <a href="#">
                    <img src="path/to/logo.svg" alt="Your Project Logo" class="h-10 mb-2">
                </a>
                <p class="text-gray-400 text-sm">Your tagline or description.</p>
            </div>
            <div class="w-full md:w-1/4 mb-4">
                <h5 class="font-bold text-lg mb-2">Quick Links</h5>
                <ul class="space-y-1">
                    <li><a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300">Contact Us</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300">About Us</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300">Privacy Policy</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300">Terms & Conditions</a></li>
                </ul>
            </div>
            <div class="w-full md:w-1/4 mb-4">
                <h5 class="font-bold text-lg mb-2">Follow Us</h5>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300" aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300" aria-label="Twitter">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300" aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300" aria-label="LinkedIn">
                        <i class="bi bi-linkedin"></i>
                    </a>
                </div>
            </div>
            <div class="w-full md:w-1/4 mb-4">
                <h5 class="font-bold text-lg mb-2">Contact Info</h5>
                <div class="flex items-center mb-1">
                    <i class="bi bi-geo-alt-fill text-indigo-500"></i>
                    <a href="https://www.google.com/maps?q=Your+Address" class="text-gray-400 hover:text-indigo-500 transition duration-300 ml-2">Your Address</a>
                </div>
                <div class="flex items-center mb-1">
                    <i class="bi bi-envelope-fill text-indigo-500"></i>
                    <a href="mailto:your-email@example.com" class="text-gray-400 hover:text-indigo-500 transition duration-300 ml-2">your-email@example.com</a>
                </div>
                <div class="flex items-center">
                    <i class="bi bi-telephone-fill text-indigo-500"></i>
                    <a href="tel:+1234567890" class="text-gray-400 hover:text-indigo-500 transition duration-300 ml-2">+1 234 567 890</a>
                </div>
            </div>
        </div>
        <div class="text-center text-gray-500 mt-4">
            <p>Designed by <a href="https://yourwebsite.com" class="text-indigo-400 hover:text-indigo-500 transition duration-300">Paarsh Infotech Pvt. Ltd.</a></p>
            <p class="text-sm">Copyright © 2024 - 2025 College Portal. All rights reserved.</p>
        </div>
    </div>
</footer>


<!-- JavaScript for filtering gallery images -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-button');
    const galleryItems = document.querySelectorAll('.gallery-item');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');

            // Show or hide items based on the filter
            galleryItems.forEach(item => {
                if (filter === 'all' || item.classList.contains(filter)) {
                    item.style.display = 'block'; // Show item
                } else {
                    item.style.display = 'none'; // Hide item
                }
            });
        });
    });
});
</script>
   <section class="bg-gray-900 text-white py-4">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row md:space-x-8 space-y-4 md:space-y-0">
            <!-- Logo Section -->
            <div class="w-full md:w-1/4">
                <h5 class="font-bold text-lg mb-2">Our Logo</h5>
                <a href="#">
                    <img src="path/to/logo.svg" alt="Your Project Logo" class="h-10 mb-2">
                </a>
                <p class="text-gray-400 text-sm">Your tagline or description.</p>
            </div>
            <!-- Quick Links -->
            <div class="w-full md:w-1/4">
                <h5 class="font-bold text-lg mb-2">Quick Links</h5>
                <ul class="space-y-1">
                    <li>
                        <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300">Contact Us</a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300">About Us</a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300">Privacy Policy</a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300">Terms & Conditions</a>
                    </li>
                </ul>
            </div>
            <!-- Follow Us -->
            <div class="w-full md:w-1/4">
                <h5 class="font-bold text-lg mb-2">Follow Us</h5>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300" aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300" aria-label="Twitter">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300" aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300" aria-label="LinkedIn">
                        <i class="bi bi-linkedin"></i>
                    </a>
                </div>
            </div>
            <!-- Contact Information -->
            <div class="w-full md:w-1/4">
                <h5 class="font-bold text-lg mb-2">Contact Info</h5>
                <div class="flex items-center mb-1">
                    <i class="bi bi-geo-alt-fill text-indigo-500"></i>
                    <a href="https://www.google.com/maps?q=Your+Address"
                        class="text-gray-400 hover:text-indigo-500 transition duration-300 ml-2">Your Address</a>
                </div>
                <div class="flex items-center mb-1">
                    <i class="bi bi-envelope-fill text-indigo-500"></i>
                    <a href="mailto:your-email@example.com"
                        class="text-gray-400 hover:text-indigo-500 transition duration-300 ml-2">your-email@example.com</a>
                </div>
                <div class="flex items-center">
                    <i class="bi bi-telephone-fill text-indigo-500"></i>
                    <a href="tel:+1234567890"
                        class="text-gray-400 hover:text-indigo-500 transition duration-300 ml-2">+1 234 567 890</a>
                </div>
            </div>
        </div>
        <div class="text-center text-gray-500 mt-6">
    <p class="text-center sm:text-left">Designed by 
        <a href="https://yourwebsite.com" class="text-indigo-400 hover:text-indigo-500 transition duration-300">Paarsh Infotech Pvt. Ltd.</a>
    </p>
    <p class="text-sm text-center sm:text-left">Copyright © 2024 - 2025 College Portal. All rights reserved.</p>
</div>
    </div>
</section>








    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <!-- Include Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
</body></html>