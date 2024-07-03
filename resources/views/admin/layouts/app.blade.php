<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
<div class="flex">
    <!-- Sidebar -->
    <div class="w-64 h-screen bg-gray-800 text-white">
        <div class="p-4">
            <h1 class="text-xl font-semibold">Admin Dashboard</h1>
        </div>
        <nav class="mt-6">
            <ul>
                <li class="px-4 py-2 hover:bg-gray-700">
                    <a href="#" class="block">Dashboard</a>
                </li>
                <!-- Add more sidebar links here -->
            </ul>
        </nav>
    </div>
    <!-- Main content -->
    <div class="flex-1 flex flex-col">
        <!-- Navbar -->
        <nav class="bg-white shadow p-4">
            <div class="flex justify-between items-center">
                <div>
                    <button id="sidebarToggle" class="text-gray-600 hover:text-gray-800 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                    </button>
                </div>
                <div>
                    <!-- Add navbar content here -->
                    <span>Admin</span>
                </div>
            </div>
        </nav>
        <!-- Breadcrumb -->
        <nav class="bg-gray-200 p-4">
            <ol class="flex space-x-2">
                <li><a href="#" class="text-gray-600 hover:text-gray-800">Home</a></li>
                <li>/</li>
                <li><a href="#" class="text-gray-600 hover:text-gray-800">Dashboard</a></li>
                <li>/</li>
                <li class="text-gray-500">Current Page</li>
            </ol>
        </nav>
        <!-- Header -->
        <header class="bg-white shadow p-4 mb-4">
            <h1 class="text-2xl font-semibold">@yield('header', 'Dashboard')</h1>
        </header>
        <!-- Main content area -->
        <main class="flex-1 p-4">
            @yield('content')
        </main>
        <!-- Footer -->
        <footer class="bg-white shadow p-4">
            <p class="text-center text-gray-600">&copy; 2024 Your Company</p>
        </footer>
    </div>
</div>
<script>
    document.getElementById('sidebarToggle').addEventListener('click', function () {
        document.querySelector('.w-64').classList.toggle('hidden');
    });
</script>
</body>
</html>
