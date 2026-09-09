@props(['href', 'icon' => 'home'])
<a href="{{ $href }}" wire:navigate
   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
   {{ request()->url() === $href ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
    <span>●</span> {{ $slot }}
</a>