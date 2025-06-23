<h2 class="text-lg font-bold mb-4 border-b pb-2">Danh mục sản phẩm</h2>

@isset($sidebarCategories)
    @php
        $limit = 5;
        $topCategories = $sidebarCategories->take($limit);
        $remainingCategories = $sidebarCategories->slice($limit);
    @endphp

    <div x-data="{ expanded: false }" class="flex-grow relative">
        <div class="transition-all duration-300 ease-in-out space-y-1">
            {{-- Hiển thị danh mục giới hạn --}}
            @include('layouts.components.category-menu', [
                'categories' => $topCategories
            ])

            {{-- Hiển thị phần còn lại khi mở rộng --}}
            <div x-show="expanded" x-collapse>
                @include('layouts.components.category-menu', [
                    'categories' => $remainingCategories
                ])
            </div>
        </div>

        {{-- Nút "Xem thêm" --}}
        @if ($sidebarCategories->count() > $limit)
            <div class="text-center mt-4">
                <button @click="expanded = !expanded"
                        class="text-sm text-blue-600 hover:underline focus:outline-none">
                    <span x-show="!expanded">Xem thêm...</span>
                    <span x-show="expanded">Thu gọn</span>
                </button>
            </div>
        @endif
    </div>
@else
    <p class="text-sm text-gray-500 italic">Không có danh mục nào.</p>
@endisset
