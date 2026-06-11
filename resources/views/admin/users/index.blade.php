<x-layouts.admin title="Users" breadcrumb="Manage all system users">

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
    <p style="color:#6b6b7b; font-size:12px; margin:0;">{{ $users->total() }} users total</p>
    <x-ui.button href="{{ route('admin.users.create') }}" tag="a" size="sm">+ New User</x-ui.button>
</div>

<x-ui.card>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-family:Inter,system-ui; font-size:13px;">
            <thead>
                <tr style="border-bottom:1px solid #2a2a35;">
                    <th style="text-align:left; padding:10px 12px; color:#6b6b7b; font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">User</th>
                    <th style="text-align:left; padding:10px 12px; color:#6b6b7b; font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">Role</th>
                    <th style="text-align:left; padding:10px 12px; color:#6b6b7b; font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">Status</th>
                    <th style="text-align:left; padding:10px 12px; color:#6b6b7b; font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">Joined</th>
                    <th style="padding:10px 12px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr style="border-bottom:1px solid #1e1e27;" onmouseover="this.style.background='#111113'" onmouseout="this.style.background=''">
                    <td style="padding:10px 12px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <x-ui.avatar :name="$user->name" :size="30" />
                            <div>
                                <p style="color:#e2e2e9; font-weight:500; margin:0; font-size:13px;">{{ $user->name }}</p>
                                <p style="color:#6b6b7b; font-size:11px; margin:2px 0 0;">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td style="padding:10px 12px;">
                        <x-ui.badge variant="accent">{{ $user->getRoleNames()->first() ?? '—' }}</x-ui.badge>
                    </td>
                    <td style="padding:10px 12px;">
                        <x-ui.badge variant="{{ $user->status === 'active' ? 'success' : 'danger' }}">{{ $user->status }}</x-ui.badge>
                    </td>
                    <td style="padding:10px 12px; color:#6b6b7b; font-size:12px;">{{ $user->created_at->format('M d, Y') }}</td>
                    <td style="padding:10px 12px;">
                        <div style="display:flex; align-items:center; gap:6px; justify-content:flex-end;">
                            <x-ui.button href="{{ route('admin.users.edit', $user) }}" tag="a" variant="secondary" size="sm">Edit</x-ui.button>

                            @if($user->id !== auth()->id())
                                @if($user->status === 'active')
                                <x-ui.alert-dialog
                                    title="Suspend {{ $user->name }}?"
                                    description="The user will be immediately logged out and blocked from signing in."
                                    confirm="Suspend"
                                    action="{{ route('admin.users.suspend', $user) }}"
                                >
                                    <x-slot:trigger>
                                        <x-ui.button variant="danger" size="sm">Suspend</x-ui.button>
                                    </x-slot:trigger>
                                </x-ui.alert-dialog>
                                @else
                                <form method="POST" action="{{ route('admin.users.activate', $user) }}" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <x-ui.button type="submit" variant="ghost" size="sm" style="color:#4ade80; border-color:#14532d50;">Activate</x-ui.button>
                                </form>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:40px 12px; text-align:center; color:#6b6b7b; font-size:13px;">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div style="padding:16px 12px; border-top:1px solid #2a2a35;">
        <x-ui.pagination
            :current="$users->currentPage()"
            :total="$users->lastPage()"
            url="{{ route('admin.users.index') }}"
        />
    </div>
    @endif
</x-ui.card>

</x-layouts.admin>
