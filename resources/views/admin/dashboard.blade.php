<x-layouts.admin title="Dashboard">

<x-slot:actions>
    <div style="display:flex; align-items:center; gap:6px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08); border-radius:7px; padding:5px 10px; color:#8a9aba; font-size:12px; cursor:pointer;"
        onmouseover="this.style.background='rgba(255,255,255,0.09)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        {{ now()->format('M d') }} — {{ now()->addDays(30)->format('M d, Y') }}
    </div>
    <div style="display:flex; align-items:center; gap:5px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08); border-radius:7px; padding:5px 10px; color:#8a9aba; font-size:12px; cursor:pointer;"
        onmouseover="this.style.background='rgba(255,255,255,0.09)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
        Monthly
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
    </div>
    <div style="display:flex; align-items:center; gap:5px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08); border-radius:7px; padding:5px 10px; color:#8a9aba; font-size:12px; cursor:pointer;"
        onmouseover="this.style.background='rgba(255,255,255,0.09)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
        Filter
    </div>
    <div style="display:flex; align-items:center; gap:5px; background:rgba(94,106,210,0.15); border:1px solid rgba(94,106,210,0.3); border-radius:7px; padding:5px 10px; color:#a5b0f5; font-size:12px; cursor:pointer; font-weight:500;"
        onmouseover="this.style.background='rgba(94,106,210,0.25)'" onmouseout="this.style.background='rgba(94,106,210,0.15)'">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export
    </div>
</x-slot:actions>

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
