<x-layouts.admin title="Activity Log">

<x-ui.card>
    @if($activities->isEmpty())
    <div style="padding:40px; text-align:center; color:#6b6b7b; font-size:13px;">No activity recorded yet.</div>
    @else
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-family:Inter,system-ui; font-size:13px;">
            <thead>
                <tr style="border-bottom:1px solid #2a2a35;">
                    <th style="text-align:left; padding:10px 12px; color:#6b6b7b; font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">Event</th>
                    <th style="text-align:left; padding:10px 12px; color:#6b6b7b; font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">Description</th>
                    <th style="text-align:left; padding:10px 12px; color:#6b6b7b; font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">User</th>
                    <th style="text-align:left; padding:10px 12px; color:#6b6b7b; font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">Subject</th>
                    <th style="text-align:left; padding:10px 12px; color:#6b6b7b; font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">When</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities as $activity)
                @php
                    $eventColor = match($activity->event) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default   => 'default',
                    };
                @endphp
                <tr style="border-bottom:1px solid #1e1e27;" onmouseover="this.style.background='#111113'" onmouseout="this.style.background=''">
                    <td style="padding:10px 12px;">
                        <x-ui.badge :variant="$eventColor">{{ $activity->event ?? $activity->log_name }}</x-ui.badge>
                    </td>
                    <td style="padding:10px 12px; color:#c2c2ce; max-width:320px;">
                        <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block; max-width:300px;" title="{{ $activity->description }}">
                            {{ $activity->description }}
                        </span>
                    </td>
                    <td style="padding:10px 12px;">
                        @if($activity->causer)
                        <div style="display:flex; align-items:center; gap:8px;">
                            <x-ui.avatar :name="$activity->causer->name" :size="24" />
                            <span style="color:#9b9bab; font-size:12px;">{{ $activity->causer->name }}</span>
                        </div>
                        @else
                        <span style="color:#3a3a45; font-size:12px;">System</span>
                        @endif
                    </td>
                    <td style="padding:10px 12px; color:#6b6b7b; font-size:12px;">
                        {{ $activity->subject_type ? class_basename($activity->subject_type) . ' #' . $activity->subject_id : '—' }}
                    </td>
                    <td style="padding:10px 12px;">
                        <x-ui.tooltip :text="$activity->created_at->format('Y-m-d H:i:s')">
                            <span style="color:#6b6b7b; font-size:12px;">{{ $activity->created_at->diffForHumans() }}</span>
                        </x-ui.tooltip>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($activities->hasPages())
    <div style="padding:16px 12px; border-top:1px solid #2a2a35;">
        <x-ui.pagination
            :current="$activities->currentPage()"
            :total="$activities->lastPage()"
            url="{{ route('admin.activity') }}"
        />
    </div>
    @endif
    @endif
</x-ui.card>

</x-layouts.admin>
