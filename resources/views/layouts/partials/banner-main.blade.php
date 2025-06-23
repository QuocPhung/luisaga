@if ($mainBanners->isNotEmpty())
<div 
    x-data="{ current: 0 }" 
    x-init="setInterval(() => current = (current + 1) % {{ $mainBanners->count() }}, 5000)" 
    class="relative w-full h-[380px] overflow-hidden rounded shadow"
>
    <div class="flex transition-transform duration-700 ease-in-out w-full h-full"
        :style="`transform: translateX(-${current * 100}%)`"
    >
        @foreach ($mainBanners as $banner)
            <div class="w-full h-full flex-shrink-0">
                <a href="{{ $banner->link ?? '#' }}" target="_blank" class="block w-full h-full">
                    <img src="{{ asset('storage/' . $banner->image) }}"
                         alt="{{ $banner->title }}"
                         class="w-full h-full object-cover">
                </a>
            </div>
        @endforeach
    </div>

    <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2">
        @foreach ($mainBanners as $i => $banner)
            <button 
                class="w-3 h-3 rounded-full transition-colors duration-300"
                :class="current === {{ $i }} ? 'bg-white' : 'bg-white/50'"
                @click="current = {{ $i }}"
            ></button>
        @endforeach
    </div>
</div>
@endif