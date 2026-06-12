@props(['orientation' => 'horizontal'])

@if($orientation === 'vertical')
<div style="display:inline-block; width:1px; height:100%; background:#2a2a35; align-self:stretch;" data-orientation="vertical" {{ $attributes }}></div>
@else
<hr style="border:none; border-top:1px solid #2a2a35; margin:0;" data-orientation="horizontal" {{ $attributes }}>
@endif
