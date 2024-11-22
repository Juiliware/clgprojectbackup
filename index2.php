<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function showForm(type) {
            const studentForm = document.getElementById('student-form');
            const collegeForm = document.getElementById('college-form');
            const registerForm = document.getElementById('register-form');
            const forgetPasswordForm = document.getElementById('forget-password-form');
            const buttons = document.getElementById('buttons');
            const studentTab = document.getElementById('student-tab');
            const collegeTab = document.getElementById('college-tab');
            const registerButton = document.getElementById('register-button');
            const forgetPasswordButton = document.getElementById('forget-password-button');
            const extraLinks = document.getElementById('extra-links');

            studentForm.classList.add('hidden');
            collegeForm.classList.add('hidden');
            registerForm.classList.add('hidden');
            forgetPasswordForm.classList.add('hidden');
            buttons.classList.remove('hidden');
            extraLinks.classList.remove('hidden');
            studentTab.classList.remove('bg-green-700', 'text-white');
            collegeTab.classList.remove('bg-blue-700', 'text-white');
            registerButton.classList.remove('underline');
            forgetPasswordButton.classList.remove('underline');

            if (type === 'student') {
                studentForm.classList.remove('hidden');
                studentTab.classList.add('bg-green-700', 'text-white');
            } else if (type === 'college') {
                collegeForm.classList.remove('hidden');
                collegeTab.classList.add('bg-blue-700', 'text-white');
                extraLinks.classList.add('hidden');
            } else if (type === 'register') {
                registerForm.classList.remove('hidden');
                buttons.classList.add('hidden');
                extraLinks.classList.add('hidden');
                registerButton.classList.add('underline');
            } else if (type === 'forget-password') {
                forgetPasswordForm.classList.remove('hidden');
                buttons.classList.add('hidden');
                extraLinks.classList.add('hidden');
                forgetPasswordButton.classList.add('underline');
            }
        }

        function toggleModal() {
            const modal = document.getElementById('modal');
            modal.classList.toggle('hidden');
        }
    </script>
    <style>
        /* Add custom styles for the modal */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <button onclick="toggleModal()" class="bg-blue-500 text-white py-2 px-4 rounded">Login</button>

    <div id="modal" class="modal hidden">
        <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md transition-all duration-300">
            <div class="flex justify-between items-center border-b border-gray-300 pb-4 mb-4">
                <h2 class="text-lg font-semibold">Login</h2>
                <button onclick="toggleModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <div id="buttons" class="flex justify-between mb-4">
                <button id="student-tab" onclick="showForm('student')" class="w-1/2 bg-gray-200 text-gray-700 py-2 mr-2 rounded hover:bg-green-600">Student</button>
                <button id="college-tab" onclick="showForm('college')" class="w-1/2 bg-gray-200 text-gray-700 py-2 ml-2 rounded hover:bg-blue-600">College</button>
            </div>
            <div id="student-form" class="border border-gray-300 p-4 mb-4 rounded">
                <label class="block text-gray-700 mb-2">Aadhar No</label>
 <input type="text" class="w-full border border-gray-300 text-gray-700 bg-white py-2 mb-4 rounded">
                <label class="block text-gray-700 mb-2">Password</label>
                <input type="password" class="w-full border border-gray-300 text-gray-700 bg-white py-2 mb-4 rounded">
                <button class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600">Login Button</button>
            </div>
            <div id="college-form" class="border border-gray-300 p-4 mb-4 rounded hidden">
                <label class="block text-gray-700 mb-2">Email / UID No</label>
                <input type="text" class="w-full border border-gray-300 text-gray-700 bg-white py-2 mb-4 rounded">
                <label class="block text-gray-700 mb-2">Password</label>
                <input type="password" class="w-full border border-gray-300 text-gray-700 bg-white py-2 mb-4 rounded">
                <button class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">Login Button</button>
            </div>
            <div id="extra-links" class="text-center text-gray-700">
                <p>Don't have an account? <button id="register-button" onclick="showForm('register')" class="underline">Register</button></p>
                <p><button id="forget-password-button" onclick="showForm('forget-password')" class="underline">Forget Password</button></p>
            </div>
            <div id="register-form" class="border border-gray-300 p-4 mb-4 rounded hidden">
                <h2 class="text-gray-700 mb-4">Register</h2>
                <div class="flex space-x-4 mb-4">
                    <div class="w-1/3">
                        <label class="block text-gray-700 mb-2">First Name</label>
                        <input type="text" class="w-full border border-gray-300 text-gray-700 bg-white py-2 rounded">
                    </div>
                    <div class="w-1/3">
                        <label class="block text-gray-700 mb-2">Middle Name</label>
                        <input type="text" class="w-full border border-gray-300 text-gray-700 bg-white py-2 rounded">
                    </div>
                    <div class="w-1/3">
                        <label class="block text-gray-700 mb-2">Last Name</label>
                        <input type="text" class="w-full border border-gray-300 text-gray-700 bg-white py-2 rounded">
                    </div>
                </div>
                <div class="flex space-x-4 mb-4">
                    <div class="w-1/2">
                        <label class="block text-gray-700 mb-2">Email Address</label>
                        <input type="email" class="w-full border border-gray-300 text-gray-700 bg-white py-2 rounded">
                    </div>
                    <div class="w-1/2">
                        <label class="block text-gray-700 mb-2">Mobile Number</label>
                        <input type="text" class="w-full border border-gray-300 text-gray-700 bg-white py-2 rounded">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Aadhar Number</label>
                    <input type="text" class="w-full border border-gray-300 text-gray-700 bg-white py-2 rounded">
                </div>
                <div class="flex space-x-4 mb-4">
                    <div class="w-1/2">
                        <label class="block text-gray-700 mb-2">Password</label>
                        <input type="password" class="w-full border border-gray-300 text-gray-700 bg-white py-2 rounded">
                    </div>
                    <div class="w-1/2">
                        <label class="block text-gray-700 mb-2">Confirm Password</label>
                        <input type="password" class="w-full border border-gray-300 text-gray-700 bg-white py-2 rounded">
                    </div>
                </div>
                <button class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600">Register</button>
                <p class="text-center mt-4">Do you have an account? <button onclick="showForm('student')" class="underline">Login</button></p>
            </div>
            <div id="forget-password-form" class="border border-gray-300 p-4 mb-4 rounded hidden">
                <h2 class="text-gray-700 mb-4">Forgot Password</h2>
                <form id="forgotPasswordFormSubmit" method="POST" action="forgot_password.php">
                    <div class="mb-4">
                        <label for="aadharNumber" class="block text-gray-700 mb-2">Aadhar Number:</label>
                        <input type="text" id="aadharNumber" name="aadharNumber" required class="mt-1 block w-full border border-gray-300 text-gray-700 bg-white py-2 mb-4 rounded" placeholder="Aadhar Number" maxlength="12" oninput="validateAadhar(this)">
                    </div>
                    <div class="mb-4">
                        <label for="existingPassword" class="block text-gray-700 mb-2">Existing Password:</label>
                        <input type="password" id="existingPassword" name="existingPassword" required class="mt-1 block w-full border border-gray-300 text-gray-700 bg-white py-2 mb-4 rounded" placeholder="Existing Password">
                    </div>
                    <div class="mb-4">
                        <label for="newPassword" class="block text-gray-700 mb-2">New Password:</label>
                        <input type="password" id="newPassword" name="newPassword" required class="mt-1 block w-full border border-gray-300 text-gray-700 bg-white py-2 mb-4 rounded" placeholder="New Password">
                    </div>
                    <div class="mb-4">
                        <label for="confirmPassword" class="block text-gray-700 mb-2">Confirm Password:</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" required class="mt-1 block w-full border border-gray-300 text-gray-700 bg-white py-2 mb-4 rounded" placeholder="Confirm Password">
                    </div>
                    <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring focus:ring-red-200">Submit</button>
                </form>
                <p class="text-center mt-4">Do you have an account? <button onclick="showForm('student')" class="underline">Login</button></p>
            </div>
        </div>
    </div>
</body>
</html>