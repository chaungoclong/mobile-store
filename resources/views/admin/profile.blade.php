<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
</head>
<body class="bg-gray-100 flex">

<!-- Sidebar -->
<div class="w-64 bg-gray-800 text-white min-h-screen">
    <div class="p-4">
        <h1 class="text-2xl font-bold">Admin Dashboard</h1>
    </div>
    <nav class="mt-10">
        <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">Dashboard</a>
        <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">Products</a>
        <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">Categories</a>
        <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">Users</a>
        <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">Settings</a>
        <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">Profile</a>
    </nav>
</div>

<!-- Main Content -->
<div class="flex-1 flex flex-col">
    <!-- Navbar -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">Dashboard</h2>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center focus:outline-none">
                    <img src="https://via.placeholder.com/40" alt="Avatar" class="rounded-full mr-2">
                    <span>John Doe</span>
                    <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M5.23 7.21a.75.75 0 011.04-.02l3.73 3.59 3.73-3.59a.75.75 0 111.02 1.1l-4.23 4.06a.75.75 0 01-1.04 0L5.25 8.3a.75.75 0 01-.02-1.08z"
                              clip-rule="evenodd"/>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition
                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2">
                    <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
                    <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Breadcrumbs -->
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="#" class="text-gray-500 hover:text-gray-700">Home</a>
                </li>
                <li>
                    <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </li>
                <li>
                    <a href="#" class="text-gray-500 hover:text-gray-700">Profile</a>
                </li>
            </ol>
        </div>
    </nav>

    <!-- Main Section -->
    <main class="flex-1 bg-gray-100">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Profile Section -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Profile</h2>
                <form action="/upload-avatar" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label for="avatar" class="block text-sm font-medium text-gray-700">Upload Avatar</label>
                        <div class="flex items-center space-x-4 mt-1">
                            <img id="avatar-preview" class="w-20 h-20 rounded-full" src="https://via.placeholder.com/40" alt="Avatar Preview">
                            <input type="file" id="avatar" name="avatar" class="hidden" onchange="previewImage(event)">
                            <button type="button" class="px-2 py-2 bg-indigo-600 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="document.getElementById('avatar').click()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M17.414 2.586a2 2 0 00-2.828 0l-1.586 1.586-2-2L9.586 1H6a2 2 0 00-2 2v5.586L3.293 8.293a1 1 0 00-1.414 1.414L6 13.414a1 1 0 001.414 0L9 11.828V17a2 2 0 002 2h5a2 2 0 002-2v-5.586l1.293 1.293a1 1 0 001.414-1.414l-3-3V5.414l1.586-1.586a2 2 0 000-2.828zM15 14h-4v-2h4v2zM13.414 7L9 11.414 4.586 7H13.414z"/>
                                </svg>
                            </button>
                            <button type="button" class="px-2 py-2 bg-red-600 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="removeImage()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 2a1 1 0 00-.883.993L7 3v1H4a1 1 0 00-.117 1.993L4 6h12a1 1 0 00.117-1.993L16 4h-3V3a1 1 0 00-.883-.993L12 2H8zm6 4H6v9a2 2 0 001.85 1.995L8 17h4a2 2 0 001.995-1.85L14 15V6zM8 8a1 1 0 011 .883L9 9v5a1 1 0 01-1.993.117L7 14V9a1 1 0 011-1zm4 0a1 1 0 011 .883L13 9v5a1 1 0 01-1.993.117L11 14V9a1 1 0 011-1z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" id="name" name="name" value="Admin Name" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" value="admin@example.com" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password Section -->
            <div class="bg-white shadow rounded-lg p-6 mt-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Change Password</h2>
                <form action="#" method="POST" class="space-y-6">
                    <div>
                        <label for="current-password" class="block text-sm font-medium text-gray-700">Current Password</label>
                        <input type="password" id="current-password" name="current-password" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="new-password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" id="new-password" name="new-password" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <input type="password" id="confirm-password" name="confirm-password" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white shadow mt-auto">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <p class="text-gray-600 text-center">&copy; 2024 Your Company. All rights reserved.</p>
        </div>
    </footer>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('avatar-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function removeImage() {
        const output = document.getElementById('avatar-preview');
        const fileInput = document.getElementById('avatar');
        output.src = "https://via.placeholder.com/40";
        fileInput.value = null;
    }
</script>

</body>
</html>
