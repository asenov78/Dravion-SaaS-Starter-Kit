<x-layouts.admin title="Users" breadcrumb="Manage all system users">

<div class="flex items-center justify-between mb-5">
    <p class="text-xs" style="color:#6b6b7b;">{{ $users->total() }} users total</p>
    <a href="{{ route('admin.users.create') }}"
        class="text-xs px-3 py-1.5 rounded-md font-medium transition-colors"
        style="background:#5e6ad2; color:#fff;"
        onmouseover="this.style.background='#7b84e0'"
        onmouseout="this.style.background='#5e6ad2'">+ New User</a>
</div>

<div class="rounded-lg border overflow-hidden" style="background:#1a1a1f; border-color:#2a2a35;">
    <table class="w-full text-sm">
        <thead>
            <tr style="border-bottom:1px solid #2a2a35;">
                <th class="text-left px-5 py-3 text-xs font-medium" style="color:#6b6b7b;">User</th>
                <th class="text-left px-5 py-3 text-xs font-medium" style="color:#6b6b7b;">Role</th>
                <th class="text-left px-5 py-3 text-xs font-medium" style="color:#6b6b7b;">Status</th>
                <th class="text-left px-5 py-3 text-xs font-medium" style="color:#6b6b7b;">Joined</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr style="border-bottom:1px solid #1e1e27;" onmouseover="this.style.background='#111113'" onmouseout="this.style.background=''">
                <td class="px-5 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold text-white flex-shrink-0" style="background:#5e6ad2;">{{ substr($user->name,0,1) }}</div>
                        <div>
                            <p class="font-medium text-sm" style="color:#e2e2e9;">{{ $user->name }}</p>
                            <p class="text-xs" style="color:#6b6b7b;">{{ $user->email }}</p>
                        </div>
                    </div>
                </td>
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
                <td class="px-5 py-3 text-xs" style="color:#6b6b7b;">{{ $user->created_at->format('M d, Y') }}</td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('admin.users.edit', $user) }}"
                            class="text-xs px-2.5 py-1 rounded-md transition-colors"
                            style="color:#6b6b7b; border:1px solid #2a2a35;"
                            onmouseover="this.style.background='#1a1a1f';this.style.color='#e2e2e9'"
                            onmouseout="this.style.background='';this.style.color='#6b6b7b'">Edit</a>

                        @if($user->id !== auth()->id())
                            @if($user->status === 'active')
                            <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs px-2.5 py-1 rounded-md transition-colors"
                                    style="color:#f87171; border:1px solid #7f1d1d50;"
                                    onmouseover="this.style.background='#7f1d1d20'"
                                    onmouseout="this.style.background=''">Suspend</button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs px-2.5 py-1 rounded-md transition-colors"
                                    style="color:#4ade80; border:1px solid #14532d50;"
                                    onmouseover="this.style.background='#14532d20'"
                                    onmouseout="this.style.background=''">Activate</button>
                            </form>
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-5 py-8 text-center text-xs" style="color:#6b6b7b;">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($users->hasPages())
    <div class="px-5 py-3 border-t" style="border-color:#2a2a35;">{{ $users->links() }}</div>
    @endif
</div>

</x-layouts.admin>
