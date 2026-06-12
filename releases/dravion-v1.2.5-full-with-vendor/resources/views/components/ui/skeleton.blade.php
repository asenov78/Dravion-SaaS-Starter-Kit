@props([])

<div class="skeleton" style="background:linear-gradient(90deg,#1a1a1f 25%,#2a2a35 50%,#1a1a1f 75%); background-size:200% 100%; animation:skeleton-shimmer 1.5s infinite; border-radius:6px;" {{ $attributes }}></div>

<style>
@keyframes skeleton-shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
</style>
