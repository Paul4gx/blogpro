<!-- Top Navigation Bar -->
<header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-30">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6">
        <!-- Mobile Menu Button -->
        <button type="button" 
                id="mobile-menu-toggle"
                onclick="toggleMobileSidebar()"
                class="lg:hidden text-gray-500 hover:text-gray-700 p-2 -ml-2 focus:outline-none transition-colors cursor-pointer relative z-50">
            <i class="fas fa-bars text-xl"></i>
        </button>

        <!-- Search Bar -->
        <div class="hidden md:flex flex-1 max-w-xl mx-2 sm:mx-4">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" 
                       placeholder="Search posts, categories, tags..." 
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
        </div>

        <!-- Right Side Actions -->
        <div class="flex items-center space-x-2 sm:space-x-4">
            <!-- Quick Actions -->
            <a href="{{ route('admin.posts.create') }}" 
               class="hidden sm:inline-flex items-center px-3 sm:px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <i class="fas fa-plus mr-1 sm:mr-2"></i>
                <span class="hidden md:inline">New Post</span>
            </a>

            <!-- Notifications -->
            <button class="relative p-2 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-lg transition-colors">
                <i class="fas fa-bell text-xl"></i>
                <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-white"></span>
            </button>

            <!-- User Menu -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" 
                        class="flex items-center space-x-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-lg p-1">
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-semibold text-xs">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <span class="hidden md:block text-gray-700 font-medium">{{ Auth::user()->name }}</span>
                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                </button>

                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-200">
                    <a href="{{ route('admin.profile.edit') }}" 
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-user mr-2"></i>Profile Settings
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

