<nav x-data="{ open: false }" class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200/80 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 font-extrabold text-slate-900 text-lg tracking-tight">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 text-white text-sm font-extrabold shadow-sm shadow-indigo-500/30">E</span>
                        <span>Exam<span class="text-indigo-600">Portal</span></span>
                    </a>
                </div>

                <div class="hidden sm:ms-8 sm:flex sm:items-center sm:space-x-1">
                    <a href="{{ route('dashboard') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('dashboard', 'exams.*', 'attempts.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Exams
                    </a>
                    <a href="{{ route('bookmarks.index') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('bookmarks.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Bookmarks
                    </a>
                    @if(Auth::user()->isExaminer())
                        <span class="h-4 w-px bg-slate-200 mx-1"></span>
                        <a href="{{ route('admin.dashboard') }}"
                           class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Admin
                        </a>
                        <a href="{{ route('admin.exams.index') }}"
                           class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.exams.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Manage Exams
                        </a>
                        <a href="{{ route('admin.questions.index') }}"
                           class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.questions.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Question Bank
                        </a>
                        <a href="{{ route('admin.categories.index') }}"
                           class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Categories
                        </a>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <span class="px-2.5 py-1 text-[11px] font-bold tracking-wider rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200/60 uppercase">
                    {{ Auth::user()->role }}
                </span>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-1.5 border border-slate-200 text-sm font-semibold rounded-xl text-slate-700 bg-white hover:bg-slate-50 focus:outline-none transition">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-indigo-600 text-white text-xs font-bold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="fill-current h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Profile Settings</x-dropdown-link>
                        @if(Auth::user()->isAdmin())
                            <x-dropdown-link :href="route('admin.users.index')">Manage Users</x-dropdown-link>
                        @endif
                        @if(Auth::user()->isExaminer())
                            <x-dropdown-link :href="route('admin.reports.index')">Question Reports</x-dropdown-link>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-200 bg-white">
        <div class="pt-2 pb-3 space-y-1 px-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Exams</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('bookmarks.index')" :active="request()->routeIs('bookmarks.*')">Bookmarks</x-responsive-nav-link>
            @if(Auth::user()->isExaminer())
                <x-responsive-nav-link :href="route('admin.dashboard')">Admin Panel</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.exams.index')">Manage Exams</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.questions.index')">Question Bank</x-responsive-nav-link>
            @endif
        </div>
        <div class="pt-4 pb-2 border-t border-slate-200">
            <div class="px-4">
                <div class="font-semibold text-base text-slate-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-xs text-slate-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1 px-2">
                <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
