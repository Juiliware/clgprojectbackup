<?php
// Assuming you have a database connection $conn

// Check if the application ID is passed in the URL
if (isset($_GET['id'])) {
    $application_id = $_GET['id'];

    // Fetch the data from the database based on the application ID
    $query = "SELECT * FROM applications WHERE id = $application_id";
    $result = mysqli_query($conn, $query);

    // If the application is found
    if ($row = mysqli_fetch_assoc($result)) {
        $application_data = $row;
    } else {
        echo "Application not found!";
        exit();
    }
} else {
    echo "Application ID is missing!";
    exit();
}
?>

<!-- Application Form Content -->
<div id="applicationFormContent" class="content-section">
    <h1 class="text-2xl font-semibold text-blue-600">Application Form</h1>

    <!-- Application Status Card -->
    <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition mt-6">
        <p class="mt-4 text-gray-600">Please fill out the application form below.</p>

        <!-- Form submission to PHP handler -->
        <form id="biodataForm" class="space-y-8 mt-8" action="submit_form.php" method="POST" enctype="multipart/form-data">
            
            <!-- Personal Information Section -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-700 border-l-4 border-blue-500 pl-2 mb-4">
                    <i class="mr-2 text-blue-500"></i> Personal Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Full Name Field -->
                    <div class="space-y-1">
                        <label for="name" class="block text-gray-700 font-medium">
                            <i class=" mr-2 text-blue-500"></i> Full Name
                        </label>
                        <input type="text" id="name" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?php echo $application_data['name']; ?>" required>
                    </div>
                    <!-- Date of Birth Field -->
                    <div class="space-y-1">
                        <label for="dob" class="block text-gray-700 font-medium">
                            <i class="mr-2 text-blue-500"></i> Date of Birth
                        </label>
                        <input type="date" id="dob" name="dob" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?php echo $application_data['dob']; ?>" required>
                    </div>
                    <!-- Gender Field -->
                    <div class="space-y-1">
                        <label for="gender" class="block text-gray-700 font-medium">
                            <i class="mr-2 text-blue-500"></i> Gender
                        </label>
                        <select id="gender" name="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                            <option value="" disabled>Select your gender</option>
                            <option value="Male" <?php echo $application_data['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo $application_data['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo $application_data['gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Parents' Details Section -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-700 border-l-4 border-blue-500 pl-2 mb-4">
                    <i class="fas fa-users mr-2 text-blue-500"></i> Parents' Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label for="fatherName" class="block text-gray-700 font-medium">Father's Name</label>
                        <input type="text" id="fatherName" name="fatherName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?php echo $application_data['fatherName']; ?>" required>
                    </div>
                    <div class="space-y-1">
                        <label for="motherName" class="block text-gray-700 font-medium">Mother's Name</label>
                        <input type="text" id="motherName" name="motherName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?php echo $application_data['motherName']; ?>" required>
                    </div>
                </div>
            </div>

            <!-- Address Details Section -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-700 border-l-4 border-blue-500 pl-2 mb-4">
                    <i class="fas fa-home mr-2 text-blue-500"></i> Address
                </h3>
                <div class="space-y-1">
                    <label for="address" class="block text-gray-700 font-medium">Permanent Address</label>
                    <input type="text" id="address" name="address" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?php echo $application_data['address']; ?>" required>
                </div>
            </div>

            <!-- Caste and Income Details Section -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-700 border-l-4 border-blue-500 pl-2 mb-4">
                    <i class="fas fa-coins mr-2 text-blue-500"></i> Caste and Income
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label for="caste" class="block text-gray-700 font-medium">Caste</label>
                        <input type="text" id="caste" name="caste" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?php echo $application_data['caste']; ?>" required>
                    </div>
                    <div class="space-y-1">
                        <label for="income" class="block text-gray-700 font-medium">Annual Family Income</label>
                        <input type="number" id="income" name="income" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?php echo $application_data['income']; ?>" required>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center mt-8">
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-700 text-white rounded-lg font-semibold transform hover:scale-105 transition">
                    Update Application
                </button>
            </div>
        </form>
    </div>
</div>
