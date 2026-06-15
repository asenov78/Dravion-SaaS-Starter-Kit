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
<div class="mb-6 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('roles.roles') }}</h3>
    </div>
    <div class="p-6">
        <form method="POST" action="{{ route('admin.roles.store') }}" class="flex gap-3 mb-6">
            @csrf
            <input type="text" name="name" placeholder="{{ __('roles.new_placeholder') }}"
                class="h-11 flex-1 max-w-xs rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
            <button type="submit"
                class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                {{ __('roles.add') }}
            </button>
        </form>

        <div class="flex flex-wrap gap-3">
            @foreach($roles as $role)
            <div class="flex items-center gap-2 rounded-full border border-gray-200 bg-gray-50 px-4 py-2 dark:border-gray-700 dark:bg-gray-800">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $role->name }}</span>
                @if($role->name !== 'admin')
                <button type="button" x-data
                    @click="$dispatch('confirm-action', { title: '{{ addslashes(__('roles.confirm_delete_title', ['name' => $role->name])) }}', message: '{{ addslashes(__('roles.confirm_delete_msg', ['name' => $role->name])) }}', btnLabel: '{{ addslashes(__('app.delete')) }}', btnColor: '#dc2626', url: '{{ route('admin.roles.destroy', $role) }}', method: 'DELETE', successAction: 'redirect', targetId: '{{ route('admin.roles.index') }}', toastMessage: '{{ addslashes(__('flash.role_deleted')) }}', toastVariant: 'error' })"
                    class="ml-1 text-gray-400 hover:text-red-500 transition-colors" title="{{ __('app.delete') }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                @else
                <span class="ml-1 text-xs text-gray-400">{{ __('roles.protected') }}</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Permissions matrix --}}
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
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
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
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
