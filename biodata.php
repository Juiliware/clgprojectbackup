<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Admission Biodata Form</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-purple-100 to-blue-100 flex justify-center items-center min-h-screen pt-12">

    <div class="w-full max-w-4xl bg-white p-8 rounded-3xl shadow-lg">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Admission Form</h2>
        
        <!-- Form submission to PHP handler -->
        <form id="biodataForm" class="space-y-6" action="submit_form.php" method="POST" enctype="multipart/form-data">
            <!-- Personal Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label for="name" class="block text-gray-700 font-medium">
                        <i class="fas fa-user mr-2 text-blue-500"></i> Full Name
                    </label>
                    <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter your full name" required>
                </div>

                <div class="space-y-1">
                    <label for="dob" class="block text-gray-700 font-medium">
                        <i class="fas fa-calendar-alt mr-2 text-blue-500"></i> Date of Birth
                    </label>
                    <input type="date" id="dob" name="dob" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <div class="space-y-1">
                    <label for="gender" class="block text-gray-700 font-medium">
                        <i class="fas fa-venus-mars mr-2 text-blue-500"></i> Gender
                    </label>
                    <select id="gender" name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                        <option value="" disabled selected>Select your gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <!-- Parents' Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label for="fatherName" class="block text-gray-700 font-medium">Father's Name</label>
                    <input type="text" id="fatherName" name="fatherName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter father's full name" required>
                </div>

                <div class="space-y-1">
                    <label for="motherName" class="block text-gray-700 font-medium">Mother's Name</label>
                    <input type="text" id="motherName" name="motherName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter mother's full name" required>
                </div>
            </div>

            <!-- Address Details -->
            <div class="space-y-1">
                <label for="address" class="block text-gray-700 font-medium">Permanent Address</label>
                <input type="text" id="address" name="address" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter permanent address" required>
            </div>

            <!-- Caste and Income Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label for="caste" class="block text-gray-700 font-medium">Caste</label>
                    <input type="text" id="caste" name="caste" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter your caste" required>
                </div>

                <div class="space-y-1">
                    <label for="income" class="block text-gray-700 font-medium">Annual Family Income</label>
                    <input type="number" id="income" name="income" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter family income" required>
                </div>
            </div>

            <!-- Previous College Details -->
            <div class="space-y-1">
                <label for="previousCollege" class="block text-gray-700 font-medium">Previous College Name</label>
                <input type="text" id="previousCollege" name="previousCollege" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter previous college name" required>
            </div>

            <!-- Document Upload -->
            <div class="space-y-6">
                <label class="block text-gray-700 font-medium">Upload Documents</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label for="aadhar" class="block text-gray-700">Aadhar Card</label>
                        <input type="file" id="aadhar" name="aadhar" class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    </div>

                    <div class="space-y-1">
                        <label for="lc" class="block text-gray-700">Leaving Certificate (LC)</label>
                        <input type="file" id="lc" name="lc" class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    </div>

                    <div class="space-y-1">
                        <label for="markSheet" class="block text-gray-700">Board Exam Mark Sheet</label>
                        <input type="file" id="markSheet" name="markSheet" class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    </div>

                    <div class="space-y-1">
                        <label for="incomeCert" class="block text-gray-700">Income Certificate</label>
                        <input type="file" id="incomeCert" name="incomeCert" class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    </div>

                    <div class="space-y-1">
                        <label for="casteCert" class="block text-gray-700">Caste Certificate</label>
                        <input type="file" id="casteCert" name="casteCert" class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center mt-6">
                <button type="submit" class="px-6 py-3 bg-blue-500 text-white rounded-lg font-semibold">
                    Submit Application
                </button>
            </div>

            <!-- Redirect Link -->
            <div class="text-center mt-4">
                <a href="index2.html" class="text-blue-500 hover:text-blue-700">Back to Home</a>
            </div>

        </form>
    </div>

</body>
</html>
