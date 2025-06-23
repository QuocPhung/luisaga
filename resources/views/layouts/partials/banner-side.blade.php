@if ($banners->count() > 3)
    <div class="flex-1 flex flex-col justify-between h-full">
        @foreach ($banners->skip(3) as $banner)
            <a href="{{ $banner->link ?? '#' }}" class="block">
                <div class="aspect-[16/9] w-full rounded overflow-hidden shadow">
                    <img src="{{ asset('storage/' . $banner->image) }}"
                         alt="{{ $banner->title }}"
                         class="w-full h-full object-cover mb-2">
                </div>
            </a>
        @endforeach
    </div>
@endif
