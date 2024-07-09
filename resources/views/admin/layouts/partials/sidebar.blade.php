@php use Illuminate\Support\Facades\Route; @endphp
    <!-- Sidebar -->
<aside class="w-64 bg-gray-800 text-white min-h-screen fixed">
    <nav class="mt-4">
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ Route::is('admin.dashboard') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-tachometer-alt w-6 h-6 mr-3"></i>
            Dashboard
        </a>
        <a href="{{ route('admin.product.index') }}"
           class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ Route::is('admin.product.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-boxes w-6 h-6 mr-3"></i>
            Products
        </a>
    </nav>
</aside>
