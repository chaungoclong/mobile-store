<nav class="bg-white shadow p-4 flex justify-between items-center fixed w-full z-10">
    <div class="flex items-center space-x-4">
        <img src="https://via.placeholder.com/40" alt="Logo" class="w-10 h-10">
        <span class="text-lg font-semibold">Admin Dashboard</span>
    </div>
    <div x-data="{ open: false }" class="relative">
        <div class="flex gap-2 items-center">
            <span>Admin</span>
            <img @click="open = !open" src="https://via.placeholder.com/40" alt="Profile"
                 class="w-10 h-10 rounded-full cursor-pointer">
        </div>
        <div x-show="open" @click.outside="open = false"
             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-20">
            <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
            <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Logout</a>
        </div>
    </div>
</nav>
