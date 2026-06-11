<x-layouts.admin title="Dashboard">

@php
$totalUsers     = \App\Models\User::count();
$activeUsers    = \App\Models\User::where('status','active')->count();
$suspendedUsers = \App\Models\User::where('status','suspended')->count();
$recentUsers    = \App\Models\User::with('roles')->latest()->take(8)->get();
@endphp

{{-- Stat cards --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px;">
    <x-ui.stat label="Total Users"  :value="$totalUsers"     color="#5e6ad2" />
    <x-ui.stat label="Active"       :value="$activeUsers"    color="#4ade80" />
    <x-ui.stat label="Suspended"    :value="$suspendedUsers" color="#f87171" />
</div>

{{-- Recent users --}}
<x-ui.card title="Recent Users">
    <x-slot:action>
        <a href="{{ route('admin.users.index') }}" style="color:#5e6ad2; font-size:12px; text-decoration:none;">View all →</a>
    </x-slot:action>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-family:Inter,system-ui; font-size:13px;">
            <thead>
                <tr style="border-bottom:1px solid #2a2a35;">
                    <th style="text-align:left; padding:10px 12px; color:#6b6b7b; font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">Name</th>
                    <th style="text-align:left; padding:10px 12px; color:#6b6b7b; font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">Email</th>
                    <th style="text-align:left; padding:10px 12px; color:#6b6b7b; font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">Role</th>
                    <th style="text-align:left; padding:10px 12px; color:#6b6b7b; font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentUsers as $user)
                <tr style="border-bottom:1px solid #1e1e27;" onmouseover="this.style.background='#111113'" onmouseout="this.style.background=''">
                    <td style="padding:10px 12px; color:#e2e2e9;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <x-ui.avatar :name="$user->name" :size="28" />
                            {{ $user->name }}
                        </div>
                    </td>
                    <td style="padding:10px 12px; color:#6b6b7b;">{{ $user->email }}</td>
                    <td style="padding:10px 12px;">
                        <x-ui.badge variant="accent">{{ $user->getRoleNames()->first() ?? '—' }}</x-ui.badge>
                    </td>
                    <td style="padding:10px 12px;">
                        <x-ui.badge variant="{{ $user->status === 'active' ? 'success' : 'danger' }}">{{ $user->status }}</x-ui.badge>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-ui.card>

</x-layouts.admin>
