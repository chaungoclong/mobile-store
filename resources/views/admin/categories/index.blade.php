<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">Profile</h2>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Admin Name</span>
                <button class="text-gray-600 hover:text-gray-800">Logout</button>
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
                <form action="#" method="POST" class="space-y-6">
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
</body>
</html>
