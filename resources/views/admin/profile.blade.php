@extends('admin.layouts.app2')

@section('content')
    <!-- Profile Section -->
    <div class="">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Profile</h2>
        <form action="{{ route('admin.profile.update') }}" method="post" class="space-y-6">
            @csrf
            @method('put')
            <div>
                <label for="avatar" class="block text-sm font-medium text-gray-700">Upload Avatar</label>
                <div class="flex items-center space-x-4 mt-1">
                    <div id="preview">
                        <img class="size-14 rounded-full object-cover"
                             src="{{ $user?->avatar_image ?? '' }}"
                             alt="Rounded avatar">
                    </div>

                    <input type="text" id="avatar" name="avatar_image"
                           value="{{ old('avatar_image', $user?->avatar_image ?? '') }}"
                           class="hidden" data-input>
                    <button type="button"
                            data-input="avatar"
                            data-preview="preview"
                            id="chooseAvatar"
                            class="px-2 py-2 bg-indigo-600 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                             fill="currentColor">
                            <path
                                d="M17.414 2.586a2 2 0 00-2.828 0l-1.586 1.586-2-2L9.586 1H6a2 2 0 00-2 2v5.586L3.293 8.293a1 1 0 00-1.414 1.414L6 13.414a1 1 0 001.414 0L9 11.828V17a2 2 0 002 2h5a2 2 0 002-2v-5.586l1.293 1.293a1 1 0 001.414-1.414l-3-3V5.414l1.586-1.586a2 2 0 000-2.828zM15 14h-4v-2h4v2zM13.414 7L9 11.414 4.586 7H13.414z"/>
                        </svg>
                    </button>

                    @error('avatar_image')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user?->name ?? '') }}" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('name')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="text" value="{{ $user?->email ?? '' }}" disabled
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Profile
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password Section -->
    <div class="bg-white mt-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Change Password</h2>
        <form action="{{ route('admin.profile.changePassword') }}" method="post" class="space-y-6">
            @csrf
            @method('put')
            <div>
                <label for="current-password" class="block text-sm font-medium text-gray-700">Current
                    Password</label>
                <input type="password" id="current-password" name="current_password"
                       value="{{ old('current_password') }}" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('current_password')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="new-password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" id="new-password" name="new_password" value="{{ old('new_password') }}" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('new_password')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirm New
                    Password</label>
                <input type="password" id="confirm-password" name="new_password_confirmation"
                       value="{{ old('new_password_confirmation') }}" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">

                @error('new_password_confirmation')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Change Password
                </button>
            </div>
        </form>
    </div>
@endsection

@section('vendor-scripts')

@endsection

@section('custom-scripts')
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
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

        const lfm = function (id, options) {
            let button = document.getElementById(id);

            button.addEventListener('click', function () {
                const route_prefix = (options && options.prefix) ? options.prefix : '/laravel-filemanager';
                const target_input = document.getElementById(button.getAttribute('data-input'));
                const target_preview = document.getElementById(button.getAttribute('data-preview'));

                window.open(route_prefix + '?type=' + options.type || 'file', 'FileManager', 'width=1000,height=800');
                window.SetUrl = function (items) {
                    // set the value of the desired input to image url
                    target_input.value = items[0]?.url;
                    target_input.dispatchEvent(new Event('change'));

                    // clear previous preview
                    target_preview.innerHtml = '';

                    // set or change the preview image src
                    target_preview.innerHTML = '';
                    let img = document.createElement('img')
                    img.setAttribute('class', 'size-14 rounded-full object-cover')
                    img.setAttribute('src', items[0]?.thumb_url)
                    target_preview.appendChild(img);

                    // trigger change event
                    target_preview.dispatchEvent(new Event('change'));
                };
            });
        };

        document.addEventListener('DOMContentLoaded', function () {
            lfm('chooseAvatar', {
                type: 'file',
                prefix: 'laravel-filemanager'
            });
        })
    </script>
@endsection
