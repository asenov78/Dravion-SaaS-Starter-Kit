<x-layouts.admin :title="__('roles.title')">

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('roles.title') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('roles.subtitle') }}</p>
    </div>
</div>

@if($errors->any())
    <x-ui.alert variant="error" :message="$errors->first()" class="mb-6" />
@endif

{{-- Create role --}}
<div class="mb-6 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('roles.roles') }}</h3>
    </div>
    <div class="p-6">
        <form method="POST" action="{{ route('admin.roles.store') }}" class="flex gap-3 mb-6">
            @csrf
            <input type="text" name="name" placeholder="{{ __('roles.new_placeholder') }}"
                class="h-11 flex-1 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
            <button type="submit"
                class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                {{ __('roles.add') }}
            </button>
        </form>

        <div class="flex flex-wrap gap-3">
            @foreach($roles as $role)
            <div x-data="{ editing: false, name: '{{ addslashes($role->name) }}' }"
                class="flex items-center gap-2 rounded-full border border-gray-200 bg-gray-50 px-4 py-2 dark:border-gray-700 dark:bg-gray-800">

                {{-- View mode --}}
                <span x-show="!editing" class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $role->name }}</span>

                {{-- Edit mode --}}
                @if($role->name !== 'admin')
                <form x-show="editing" method="POST" action="{{ route('admin.roles.update', $role) }}" class="flex items-center gap-1.5" @submit.prevent="$el.submit()">
                    @csrf @method('PUT')
                    <input type="text" name="name" x-model="name" x-ref="input"
                        class="h-7 w-36 rounded-md border border-brand-300 bg-white px-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-brand-700 dark:bg-gray-900 dark:text-white/90">
                    <button type="submit" class="text-success-600 hover:text-success-700" title="{{ __('app.save') }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </button>
                    <button type="button" @click="editing = false; name = '{{ addslashes($role->name) }}'" class="text-gray-400 hover:text-gray-600" title="{{ __('app.cancel') }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </form>

                {{-- Action buttons (view mode) --}}
                <div x-show="!editing" class="flex items-center gap-1">
                    <button type="button" @click="editing = true; $nextTick(() => $refs.input.focus())"
                        class="text-gray-400 hover:text-brand-500 transition-colors" title="{{ __('roles.rename') }}">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <button type="button"
                        @click="$dispatch('confirm-action', { title: '{{ addslashes(__('roles.confirm_delete_title', ['name' => $role->name])) }}', message: '{{ addslashes(__('roles.confirm_delete_msg', ['name' => $role->name])) }}', btnLabel: '{{ addslashes(__('app.delete')) }}', btnColor: '#dc2626', url: '{{ route('admin.roles.destroy', $role) }}', method: 'DELETE', successAction: 'redirect', targetId: '{{ route('admin.roles.index') }}', toastMessage: '{{ addslashes(__('flash.role_deleted')) }}', toastVariant: 'error' })"
                        class="text-gray-400 hover:text-error-500 transition-colors" title="{{ __('app.delete') }}">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </button>
                </div>
                @else
                <span class="ml-1 text-xs text-gray-400">{{ __('roles.protected') }}</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Permissions matrix --}}
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <div>
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('roles.matrix') }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('roles.matrix_desc') }}</p>
        </div>
        <button type="submit" form="matrix-form"
            class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
            {{ __('roles.save') }}
        </button>
    </div>

    <form id="matrix-form" method="POST" action="{{ route('admin.roles.permissions') }}">
        @csrf @method('PUT')
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 min-w-[180px]">{{ __('roles.permission') }}</th>
                        @foreach($roles->where('name', '!=', 'admin') as $role)
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ $role->name }}</th>
                        @endforeach
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @php
                        $grouped = $permissions->groupBy(fn($p) => explode(' ', $p->name, 2)[1] ?? $p->name);
                    @endphp
                    @foreach($grouped as $group => $groupPerms)
                    <tr>
                        <td colspan="{{ $roles->count() + 1 }}" class="px-6 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-800">
                            {{ trans('roles.groups')[$group] ?? $group }}
                        </td>
                    </tr>
                    @foreach($groupPerms as $permission)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-300 pl-8">{{ __('permissions.' . $permission->name) }}</td>
                        @foreach($roles->where('name', '!=', 'admin') as $role)
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox"
                                name="matrix[{{ $role->id }}][]"
                                value="{{ $permission->id }}"
                                {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-700">
                        </td>
                        @endforeach
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" checked disabled
                                class="h-4 w-4 rounded border-gray-300 text-brand-500 opacity-50 cursor-not-allowed">
                        </td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex justify-end">
            <button type="submit"
                class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                {{ __('roles.save') }}
            </button>
        </div>
    </form>
</div>

</x-layouts.admin>
