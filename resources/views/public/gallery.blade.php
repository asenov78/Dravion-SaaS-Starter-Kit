@extends('layouts.public')
@section('meta_title', __('gallery.title') . ' — ' . config('app.name'))

@section('content')
@php
    $galleryBg = $galleryPage?->hero_image ?? 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1920&q=80';
    $galleryTitle = $galleryPage?->hero_title ?? __('gallery.title');
    $gallerySubtitle = $galleryPage?->hero_subtitle ?? __('gallery.subtitle');
@endphp
{{-- Hero --}}
<section class="relative overflow-hidden py-20 sm:py-24 flex items-center"
    style="background-image: url('{{ $galleryBg }}'); background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-gradient-to-br from-gray-950/85 via-gray-900/75 to-brand-900/60" aria-hidden="true"></div>
    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center py-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white/10 text-white mb-6 backdrop-blur-sm">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-4 drop-shadow-lg">{{ $galleryTitle }}</h1>
        <p class="text-lg text-white/70 max-w-xl mx-auto">{{ $gallerySubtitle }}</p>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
    <div class="text-center mb-16" style="display:none"></div>

    {{-- ── DASHBOARD MOCKUP ── --}}
    <div class="mb-8">
        <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-brand-500"></span> Admin Dashboard
        </h2>
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden shadow-xl">
            {{-- Browser bar --}}
            <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="w-3 h-3 rounded-full bg-error-400"></div><div class="w-3 h-3 rounded-full bg-warning-400"></div><div class="w-3 h-3 rounded-full bg-success-400"></div>
                <div class="flex-1 ml-3 h-5 rounded-md bg-white dark:bg-gray-700 text-xs text-gray-400 flex items-center px-2">localhost/admin/dashboard</div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 flex" style="min-height:320px">
                {{-- Sidebar --}}
                <div class="w-16 bg-white dark:bg-gray-950 border-r border-gray-200 dark:border-gray-800 flex flex-col items-center py-4 gap-3">
                    <div class="w-8 h-8 rounded-lg bg-brand-500 mb-2"></div>
                    @foreach(['M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6','M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197','M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2','M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37'] as $i => $p)
                    <div class="w-9 h-9 rounded-xl {{ $i===0 ? 'bg-brand-50 dark:bg-brand-500/20 text-brand-500' : 'text-gray-400 hover:text-gray-600' }} flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $p }}"/></svg>
                    </div>
                    @endforeach
                </div>
                {{-- Main --}}
                <div class="flex-1 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-5 w-32 rounded bg-gray-200 dark:bg-gray-700"></div>
                        <div class="flex gap-2"><div class="h-7 w-20 rounded-lg bg-brand-500/20"></div><div class="h-7 w-7 rounded-full bg-gray-200 dark:bg-gray-700"></div></div>
                    </div>
                    <div class="grid grid-cols-4 gap-3 mb-5">
                        @foreach([['12','Потребители','brand'],['87','Действия','success'],['3','Роли','warning'],['1','Обновления','error']] as [$n,$l,$c])
                        @php $bg=['brand'=>'bg-brand-50 dark:bg-brand-500/10','success'=>'bg-success-50 dark:bg-success-500/10','warning'=>'bg-warning-50 dark:bg-warning-500/10','error'=>'bg-error-50 dark:bg-error-500/10'][$c]; $tc=['brand'=>'text-brand-600','success'=>'text-success-600','warning'=>'text-warning-600','error'=>'text-error-600'][$c]; @endphp
                        <div class="rounded-xl {{ $bg }} p-3">
                            <div class="text-lg font-bold {{ $tc }}">{{ $n }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $l }}</div>
                        </div>
                        @endforeach
                    </div>
                    <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-hidden">
                        <div class="px-4 py-2.5 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
                            <div class="h-3 w-24 rounded bg-gray-200 dark:bg-gray-700"></div>
                            <div class="ml-auto flex gap-2"><div class="h-5 w-16 rounded bg-gray-100 dark:bg-gray-800"></div><div class="h-5 w-16 rounded bg-gray-100 dark:bg-gray-800"></div></div>
                        </div>
                        @foreach([['Admin','logged in','success'],['User #3','suspended','error'],['Settings','saved','brand'],['Update','installed','warning']] as [$who,$what,$c])
                        @php $dot=['success'=>'bg-success-400','error'=>'bg-error-400','brand'=>'bg-brand-400','warning'=>'bg-warning-400'][$c]; @endphp
                        <div class="flex items-center gap-3 px-4 py-2.5 border-b border-gray-50 dark:border-gray-800/60 last:border-0">
                            <div class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-800 flex-shrink-0"></div>
                            <div class="flex-1 h-3 rounded bg-gray-100 dark:bg-gray-800"></div>
                            <div class="w-2 h-2 rounded-full {{ $dot }}"></div>
                            <div class="h-3 w-16 rounded bg-gray-100 dark:bg-gray-800"></div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── COMPONENT GRID ── --}}
    <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-success-500"></span> UI Components
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">

        {{-- Buttons --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Buttons</p>
            <div class="flex flex-wrap gap-2">
                <button class="px-3.5 py-2 rounded-lg bg-brand-500 text-white text-xs font-semibold">Primary</button>
                <button class="px-3.5 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs font-semibold">Secondary</button>
                <button class="px-3.5 py-2 rounded-lg bg-error-500 text-white text-xs font-semibold">Danger</button>
                <button class="px-3.5 py-2 rounded-lg bg-success-500 text-white text-xs font-semibold">Success</button>
                <button class="px-3.5 py-2 rounded-lg bg-warning-500 text-white text-xs font-semibold">Warning</button>
                <button class="px-3.5 py-2 rounded-lg text-brand-500 text-xs font-semibold hover:bg-brand-50 dark:hover:bg-brand-500/10">Ghost</button>
            </div>
        </div>

        {{-- Badges --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Badges</p>
            <div class="flex flex-wrap gap-2">
                @foreach([['Active','success'],['Pending','warning'],['Suspended','error'],['Admin','brand'],['Editor','purple-500'],['New','gray-500']] as [$l,$c])
                @php $cls=['success'=>'bg-success-50 dark:bg-success-500/10 text-success-700 dark:text-success-400 border-success-200 dark:border-success-500/20','warning'=>'bg-warning-50 dark:bg-warning-500/10 text-warning-700 dark:text-warning-400 border-warning-200 dark:border-warning-500/20','error'=>'bg-error-50 dark:bg-error-500/10 text-error-700 dark:text-error-400 border-error-200 dark:border-error-500/20','brand'=>'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400 border-brand-200 dark:border-brand-500/20','purple-500'=>'bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-200 dark:border-purple-500/20','gray-500'=>'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-400 border-gray-200 dark:border-gray-700'][$c] @endphp
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $cls }}">{{ $l }}</span>
                @endforeach
            </div>
        </div>

        {{-- Alerts --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Alerts</p>
            <div class="space-y-2">
                @foreach([['Операцията е успешна!','success','M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],['Предупреждение!','warning','M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],['Грешка!','error','M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z']] as [$msg,$c,$icon])
                @php $a=['success'=>'bg-success-50 dark:bg-success-500/10 border-success-200 dark:border-success-500/20 text-success-700 dark:text-success-400','warning'=>'bg-warning-50 dark:bg-warning-500/10 border-warning-200 dark:border-warning-500/20 text-warning-700 dark:text-warning-400','error'=>'bg-error-50 dark:bg-error-500/10 border-error-200 dark:border-error-500/20 text-error-700 dark:text-error-400'][$c] @endphp
                <div class="flex items-center gap-2 rounded-lg border {{ $a }} px-3 py-2 text-xs font-medium">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    {{ $msg }}
                </div>
                @endforeach
            </div>
        </div>

        {{-- User table mockup --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Data Table</p>
            <div class="overflow-hidden rounded-lg border border-gray-100 dark:border-gray-800">
                <div class="grid grid-cols-3 gap-2 px-3 py-2 bg-gray-50 dark:bg-gray-800 border-b border-gray-100 dark:border-gray-800">
                    <span class="text-xs font-semibold text-gray-500">Потребител</span>
                    <span class="text-xs font-semibold text-gray-500">Роля</span>
                    <span class="text-xs font-semibold text-gray-500">Статус</span>
                </div>
                @foreach([['Иван И.','Admin','success'],['Мария П.','Editor','brand'],['Петър С.','User','warning'],['Анна Т.','Manager','brand']] as [$name,$role,$c])
                @php $bs=['success'=>'bg-success-50 text-success-700','brand'=>'bg-brand-50 text-brand-700','warning'=>'bg-warning-50 text-warning-700'][$c] @endphp
                <div class="grid grid-cols-3 gap-2 px-3 py-2 border-b border-gray-50 dark:border-gray-800/60 last:border-0 items-center">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-brand-500 flex items-center justify-center text-white text-xs font-bold">{{ substr($name,0,1) }}</div>
                        <span class="text-xs text-gray-700 dark:text-gray-300">{{ $name }}</span>
                    </div>
                    <span class="text-xs text-gray-500">{{ $role }}</span>
                    <span class="inline-flex px-1.5 py-0.5 rounded text-xs font-medium {{ $bs }}">Active</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Form controls --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Form Controls</p>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Email</label>
                    <div class="w-full h-8 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 flex items-center">
                        <span class="text-xs text-gray-400">user@example.com</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Роля</label>
                    <div class="w-full h-8 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 flex items-center justify-between">
                        <span class="text-xs text-gray-400">Admin</span>
                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-5 rounded-full bg-brand-500 relative"><div class="absolute right-0.5 top-0.5 w-4 h-4 rounded-full bg-white shadow-sm"></div></div>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Активен акаунт</span>
                </div>
            </div>
        </div>

        {{-- Notification bell mockup --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Notifications</p>
            <div class="space-y-3">
                @foreach([['Нов потребител се регистрира','Преди 2 мин','brand',false],['Акаунт е активиран','Преди 15 мин','success',true],['Системата е обновена','Преди 1 ч','warning',true],['Потребител е спрян','Преди 3 ч','error',true]] as [$title,$time,$c,$read])
                @php $dot=['brand'=>'bg-brand-400','success'=>'bg-success-400','warning'=>'bg-warning-400','error'=>'bg-error-400'][$c] @endphp
                <div class="flex items-start gap-3 {{ !$read ? 'opacity-100' : 'opacity-60' }}">
                    <div class="w-2 h-2 rounded-full {{ $dot }} mt-1.5 shrink-0 {{ $read ? 'opacity-0' : '' }}"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-800 dark:text-white/90 truncate">{{ $title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $time }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Dark mode toggle mockup --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Dark / Light Mode</p>
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-white border-2 border-brand-500 p-3">
                    <div class="flex gap-1.5 mb-2"><div class="w-2 h-2 rounded-full bg-gray-200"></div><div class="w-2 h-2 rounded-full bg-gray-200"></div></div>
                    <div class="space-y-1.5"><div class="h-2 rounded bg-gray-100 w-full"></div><div class="h-2 rounded bg-gray-100 w-3/4"></div><div class="h-2 rounded bg-brand-100 w-1/2"></div></div>
                    <p class="text-xs text-center font-medium text-gray-600 mt-3">Light</p>
                </div>
                <div class="rounded-xl bg-gray-900 border-2 border-brand-500 p-3">
                    <div class="flex gap-1.5 mb-2"><div class="w-2 h-2 rounded-full bg-gray-700"></div><div class="w-2 h-2 rounded-full bg-gray-700"></div></div>
                    <div class="space-y-1.5"><div class="h-2 rounded bg-gray-700 w-full"></div><div class="h-2 rounded bg-gray-700 w-3/4"></div><div class="h-2 rounded bg-brand-900 w-1/2"></div></div>
                    <p class="text-xs text-center font-medium text-gray-400 mt-3">Dark</p>
                </div>
            </div>
        </div>

        {{-- Roles & Permissions --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Roles & Permissions</p>
            <div class="space-y-2">
                @foreach([['admin','bg-error-100 dark:bg-error-500/20 text-error-700 dark:text-error-400','view users,edit users,delete users,view settings'],['manager','bg-warning-100 dark:bg-warning-500/20 text-warning-700 dark:text-warning-400','view users,edit users'],['editor','bg-brand-100 dark:bg-brand-500/20 text-brand-700 dark:text-brand-400','view users'],['user','bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400','—']] as [$role,$cls,$perms])
                <div class="flex items-start gap-2.5">
                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold {{ $cls }} shrink-0 mt-0.5">{{ $role }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $perms }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Activity log mockup --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Activity Log</p>
            <div class="space-y-3 relative">
                <div class="absolute left-2 top-0 bottom-0 w-px bg-gray-100 dark:bg-gray-800"></div>
                @foreach([['Admin logged in','web','brand'],['User suspended','admin','error'],['Settings saved','admin','success'],['Update installed','system','warning']] as [$event,$causer,$c])
                @php $dot=['brand'=>'bg-brand-500','error'=>'bg-error-500','success'=>'bg-success-500','warning'=>'bg-warning-500'][$c] @endphp
                <div class="flex items-start gap-3 pl-1">
                    <div class="w-4 h-4 rounded-full {{ $dot }} shrink-0 mt-0.5 flex items-center justify-center z-10">
                        <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-800 dark:text-white/90">{{ $event }}</p>
                        <p class="text-xs text-gray-400">by {{ $causer }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ── CTA ── --}}
    <div class="rounded-2xl bg-gradient-to-r from-brand-500 to-purple-600 p-8 text-center text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="g" width="32" height="32" patternUnits="userSpaceOnUse"><path d="M 32 0 L 0 0 0 32" fill="none" stroke="white" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(#g)"/></svg>
        </div>
        <div class="relative">
            <h3 class="text-2xl font-bold mb-2">Харесва ли ви?</h3>
            <p class="text-white/80 mb-6">Свържете се с нас или стартирайте още днес.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('contact') }}" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg bg-white text-brand-600 font-semibold text-sm hover:bg-brand-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Свържи се с нас
                </a>
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg border-2 border-white/30 text-white font-semibold text-sm hover:bg-white/10 transition-colors">
                    Стартирай безплатно
                </a>
            </div>
        </div>
    </div>

</div>
@endsection