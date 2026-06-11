<x-layouts.admin title="Dashboard">

{{-- Stat cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    @php
        $stats = [
            ['label' => 'Total Users',   'value' => \App\Models\User::count(),                              'color' => '#5e6ad2'],
            ['label' => 'Active',        'value' => \App\Models\User::where('status','active')->count(),    'color' => '#4ade80'],
            ['label' => 'Suspended',     'value' => \App\Models\User::where('status','suspended')->count(), 'color' => '#f87171'],
        ];
    @endphp
    @foreach($stats as $s)
    <div class="rounded-lg p-5 border" style="background:#1a1a1f; border-color:#2a2a35;">
        <p class="text-xs mb-2" style="color:#6b6b7b;">{{ $s['label'] }}</p>
        <p class="text-3xl font-semibold" style="color:{{ $s['color'] }};">{{ $s['value'] }}</p>
    </div>
    @endforeach
</div>

{{-- Recent users table --}}
<div class="rounded-lg border" style="background:#1a1a1f; border-color:#2a2a35;">
    <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:#2a2a35;">
        <h2 class="text-sm font-semibold" style="color:#e2e2e9;">Recent Users</h2>
        <a href="{{ route('admin.users.index') }}" class="text-xs" style="color:#5e6ad2;">View all →</a>
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr style="border-bottom:1px solid #2a2a35;">
                <th class="text-left px-5 py-3 text-xs font-medium" style="color:#6b6b7b;">Name</th>
                <th class="text-left px-5 py-3 text-xs font-medium" style="color:#6b6b7b;">Email</th>
                <th class="text-left px-5 py-3 text-xs font-medium" style="color:#6b6b7b;">Role</th>
                <th class="text-left px-5 py-3 text-xs font-medium" style="color:#6b6b7b;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach(\App\Models\User::with('roles')->latest()->take(8)->get() as $user)
            <tr style="border-bottom:1px solid #1e1e27;" onmouseover="this.style.background='#111113'" onmouseout="this.style.background=''">
                <td class="px-5 py-3 font-medium" style="color:#e2e2e9;">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold text-white flex-shrink-0" style="background:#5e6ad2;">{{ substr($user->name,0,1) }}</div>
                        {{ $user->name }}
                    </div>
                </td>
                <td class="px-5 py-3" style="color:#6b6b7b;">{{ $user->email }}</td>
                <td class="px-5 py-3">
                    <span class="text-xs px-2 py-0.5 rounded-md" style="background:#1e1e2e; color:#5e6ad2; border:1px solid #2a2a35;">{{ $user->getRoleNames()->first() ?? '—' }}</span>
                </td>
                <td class="px-5 py-3">
                    @if($user->status === 'active')
                        <span class="text-xs px-2 py-0.5 rounded-md" style="background:#14532d20; color:#4ade80; border:1px solid #14532d50;">active</span>
                    @else
                        <span class="text-xs px-2 py-0.5 rounded-md" style="background:#7f1d1d20; color:#f87171; border:1px solid #7f1d1d50;">suspended</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

</x-layouts.admin>
