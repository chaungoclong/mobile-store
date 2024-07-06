<?php

/**
 * @var string $activeMenu
 * @var array $menu
 */

use Illuminate\Support\Facades\Route;

$hasSubmenu = !empty($menu['submenu']);
$route = $menu['route'] ?? null;

$isActive = $activeMenu === $route;

if (Route::has($route)) {
    $url = route($route);
} else {
    $url = '#';
}
?>

<div>
    <a href="{{ $url }}"
       @if($hasSubmenu)
           @click.prevent="activeMenu = '{{ $menu['title'] ?? '' }}';
            openSubMenu = openSubMenu === '{{ $menu['title'] ?? '' }}' ? '' : '{{ $menu['title'] ?? '' }}'"
       @else
           @click="activeMenu = '{{ $menu['route'] }}'"
       @endif
       :class="{ 'bg-gray-700': {{ json_encode($isActive) }} }"
       class="flex justify-between items-center block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
        <div class="flex items-center">
            <i class="{{ $menu['icon'] ?? '' }} mr-2"></i>
            {{ $menu['title'] }}
        </div>
        @if($hasSubmenu)
            <svg :class="{ 'transform rotate-180': openSubMenu === '{{ $menu['title'] ?? '' }}' }"
                 class="w-4 h-4 transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                      d="M5.23 7.21a.75.75 0 011.04-.02l3.73 3.59 3.73-3.59a.75.75 0 111.02 1.1l-4.23 4.06a.75.75 0 01-1.04 0L5.25 8.3a.75.75 0 01-.02-1.08z"
                      clip-rule="evenodd"/>
            </svg>
        @endif
    </a>
    @if($hasSubmenu)
        <div x-show="openSubMenu === '{{ $menu['title'] ?? '' }}'" class="pl-8">
            @foreach($menu['submenu'] as $submenu)
                @include('admin.layouts.includes.sidebar-menu-item', ['menu' => $submenu, 'activeMenu' => $activeMenu, 'openSubMenu' => $openSubMenu])
            @endforeach
        </div>
    @endif
</div>
