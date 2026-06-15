<header
    class="sticky top-0 flex w-full bg-white border-gray-200 z-99999 dark:border-gray-800 dark:bg-gray-900 xl:border-b"
    x-data="{
        isApplicationMenuOpen: false,
        toggleApplicationMenu() {
            this.isApplicationMenuOpen = !this.isApplicationMenuOpen;
        }
    }">
    <div class="flex flex-col items-center justify-between grow xl:flex-row xl:px-6">
        <div
            class="flex items-center justify-between w-full gap-2 px-3 py-3 border-b border-gray-200 dark:border-gray-800 sm:gap-4 xl:justify-normal xl:border-b-0 xl:px-0 lg:py-4">

            <!-- Desktop Sidebar Toggle Button -->
            <button
                class="hidden xl:flex items-center justify-center w-10 h-10 text-gray-500 border border-gray-200 rounded-lg dark:border-gray-800 dark:text-gray-400 lg:h-11 lg:w-11"
                :class="{ 'bg-gray-100 dark:bg-white/[0.03]': !$store.sidebar.isExpanded }"
                @click="$store.sidebar.toggleExpanded()" aria-label="Toggle Sidebar">
                <svg x-show="!$store.sidebar.isMobileOpen" width="16" height="12" viewBox="0 0 16 12" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M0.583252 1C0.583252 0.585788 0.919038 0.25 1.33325 0.25H14.6666C15.0808 0.25 15.4166 0.585786 15.4166 1C15.4166 1.41421 15.0808 1.75 14.6666 1.75L1.33325 1.75C0.919038 1.75 0.583252 1.41422 0.583252 1ZM0.583252 11C0.583252 10.5858 0.919038 10.25 1.33325 10.25L14.6666 10.25C15.0808 10.25 15.4166 10.5858 15.4166 11C15.4166 11.4142 15.0808 11.75 14.6666 11.75L1.33325 11.75C0.919038 11.75 0.583252 11.4142 0.583252 11ZM1.33325 5.25C0.919038 5.25 0.583252 5.58579 0.583252 6C0.583252 6.41421 0.919038 6.75 1.33325 6.75L7.99992 6.75C8.41413 6.75 8.74992 6.41421 8.74992 6C8.74992 5.58579 8.41413 5.25 7.99992 5.25L1.33325 5.25Z"
                        fill="currentColor"></path>
                </svg>
                <svg x-show="$store.sidebar.isMobileOpen" class="fill-current" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M6.21967 7.28131C5.92678 6.98841 5.92678 6.51354 6.21967 6.22065C6.51256 5.92775 6.98744 5.92775 7.28033 6.22065L11.999 10.9393L16.7176 6.22078C17.0105 5.92789 17.4854 5.92788 17.7782 6.22078C18.0711 6.51367 18.0711 6.98855 17.7782 7.28144L13.0597 12L17.7782 16.7186C18.0711 17.0115 18.0711 17.4863 17.7782 17.7792C17.4854 18.0721 17.0105 18.0721 16.7176 17.7792L11.999 13.0607L7.28033 17.7794C6.98744 18.0722 6.51256 18.0722 6.21967 17.7794C5.92678 17.4865 5.92678 17.0116 6.21967 16.7187L10.9384 12L6.21967 7.28131Z"
                        fill="" />
                </svg>
            </button>

            <!-- Mobile Menu Toggle Button -->
            <button
                class="flex xl:hidden items-center justify-center w-10 h-10 text-gray-500 rounded-lg dark:text-gray-400 lg:h-11 lg:w-11"
                :class="{ 'bg-gray-100 dark:bg-white/[0.03]': $store.sidebar.isMobileOpen }"
                @click="$store.sidebar.toggleMobileOpen()" aria-label="Toggle Mobile Menu">
                <svg x-show="!$store.sidebar.isMobileOpen" width="16" height="12" viewBox="0 0 16 12" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M0.583252 1C0.583252 0.585788 0.919038 0.25 1.33325 0.25H14.6666C15.0808 0.25 15.4166 0.585786 15.4166 1C15.4166 1.41421 15.0808 1.75 14.6666 1.75L1.33325 1.75C0.919038 1.75 0.583252 1.41422 0.583252 1ZM0.583252 11C0.583252 10.5858 0.919038 10.25 1.33325 10.25L14.6666 10.25C15.0808 10.25 15.4166 10.5858 15.4166 11C15.4166 11.4142 15.0808 11.75 14.6666 11.75L1.33325 11.75C0.919038 11.75 0.583252 11.4142 0.583252 11ZM1.33325 5.25C0.919038 5.25 0.583252 5.58579 0.583252 6C0.583252 6.41421 0.919038 6.75 1.33325 6.75L7.99992 6.75C8.41413 6.75 8.74992 6.41421 8.74992 6C8.74992 5.58579 8.41413 5.25 7.99992 5.25L1.33325 5.25Z"
                        fill="currentColor"></path>
                </svg>
                <svg x-show="$store.sidebar.isMobileOpen" class="fill-current" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M6.21967 7.28131C5.92678 6.98841 5.92678 6.51354 6.21967 6.22065C6.51256 5.92775 6.98744 5.92775 7.28033 6.22065L11.999 10.9393L16.7176 6.22078C17.0105 5.92789 17.4854 5.92788 17.7782 6.22078C18.0711 6.51367 18.0711 6.98855 17.7782 7.28144L13.0597 12L17.7782 16.7186C18.0711 17.0115 18.0711 17.4863 17.7782 17.7792C17.4854 18.0721 17.0105 18.0721 16.7176 17.7792L11.999 13.0607L7.28033 17.7794C6.98744 18.0722 6.51256 18.0722 6.21967 17.7794C5.92678 17.4865 5.92678 17.0116 6.21967 16.7187L10.9384 12L6.21967 7.28131Z"
                        fill="" />
                </svg>
            </button>

            <!-- Mobile Logo -->
            @php
                $headerLogo = \App\Models\Setting::get('logo', '');
                $headerName = \App\Models\Setting::get('app_name', config('app.name'));
            @endphp
            <a href="{{ route('admin.dashboard') }}" class="xl:hidden flex items-center gap-2">
                @if($headerLogo)
                    <img src="{{ Storage::url($headerLogo) }}" class="w-7 h-7 object-contain rounded-md" alt="logo">
                @else
                    <div class="w-7 h-7 bg-brand-500 rounded-md flex items-center justify-center">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="white"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                    </div>
                @endif
                <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $headerName }}</span>
            </a>

            <!-- Application Menu Toggle (mobile only) -->
            <button @click="toggleApplicationMenu()"
                class="flex items-center justify-center w-10 h-10 text-gray-700 rounded-lg z-99999 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 xl:hidden">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M5.99902 10.4951C6.82745 10.4951 7.49902 11.1667 7.49902 11.9951V12.0051C7.49902 12.8335 6.82745 13.5051 5.99902 13.5051C5.1706 13.5051 4.49902 12.8335 4.49902 12.0051V11.9951C4.49902 11.1667 5.1706 10.4951 5.99902 10.4951ZM17.999 10.4951C18.8275 10.4951 19.499 11.1667 19.499 11.9951V12.0051C19.499 12.8335 18.8275 13.5051 17.999 13.5051C17.1706 13.5051 16.499 12.8335 16.499 12.0051V11.9951C16.499 11.1667 17.1706 10.4951 17.999 10.4951ZM13.499 11.9951C13.499 11.1667 12.8275 10.4951 11.999 10.4951C11.1706 10.4951 10.499 11.1667 10.499 11.9951V12.0051C10.499 12.8335 11.1706 13.5051 11.999 13.5051C12.8275 13.5051 13.499 12.8335 13.499 12.0051V11.9951Z"
                        fill="currentColor" />
                </svg>
            </button>

            <!-- Global Search (desktop only) -->
            <div class="hidden xl:block"
                x-data="{
                    focused: false,
                    open: false,
                    filterOpen: false,
                    query: '{{ addslashes(request('search', '')) }}',
                    results: [],
                    loading: false,
                    timer: null,
                    grouped: {},
                    filters: { users: true, roles: true, activity: true, settings: true, languages: true },
                    groupLabels: {
                        users: '{{ __('users.title') }}',
                        roles: '{{ __('app.roles') }}',
                        activity: '{{ __('activity.title') }}',
                        settings: '{{ __('settings.title') }}',
                        languages: '{{ __('languages.title') }}',
                    },
                    get activeGroups() {
                        return Object.keys(this.filters).filter(k => this.filters[k]);
                    },
                    get allSelected() {
                        return this.activeGroups.length === Object.keys(this.filters).length;
                    },
                    toggleAll() {
                        if (this.allSelected) {
                            const keys = Object.keys(this.filters);
                            keys.forEach((k, i) => this.filters[k] = i === 0);
                        } else {
                            Object.keys(this.filters).forEach(k => this.filters[k] = true);
                        }
                        if (this.query.length >= 3) this.doSearch();
                    },
                    onFocus() {
                        this.focused = true;
                        if (this.query.length >= 3 && this.results.length > 0) this.open = true;
                    },
                    onInput() {
                        clearTimeout(this.timer);
                        if (this.query.length < 3) {
                            this.results = [];
                            this.grouped = {};
                            this.open = false;
                            return;
                        }
                        this.timer = setTimeout(() => this.doSearch(), 300);
                    },
                    async doSearch() {
                        this.loading = true;
                        try {
                            const params = new URLSearchParams({ q: this.query });
                            this.activeGroups.forEach(g => params.append('groups[]', g));
                            const res = await fetch('/admin/search?' + params.toString(), {
                                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                            });
                            const data = await res.json();
                            this.results = data.results || [];
                            this.grouped = this.results.reduce((acc, r) => {
                                if (!acc[r.group]) acc[r.group] = [];
                                acc[r.group].push(r);
                                return acc;
                            }, {});
                            this.open = this.results.length > 0;
                        } finally {
                            this.loading = false;
                        }
                    },
                    navigate(url) {
                        window.location.href = url;
                    },
                    close() {
                        this.open = false;
                        this.focused = false;
                        this.filterOpen = false;
                        this.query = '';
                        this.results = [];
                        this.grouped = {};
                    }
                }"
                @keydown.window.prevent.cmd.k="$refs.searchInput.focus()"
                @keydown.window.prevent.ctrl.k="$refs.searchInput.focus()"
                @keydown.escape="close(); $refs.searchInput.blur()">

                <!-- Full-page blur overlay — teleported to body to escape header stacking context -->
                <template x-teleport="body">
                    <div
                        x-show="focused"
                        x-transition:enter="transition-opacity ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"
                        style="z-index:99990;"
                        @click="close(); $refs.searchInput.blur()"></div>
                </template>

                <!-- Search wrapper — header z-99999 keeps it above overlay -->
                <div class="relative">
                    <span class="absolute -translate-y-1/2 pointer-events-none left-4 top-1/2">
                        <svg x-show="!loading" class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z"
                                fill="" />
                        </svg>
                        <svg x-show="loading" class="animate-spin text-gray-400" width="20" height="20" viewBox="0 0 24 24" fill="none" style="display:none;">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <input
                        type="text"
                        x-ref="searchInput"
                        x-model="query"
                        @focus="onFocus()"
                        @blur="if (!open) { focused = false; }"
                        @input="onInput()"
                        placeholder="{{ __('app.search_global') }}"
                        autocomplete="off"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pl-12 pr-14 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 xl:w-[430px] transition-all" />
                    <!-- Clear button — removes ?search= from URL and reloads -->
                    <button
                        type="button"
                        x-show="query.length > 0"
                        @mousedown.prevent="
                            const url = new URL(window.location.href);
                            url.searchParams.delete('search');
                            window.location.href = url.toString();
                        "
                        class="absolute right-[4.5rem] top-1/2 -translate-y-1/2 flex items-center justify-center w-5 h-5 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-white/10 dark:hover:text-gray-300 transition-colors"
                        style="display:none;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M18 6L6 18M6 6l12 12"/>
                        </svg>
                    </button>

                    <!-- Filter dropdown trigger (⌘K badge) -->
                    <div class="absolute right-2.5 top-1/2 -translate-y-1/2" @click.outside="filterOpen = false">
                        <button
                            type="button"
                            @mousedown.prevent="filterOpen = !filterOpen"
                            class="flex items-center gap-0.5 rounded-lg border px-[7px] py-[4.5px] text-xs -tracking-[0.2px] transition-colors"
                            :class="allSelected
                                ? 'border-gray-200 bg-gray-50 text-gray-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400'
                                : 'border-brand-300 bg-brand-50 text-brand-600 dark:border-brand-800 dark:bg-brand-500/10 dark:text-brand-400'">
                            <span> ⌘ </span>
                            <span> K </span>
                        </button>

                        <!-- Filter dropdown -->
                        <div
                            x-show="filterOpen"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            class="absolute right-0 top-full mt-2 w-44 rounded-2xl border border-gray-200 bg-white shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark overflow-hidden"
                            style="display:none; z-index:1;">

                            <div class="px-3 py-2 border-b border-gray-100 dark:border-gray-800">
                                <button type="button" @click="toggleAll()"
                                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                                    <span x-text="allSelected ? '{{ __('app.deselect_all') }}' : '{{ __('app.select_all') }}'"></span>
                                </button>
                            </div>

                            <div class="p-2 flex flex-col gap-0.5">
                                @foreach(['users' => 'users.title', 'roles' => 'app.roles', 'activity' => 'activity.title', 'settings' => 'settings.title', 'languages' => 'languages.title'] as $key => $langKey)
                                <label class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors"
                                    :class="filters.{{ $key }} && activeGroups.length === 1 ? 'cursor-not-allowed opacity-60' : 'cursor-pointer'">
                                    <input type="checkbox" x-model="filters.{{ $key }}"
                                        :disabled="filters.{{ $key }} && activeGroups.length === 1"
                                        @change="if (query.length >= 3) doSearch()"
                                        class="w-3.5 h-3.5 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800 disabled:opacity-50">
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-400">{{ __($langKey) }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Dropdown results -->
                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute top-full left-0 mt-2 w-full min-w-[430px] rounded-2xl border border-gray-200 bg-white shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark overflow-hidden max-h-[480px] overflow-y-auto"
                        style="display:none;">

                        <template x-for="(items, group) in grouped" :key="group">
                            <div>
                                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-800">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500"
                                        x-text="groupLabels[group] || group"></span>
                                </div>
                                <template x-for="(item, idx) in items" :key="idx">
                                    <button
                                        type="button"
                                        @mousedown.prevent="navigate(item.url)"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors group border-b border-gray-50 dark:border-white/[0.04] last:border-0">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-400 truncate group-hover:text-gray-900 dark:group-hover:text-gray-300"
                                                x-text="item.label"></p>
                                            <p x-show="item.meta" class="text-xs text-gray-500 dark:text-gray-500 truncate mt-0.5"
                                                x-text="item.meta"></p>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M9 18l6-6-6-6"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                        </template>

                        <div x-show="!loading && results.length === 0" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400" style="display:none;">
                            {{ __('app.no_results') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Menu (mobile) and Right Side Actions (desktop) -->
        <div :class="isApplicationMenuOpen ? 'flex' : 'hidden'"
            class="items-center justify-between w-full gap-4 px-5 py-4 xl:flex shadow-theme-md xl:justify-end xl:px-0 xl:shadow-none">
            <div class="flex items-center gap-2 2xsm:gap-3">
                <!-- Language Switcher -->
                @php $allLangs = \App\Models\Language::orderByDesc('is_default')->orderBy('name')->get(); @endphp
                @if($allLangs->count() > 1)
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                        class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-gray-700 h-11 w-11 hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                        title="Switch language">
                        <span class="text-base leading-none">{{ $allLangs->firstWhere('code', app()->getLocale())?->flag ?? $allLangs->first()?->flag ?? '🌐' }}</span>
                    </button>
                    <div x-show="open" x-transition
                        class="absolute right-0 mt-2 w-40 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900 z-50 overflow-hidden">
                        @foreach($allLangs as $lang)
                        <a href="{{ route('locale.switch', $lang->code) }}"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors {{ app()->getLocale() === $lang->code ? 'font-semibold bg-gray-50 dark:bg-gray-800' : '' }}">
                            <span>{{ $lang->flag }}</span>
                            <span>{{ $lang->name }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Cache Clear Button -->
                <form method="POST" action="{{ route('admin.cache.clear') }}" x-data="{ spinning: false }">
                    @csrf
                    <button type="submit" @click="spinning = true"
                        title="{{ __('app.clear_cache') }}"
                        class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-gray-700 h-11 w-11 hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white">
                        <svg :class="spinning ? 'animate-spin' : ''" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.06189 13C4.02104 12.6724 4 12.3387 4 12C4 7.58172 7.58172 4 12 4C14.1128 4 16.0431 4.84864 17.4786 6.22642L15 8H21V2L18.7463 4.25368C16.9785 2.53679 14.611 1.5 12 1.5C6.20101 1.5 1.5 6.20101 1.5 12C1.5 12.3409 1.51396 12.6786 1.54139 13H4.06189Z" fill="currentColor"/>
                            <path d="M19.9381 11C19.979 11.3276 20 11.6613 20 12C20 16.4183 16.4183 20 12 20C9.8872 20 7.95686 19.1514 6.52143 17.7736L9 16H3V22L5.25368 19.7463C7.02148 21.4632 9.38904 22.5 12 22.5C17.799 22.5 22.5 17.799 22.5 12C22.5 11.6591 22.486 11.3214 22.4586 11H19.9381Z" fill="currentColor"/>
                        </svg>
                    </button>
                </form>

                <!-- Theme Toggle Button -->
                <button
                    class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-dark-900 h-11 w-11 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                    @click="$store.theme.toggle()">
                    <svg class="hidden dark:block" width="20" height="20" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M9.99998 1.5415C10.4142 1.5415 10.75 1.87729 10.75 2.2915V3.5415C10.75 3.95572 10.4142 4.2915 9.99998 4.2915C9.58577 4.2915 9.24998 3.95572 9.24998 3.5415V2.2915C9.24998 1.87729 9.58577 1.5415 9.99998 1.5415ZM10.0009 6.79327C8.22978 6.79327 6.79402 8.22904 6.79402 10.0001C6.79402 11.7712 8.22978 13.207 10.0009 13.207C11.772 13.207 13.2078 11.7712 13.2078 10.0001C13.2078 8.22904 11.772 6.79327 10.0009 6.79327ZM5.29402 10.0001C5.29402 7.40061 7.40135 5.29327 10.0009 5.29327C12.6004 5.29327 14.7078 7.40061 14.7078 10.0001C14.7078 12.5997 12.6004 14.707 10.0009 14.707C7.40135 14.707 5.29402 12.5997 5.29402 10.0001ZM15.9813 5.08035C16.2742 4.78746 16.2742 4.31258 15.9813 4.01969C15.6884 3.7268 15.2135 3.7268 14.9207 4.01969L14.0368 4.90357C13.7439 5.19647 13.7439 5.67134 14.0368 5.96423C14.3297 6.25713 14.8045 6.25713 15.0974 5.96423L15.9813 5.08035ZM18.4577 10.0001C18.4577 10.4143 18.1219 10.7501 17.7077 10.7501H16.4577C16.0435 10.7501 15.7077 10.4143 15.7077 10.0001C15.7077 9.58592 16.0435 9.25013 16.4577 9.25013H17.7077C18.1219 9.25013 18.4577 9.58592 18.4577 10.0001ZM14.9207 15.9806C15.2135 16.2735 15.6884 16.2735 15.9813 15.9806C16.2742 15.6877 16.2742 15.2128 15.9813 14.9199L15.0974 14.036C14.8045 13.7431 14.3297 13.7431 14.0368 14.036C13.7439 14.3289 13.7439 14.8038 14.0368 15.0967L14.9207 15.9806ZM9.99998 15.7088C10.4142 15.7088 10.75 16.0445 10.75 16.4588V17.7088C10.75 18.123 10.4142 18.4588 9.99998 18.4588C9.58577 18.4588 9.24998 18.123 9.24998 17.7088V16.4588C9.24998 16.0445 9.58577 15.7088 9.99998 15.7088ZM5.96356 15.0972C6.25646 14.8043 6.25646 14.3295 5.96356 14.0366C5.67067 13.7437 5.1958 13.7437 4.9029 14.0366L4.01902 14.9204C3.72613 15.2133 3.72613 15.6882 4.01902 15.9811C4.31191 16.274 4.78679 16.274 5.07968 15.9811L5.96356 15.0972ZM4.29224 10.0001C4.29224 10.4143 3.95645 10.7501 3.54224 10.7501H2.29224C1.87802 10.7501 1.54224 10.4143 1.54224 10.0001C1.54224 9.58592 1.87802 9.25013 2.29224 9.25013H3.54224C3.95645 9.25013 4.29224 9.58592 4.29224 10.0001ZM4.9029 5.9637C5.1958 6.25659 5.67067 6.25659 5.96356 5.9637C6.25646 5.6708 6.25646 5.19593 5.96356 4.90303L5.07968 4.01915C4.78679 3.72626 4.31191 3.72626 4.01902 4.01915C3.72613 4.31204 3.72613 4.78692 4.01902 5.07981L4.9029 5.9637Z"
                            fill="currentColor" />
                    </svg>
                    <svg class="dark:hidden" width="20" height="20" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M17.4547 11.97L18.1799 12.1611C18.265 11.8383 18.1265 11.4982 17.8401 11.3266C17.5538 11.1551 17.1885 11.1934 16.944 11.4207L17.4547 11.97ZM8.0306 2.5459L8.57989 3.05657C8.80718 2.81209 8.84554 2.44682 8.67398 2.16046C8.50243 1.8741 8.16227 1.73559 7.83948 1.82066L8.0306 2.5459ZM12.9154 13.0035C9.64678 13.0035 6.99707 10.3538 6.99707 7.08524H5.49707C5.49707 11.1823 8.81835 14.5035 12.9154 14.5035V13.0035ZM16.944 11.4207C15.8869 12.4035 14.4721 13.0035 12.9154 13.0035V14.5035C14.8657 14.5035 16.6418 13.7499 17.9654 12.5193L16.944 11.4207ZM16.7295 11.7789C15.9437 14.7607 13.2277 16.9586 10.0003 16.9586V18.4586C13.9257 18.4586 17.2249 15.7853 18.1799 12.1611L16.7295 11.7789ZM10.0003 16.9586C6.15734 16.9586 3.04199 13.8433 3.04199 10.0003H1.54199C1.54199 14.6717 5.32892 18.4586 10.0003 18.4586V16.9586ZM3.04199 10.0003C3.04199 6.77289 5.23988 4.05695 8.22173 3.27114L7.83948 1.82066C4.21532 2.77574 1.54199 6.07486 1.54199 10.0003H3.04199ZM6.99707 7.08524C6.99707 5.52854 7.5971 4.11366 8.57989 3.05657L7.48132 2.03522C6.25073 3.35885 5.49707 5.13487 5.49707 7.08524H6.99707Z"
                            fill="currentColor" />
                    </svg>
                </button>

                @php
                    $licenseKey = config('dravion.license_key', '');
                    $licensed = !empty($licenseKey) && !str_starts_with($licenseKey, 'DEV-');
                    $devLicense = str_starts_with($licenseKey, 'DEV-');
                @endphp
                @if (!$licensed)
                <a href="{{ route('admin.license') }}"
                    class="hidden xl:inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-medium transition-colors
                        {{ $devLicense ? 'border-warning-200 bg-warning-50 text-warning-700 dark:border-warning-800 dark:bg-warning-500/10 dark:text-warning-400' : 'border-error-200 bg-error-50 text-error-700 dark:border-error-800 dark:bg-error-500/10 dark:text-error-400' }}">
                    {{ $devLicense ? 'Dev License' : 'Unlicensed' }}
                </a>
                @endif
            </div>

            <!-- View Site link -->
            <a href="{{ route('home') }}" target="_blank"
               title="{{ __('nav.view_site') }}"
               class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-gray-700 h-11 w-11 hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                </svg>
            </a>

            <!-- Notification Bell -->
            <div class="relative"
                x-data="{
                    open: false,
                    unread: 0,
                    items: [],
                    async load() {
                        const r = await fetch('{{ route('notifications.index') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                        const d = await r.json();
                        this.unread = d.unread_count;
                        this.items  = d.notifications;
                    },
                    async markRead(id) {
                        await fetch(`/notifications/${id}/read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' } });
                        this.items = this.items.map(n => n.id === id ? {...n, read: true} : n);
                        this.unread = this.items.filter(n => !n.read).length;
                    },
                    async markAll() {
                        await fetch('/notifications/read-all', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' } });
                        this.items = this.items.map(n => ({...n, read: true}));
                        this.unread = 0;
                    }
                }"
                x-init="load()"
                @click.outside="open = false">

                <button @click="open = !open"
                    class="relative flex items-center justify-center w-10 h-10 text-gray-500 border border-gray-200 rounded-lg dark:border-gray-800 dark:text-gray-400 lg:h-11 lg:w-11"
                    :class="{ 'bg-gray-100 dark:bg-white/[0.03]': open }">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <span x-show="unread > 0" x-cloak x-text="unread > 9 ? '9+' : unread"
                        class="absolute -top-1 -right-1 flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-brand-500 text-white text-[10px] font-bold leading-none"></span>
                </button>

                <div x-show="open" x-cloak @click.stop
                    class="absolute right-0 mt-2 w-80 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900 z-50">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                        <span class="text-sm font-semibold text-gray-800 dark:text-white">{{ __('nav.notifications') }}</span>
                        <button x-show="unread > 0" x-cloak @click="markAll()"
                            class="text-xs text-brand-600 dark:text-brand-400 hover:underline">{{ __('nav.mark_all_read') }}</button>
                    </div>

                    <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-800">
                        <template x-if="items.length === 0">
                            <div class="px-4 py-6 text-center text-sm text-gray-400">{{ __('nav.no_notifications') }}</div>
                        </template>
                        <template x-for="n in items" :key="n.id">
                            <div class="flex gap-3 px-4 py-3 transition-colors"
                                :class="n.read ? 'opacity-60' : 'bg-brand-50/50 dark:bg-brand-900/20'">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 dark:text-white truncate" x-text="n.title"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="n.body"></p>
                                    <p class="text-xs text-gray-400 mt-1" x-text="n.created"></p>
                                </div>
                                <button x-show="!n.read" @click="markRead(n.id)"
                                    class="flex-shrink-0 w-2 h-2 mt-1.5 rounded-full bg-brand-500 hover:bg-brand-600 cursor-pointer" title="{{ __('nav.mark_read') }}"></button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- User Dropdown -->
            <x-header.user-dropdown />
        </div>
    </div>
</header>
