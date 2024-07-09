<!-- Sidebar -->
<aside class="w-64 bg-gray-800 text-white min-h-screen fixed">
    <nav class="mt-10">
        <a href="#" @click="activeMenu = 'dashboard'" :class="{'bg-gray-700': activeMenu === 'dashboard'}"
           class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
            <i class="fas fa-tachometer-alt w-6 h-6 mr-3"></i>
            Dashboard
        </a>
        <a href="#" @click="activeMenu = 'products'" :class="{'bg-gray-700': activeMenu === 'products'}"
           class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
            <i class="fas fa-boxes w-6 h-6 mr-3"></i>
            Products
        </a>
        <a href="#" @click="activeMenu = 'orders'" :class="{'bg-gray-700': activeMenu === 'orders'}"
           class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
            <i class="fas fa-shopping-cart w-6 h-6 mr-3"></i>
            Orders
        </a>
        <a href="#" @click="activeMenu = 'customers'" :class="{'bg-gray-700': activeMenu === 'customers'}"
           class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
            <i class="fas fa-users w-6 h-6 mr-3"></i>
            Customers
        </a>
        <div x-data="{ open: false }">
            <button @click="open = !open"
                    class="flex items-center justify-between w-full text-left py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                <div class="flex items-center">
                    <i class="fas fa-cogs w-6 h-6 mr-3"></i>
                    Settings
                </div>
                <i :class="{'transform rotate-180': open}"
                   class="fas fa-chevron-down w-5 h-5 transition-transform duration-200"></i>
            </button>
            <div x-show="open" class="pl-8">
                <a href="#" @click="activeMenu = 'general-settings'"
                   :class="{'bg-gray-700': activeMenu === 'general-settings'}"
                   class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                    <i class="fas fa-tools w-6 h-6 mr-3"></i>
                    General
                </a>
                <a href="#" @click="activeMenu = 'security-settings'"
                   :class="{'bg-gray-700': activeMenu === 'security-settings'}"
                   class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                    <i class="fas fa-shield-alt w-6 h-6 mr-3"></i>
                    Security
                </a>
            </div>
        </div>
    </nav>
</aside>
