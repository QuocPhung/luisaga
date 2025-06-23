@isset($categories)
    <ul class="space-y-1">
        @foreach ($categories as $cat)
            <li x-data="{ open: false }" class="relative group">
                <div class="flex items-center justify-between hover:bg-blue-100 px-3 py-2 rounded"
                     @mouseenter="open = true" @mouseleave="open = false">
                        <a href="{{ route('product.index', ['category_id' => $cat->id]) }}"
                        class="font-medium text-gray-800 flex-grow cursor-pointer">
                            {{ $cat->name }}
                        </a>


                    @if ($cat->childrenRecursive->isNotEmpty())
                        <svg :class="{ 'rotate-90': open }"
                             class="w-4 h-4 text-gray-400 transform transition-transform duration-200"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5l7 7-7 7" />
                        </svg>
                    @endif
                </div>

                {{-- Danh mục con xổ sang ngang --}}
                @if ($cat->childrenRecursive->isNotEmpty())
                    <div x-show="open" x-transition
                         class="absolute top-0 left-full ml-1 bg-white border shadow rounded w-48 z-50"
                         @mouseenter="open = true" @mouseleave="open = false">
                        @include('layouts.components.category-menu', [
                            'categories' => $cat->childrenRecursive->filter(fn($child) => $child->id !== $cat->id)
                        ])
                    </div>
                @endif
            </li>
        @endforeach
    </ul>
@endisset
