@props(['headers' => [], 'rows' => []])

<div style="overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse; font-family:Inter,system-ui; font-size:13px;">
        @if(count($headers))
        <thead>
            <tr style="border-bottom:1px solid #2a2a35;">
                @foreach($headers as $header)
                <th style="text-align:left; padding:10px 12px; color:#6b6b7b; font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em; white-space:nowrap;">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        @endif
        <tbody>
            @foreach($rows as $row)
            <tr style="border-bottom:1px solid #2a2a35;" onmouseover="this.style.background='#1a1a1f'" onmouseout="this.style.background='transparent'">
                @foreach($row as $cell)
                <td style="padding:10px 12px; color:#c2c2ce;">{{ $cell }}</td>
                @endforeach
            </tr>
            @endforeach
            @if($slot->isNotEmpty())
            {{ $slot }}
            @endif
        </tbody>
    </table>
</div>
