<div class="w-64 bg-gray-800 text-white min-h-screen"
     x-data="{ activeMenu: '{{ Route::currentRouteName() }}', openSubMenu: '' }">
    <div class="p-4">
        <h1 class="text-2xl font-bold">Admin Dashboard</h1>
    </div>
    <nav class="mt-10">
        @foreach(config('menu') as $menu)
            @include('admin.layouts.includes.sidebar-menu-item', ['menu' => $menu, 'activeMenu' => Route::currentRouteName(), 'openSubMenu' => ''])
        @endforeach
    </nav>
</div>
