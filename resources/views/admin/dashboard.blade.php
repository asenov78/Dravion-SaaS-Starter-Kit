<x-layouts.admin title="Dashboard">

@php
$totalUsers     = \App\Models\User::count();
$activeUsers    = \App\Models\User::where('status','active')->count();
$suspendedUsers = \App\Models\User::where('status','suspended')->count();
$recentUsers    = \App\Models\User::with('roles')->latest()->take(8)->get();
@endphp

{{-- Stat cards --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:16px; margin-bottom:24px;">
    <x-ui.stat label="Total Users"  :value="$totalUsers"     color="#5e6ad2" />
    <x-ui.stat label="Active"       :value="$activeUsers"    color="#4ade80" />
    <x-ui.stat label="Suspended"    :value="$suspendedUsers" color="#f87171" />
</div>

{{-- Recent users --}}
<x-ui.card title="Recent Users">
    <x-slot:action>
        <x-ui.button href="{{ route('admin.users.index') }}" tag="a" variant="ghost" size="sm">View all →</x-ui.button>
    </x-slot:action>

    <div style="overflow-x:auto; margin:-20px;">
        <table style="width:100%; border-collapse:collapse; font-family:Inter,system-ui; font-size:13px;">
            <thead>
                <tr style="border-bottom:1px solid #2a2a35;">
                    <th style="text-align:left; padding:10px 20px; color:#6b6b7b; font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">User</th>
                    <th style="text-align:left; padding:10px 12px; color:#6b6b7b; font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">Role</th>
                    <th style="text-align:left; padding:10px 12px; color:#6b6b7b; font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">Status</th>
                    <th style="text-align:left; padding:10px 20px; color:#6b6b7b; font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">Joined</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentUsers as $user)
                <tr style="border-bottom:1px solid #1e1e27;" onmouseover="this.style.background='#0d0d10'" onmouseout="this.style.background=''">
                    <td style="padding:10px 20px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <x-ui.avatar :name="$user->name" :size="28" />
                            <div>
                                <p style="color:#e2e2e9; font-weight:500; margin:0; font-size:13px;">{{ $user->name }}</p>
                                <p style="color:#4b4b5b; font-size:11px; margin:1px 0 0;">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td style="padding:10px 12px;">
                        <x-ui.badge variant="accent">{{ $user->getRoleNames()->first() ?? '—' }}</x-ui.badge>
                    </td>
                    <td style="padding:10px 12px;">
                        <x-ui.badge variant="{{ $user->status === 'active' ? 'success' : 'danger' }}">{{ $user->status }}</x-ui.badge>
                    </td>
                    <td style="padding:10px 20px; color:#6b6b7b; font-size:12px;">{{ $user->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="padding:32px 20px; text-align:center; color:#6b6b7b; font-size:13px;">No users yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-ui.card>

</x-layouts.admin>
