<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
            <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
    
</head>
<body class="bg-gray-100 font-sans text-gray-900 antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 bg-[#011733]">
                <div class="flex items-center justify-center h-16 px-4 bg-white">
                    <a href="{{route('admin.dashboard')}}">
                        <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                    </a>
                </div>
                <div class="flex flex-col flex-grow px-4 overflow-y-auto">
                    <nav class="flex-1 space-y-2 mt-6">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-white bg-[#011733] rounded-lg">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            Dashboard
                        </a>
                        <a href="{{ route('admin.posts.index') }}" class="flex items-center px-4 py-2 text-indigo-200 hover:text-white hover:bg-[#037396FF] rounded-lg">
                            <i class="fas fa-newspaper mr-3"></i>
                            Posts
                        </a>
                        <a href="{{ route('admin.profile.edit') }}" class="flex items-center px-4 py-2 text-indigo-200 hover:text-white hover:bg-[#037396FF] rounded-lg">
                            <i class="fas fa-user mr-3"></i>
                            Profile
                        </a>
                    </nav>
                </div>
                <div class="p-4 border-t border-[#037396FF]">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-4 py-2 text-indigo-200 hover:text-white">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top navbar -->
            <header class="flex items-center justify-between h-16 px-4 bg-white border-b border-gray-200">
                <div class="flex items-center">
                    <button class="md:hidden text-gray-500 focus:outline-none">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="ml-4 text-lg font-semibold text-gray-800">@yield('title')</h1>
                </div>
                <div class="flex items-center">
                    <div class="relative">
                        <button class="flex items-center focus:outline-none">
                            <img class="w-8 h-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random" alt="User">
                            <span class="ml-2 text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @yield('scripts')
</body>
</html>