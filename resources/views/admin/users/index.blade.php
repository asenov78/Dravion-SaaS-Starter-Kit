<x-layouts.admin :title="__('users.title')">

<div
    x-data="{
        selected: [],
        toggleAll(ids) {
            if (this.selected.length === ids.length) { this.selected = []; }
            else { this.selected = [...ids]; }
        }
    }"
>

{{-- Page header --}}
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('users.title') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $users->total() }} {{ Str::lower(__('users.title')) }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.users.export', request()->only('search','role','status')) }}"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-transparent dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            {{ __('users.export_csv') }}
        </a>
        <a href="{{ route('admin.users.create') }}"
            class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            {{ __('users.add') }}
        </a>
    </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.users.index') }}" class="mb-4 flex flex-wrap gap-3">
    @if($trashed)<input type="hidden" name="trashed" value="1">@endif
    <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('app.search_users') }}"
        class="h-10 flex-1 min-w-48 rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />

    <select name="role" onchange="this.form.submit()"
        class="h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
        <option value="">{{ __('users.filter_role') }}</option>
        @foreach($roles as $r)
        <option value="{{ $r }}" {{ $role === $r ? 'selected' : '' }}>{{ $r }}</option>
        @endforeach
    </select>

    <select name="status" onchange="this.form.submit()"
        class="h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
        <option value="">{{ __('users.filter_status') }}</option>
        <option value="active"    {{ $status === 'active'    ? 'selected' : '' }}>{{ __('app.active') }}</option>
        <option value="suspended" {{ $status === 'suspended' ? 'selected' : '' }}>{{ __('app.suspended') }}</option>
    </select>

    <button type="submit" class="h-10 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
        {{ __('app.search') }}
    </button>

    @if($search || $role || $status)
    <a href="{{ route('admin.users.index', array_filter(['trashed' => $trashed ?: null])) }}"
        class="h-10 inline-flex items-center rounded-lg border border-gray-300 px-4 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors">
        {{ __('app.cancel') }}
    </a>
    @endif
</form>

{{-- Bulk action bar --}}
@if(!$trashed)
<div x-show="selected.length > 0" x-cloak
    class="mb-4 flex items-center gap-3 rounded-xl border border-brand-200 bg-brand-50 px-4 py-2.5 dark:border-brand-800 dark:bg-brand-500/10">
    <span class="text-sm font-medium text-brand-700 dark:text-brand-300">
        <span x-text="selected.length"></span> {{ __('users.selected') }}
    </span>
    <div class="flex flex-wrap items-center gap-2 ml-auto">
        <form method="POST" action="{{ route('admin.users.bulk') }}" @submit.prevent="if(selected.length) $el.submit()">
            @csrf
            <input type="hidden" name="action" value="activate">
            <template x-for="id in selected"><input type="hidden" name="ids[]" :value="id"></template>
            <button type="submit" class="inline-flex items-center rounded-lg border border-success-200 bg-success-50 px-3 py-1.5 text-xs font-medium text-success-700 hover:bg-success-100 dark:border-success-700 dark:bg-success-500/10 dark:text-success-400">
                {{ __('users.activate') }}
            </button>
        </form>
        <form method="POST" action="{{ route('admin.users.bulk') }}" @submit.prevent="if(selected.length) $el.submit()">
            @csrf
            <input type="hidden" name="action" value="suspend">
            <template x-for="id in selected"><input type="hidden" name="ids[]" :value="id"></template>
            <button type="submit" class="inline-flex items-center rounded-lg border border-warning-200 bg-warning-50 px-3 py-1.5 text-xs font-medium text-warning-700 hover:bg-warning-100 dark:border-warning-700 dark:bg-warning-500/10 dark:text-warning-400">
                {{ __('users.suspend') }}
            </button>
        </form>
        @role('admin')
        <form method="POST" action="{{ route('admin.users.bulk') }}" @submit.prevent="if(selected.length) $el.submit()">
            @csrf
            <input type="hidden" name="action" value="delete">
            <template x-for="id in selected"><input type="hidden" name="ids[]" :value="id"></template>
            <button type="submit" class="inline-flex items-center rounded-lg border border-error-200 bg-error-50 px-3 py-1.5 text-xs font-medium text-error-700 hover:bg-error-100 dark:border-error-700 dark:bg-error-500/10 dark:text-error-400">
                {{ __('app.delete') }}
            </button>
        </form>
        @endrole
        <button type="button" @click="selected = []"
            class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
            {{ __('app.cancel') }}
        </button>
    </div>
</div>
@endif

{{-- Tabs: All / Trash --}}
<div class="mb-4 flex gap-1 border-b border-gray-200 dark:border-gray-800">
    <a href="{{ route('admin.users.index', array_filter(['search'=>$search,'role'=>$role,'status'=>$status])) }}"
        class="px-4 py-2 text-sm font-medium border-b-2 transition-colors {{ !$trashed ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
        {{ __('users.all_users') }}
    </a>
    @role('admin')
    <a href="{{ route('admin.users.index', ['trashed'=>1]) }}"
        class="px-4 py-2 text-sm font-medium border-b-2 transition-colors {{ $trashed ? 'border-error-500 text-error-600 dark:text-error-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
        {{ __('users.trash') }}
    </a>
    @endrole
</div>

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    @if(!$trashed)
                    <th class="w-10 px-4 py-3">
                        <input type="checkbox"
                            @change="toggleAll({{ $users->pluck('id')->toJson() }})"
                            :checked="selected.length === {{ $users->count() }} && {{ $users->count() }} > 0"
                            class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800">
                    </th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('app.name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('app.role') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('app.status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">2FA</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ $trashed ? __('users.deleted_at') : __('app.created_at') }}</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($users as $user)
                <tr id="row-{{ $user->id }}"
                    x-data="{ status: '{{ $user->status }}' }"
                    @user-status-updated.window="if ($event.detail.id === {{ $user->id }}) status = $event.detail.newStatus"
                    :class="selected.includes({{ $user->id }}) ? 'bg-brand-50 dark:bg-brand-500/5' : 'hover:bg-gray-50 dark:hover:bg-gray-800'"
                    class="transition-colors {{ $trashed ? 'opacity-60' : '' }}">
                    @if(!$trashed)
                    <td class="w-10 px-4 py-3">
                        <input type="checkbox" :value="{{ $user->id }}" x-model="selected"
                            class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800">
                    </td>
                    @endif
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-9 h-9 rounded-full {{ $trashed ? 'bg-gray-400' : 'bg-brand-500' }} text-white text-sm font-semibold flex-shrink-0 overflow-hidden">
                                @if($user->avatar)
                                    <img src="{{ url('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </span>
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3">
                        <span class="inline-block px-2.5 py-0.5 text-xs font-medium rounded-full bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                            {{ $user->getRoleNames()->first() ?? '—' }}
                        </span>
                    </td>
                    <td class="px-6 py-3">
                        @if($trashed)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>{{ __('app.deleted') }}
                        </span>
                        @else
                        <span x-show="status === 'active'" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-medium rounded-full bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-success-500"></span>{{ __('app.active') }}
                        </span>
                        <span x-show="status !== 'active'" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-medium rounded-full bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-error-500"></span>{{ __('app.suspended') }}
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-3">
                        @if($user->two_factor_confirmed_at)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            {{ __('users.2fa_on') }}
                        </span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                            {{ __('users.2fa_off') }}
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">
                        {{ ($trashed ? $user->deleted_at : $user->created_at)->locale(app()->getLocale())->isoFormat('D MMM YYYY') }}
                    </td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2 justify-end">
                            @if($trashed)
                                @role('admin')
                                <button type="button"
                                    @click="$dispatch('confirm-action', { title: '{{ addslashes(__('users.restore')) }} {{ addslashes($user->name) }}?', message: '{{ addslashes(__('users.confirm_restore', ['name' => $user->name])) }}', btnLabel: '{{ addslashes(__('users.restore')) }}', btnColor: '#16a34a', url: '{{ route('admin.users.restore', $user->id) }}', method: 'PATCH', successAction: 'remove', targetId: 'row-{{ $user->id }}', toastMessage: '{{ addslashes(__('flash.user_restored')) }}', toastVariant: 'success' })"
                                    class="inline-flex items-center rounded-lg border border-success-200 bg-success-50 px-3 py-1.5 text-xs font-medium text-success-700 hover:bg-success-100 dark:border-success-800 dark:bg-success-500/10 dark:text-success-400">
                                    {{ __('users.restore') }}
                                </button>
                                @endrole
                            @else
                                <a href="{{ route('admin.users.edit', $user) }}"
                                    class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                                    {{ __('app.edit') }}
                                </a>

                                @if($user->id !== auth()->id())
                                    <button type="button" x-show="status === 'active'"
                                        @click="$dispatch('confirm-action', { title: '{{ addslashes(__('users.suspend')) }} {{ addslashes($user->name) }}?', message: '{{ addslashes(__('users.confirm_suspend', ['name' => $user->name])) }}', btnLabel: '{{ addslashes(__('users.suspend')) }}', btnColor: '#f59e0b', url: '{{ route('admin.users.suspend', $user) }}', method: 'PATCH', successAction: 'suspended', userId: {{ $user->id }}, toastMessage: '{{ addslashes(__('flash.user_suspended')) }}', toastVariant: 'warning' })"
                                        class="inline-flex items-center rounded-lg border border-warning-200 bg-warning-50 px-3 py-1.5 text-xs font-medium text-warning-700 hover:bg-warning-100 dark:border-warning-800 dark:bg-warning-500/10 dark:text-warning-400">
                                        {{ __('users.suspend') }}
                                    </button>
                                    <button type="button" x-show="status !== 'active'"
                                        @click="$dispatch('confirm-action', { title: '{{ addslashes(__('users.activate')) }} {{ addslashes($user->name) }}?', message: '{{ addslashes(__('users.confirm_activate', ['name' => $user->name])) }}', btnLabel: '{{ addslashes(__('users.activate')) }}', btnColor: '#16a34a', url: '{{ route('admin.users.activate', $user) }}', method: 'PATCH', successAction: 'active', userId: {{ $user->id }}, toastMessage: '{{ addslashes(__('flash.user_activated')) }}', toastVariant: 'success' })"
                                        class="inline-flex items-center rounded-lg border border-success-200 bg-success-50 px-3 py-1.5 text-xs font-medium text-success-700 hover:bg-success-100 dark:border-success-800 dark:bg-success-500/10 dark:text-success-400">
                                        {{ __('users.activate') }}
                                    </button>

                                    @role('admin')
                                    <button type="button"
                                        @click="$dispatch('confirm-action', { title: '{{ addslashes(__('app.delete')) }} {{ addslashes($user->name) }}?', message: '{{ addslashes(__('users.confirm_delete', ['name' => $user->name])) }}', btnLabel: '{{ addslashes(__('app.delete')) }}', btnColor: '#dc2626', url: '{{ route('admin.users.destroy', $user) }}', method: 'DELETE', successAction: 'remove', targetId: 'row-{{ $user->id }}', toastMessage: '{{ addslashes(__('flash.user_deleted')) }}', toastVariant: 'error' })"
                                        class="inline-flex items-center rounded-lg border border-error-300 bg-error-100 px-3 py-1.5 text-xs font-medium text-error-800 hover:bg-error-200 dark:border-error-700 dark:bg-error-500/20 dark:text-error-300">
                                        {{ __('app.delete') }}
                                    </button>
                                    @endrole
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $trashed ? 5 : 6 }}" class="px-6 py-12 text-center text-sm text-gray-400 dark:text-gray-500">
                        {{ __('app.no_results') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
        {{ $users->links() }}
    </div>
    @endif
</div>

</div>{{-- end x-data --}}
</x-layouts.admin>
