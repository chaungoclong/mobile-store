<header class="bg-white shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">Dashboard</h2>
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center focus:outline-none">
                <img src="{{ auth()->user()?->avatar_url ?? '' }}" alt="Avatar" class="w-8 h-8 rounded-full mr-2">
                <span>{{ auth()->user()?->name ?? '' }}</span>
                <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M5.23 7.21a.75.75 0 011.04-.02l3.73 3.59 3.73-3.59a.75.75 0 111.02 1.1l-4.23 4.06a.75.75 0 01-1.04 0L5.25 8.3a.75.75 0 01-.02-1.08z"
                          clip-rule="evenodd"/>
                </svg>
            </button>
            <div x-show="open" @click.away="open = false" x-transition
                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2">
                <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
                <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Đăng xuất</a>
            </div>
        </div>
    </div>
</header>
