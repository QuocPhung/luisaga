@extends('pages.cart.cart')

@section('title', 'Giỏ hàng của bạn')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Giỏ hàng của bạn</h1>

    <div x-data="cartComponent()" x-init="initCart()" class="space-y-6">

        <template x-if="items.length > 0">
            <div>
                <template x-for="(item, index) in items" :key="item.id">
                    <div class="flex items-center gap-4 bg-white p-4 rounded shadow">
                        <img :src="'/storage/' + (item.thumbnail ?? 'default.jpg')" alt="" class="w-20 h-20 object-cover rounded">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800" x-text="item.name"></h3>
                            <p class="text-sm text-gray-500">
                                Đơn giá: 
                                <span class="text-blue-600 font-semibold" x-text="formatCurrency(item.final_price)"></span>
                            </p>
                            <div class="mt-2 flex items-center gap-2">
                                <label class="text-sm">Số lượng:</label>
                                <input type="number" min="1" class="w-16 px-2 py-1 border rounded"
                                       x-model.number="item.quantity"
                                       @change="updateQuantity(index)">
                            </div>
                        </div>
                        <button
                            class="text-red-500 hover:underline text-sm"
                            @click="confirmRemoveItem(index)"
                        >
                            Xóa
                        </button>
                    </div>
                </template>

                {{-- Tổng tiền --}}
                <div class="text-right mt-6 text-lg font-bold text-blue-700">
                    Tổng tiền: <span x-text="formatCurrency(total)"></span>
                </div>

                {{-- Nút thanh toán --}}
                <div class="text-right mt-4">
                <button 
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"
                    @click="checkout()"
                >
                    Tiến hành thanh toán
                </button>
                </div>
            </div>
        </template>

        <template x-if="items.length === 0">
            <p class="text-gray-500 italic">Giỏ hàng của bạn đang trống.</p>
        </template>

    </div>
@endsection
<script>
    function cartComponent() {
        return {
            items: [],
            total: 0,

            async initCart() {
                if (window.userLoggedIn) {
                    try {
                        const res = await fetch('/api/cart/items');
                        const data = await res.json();
                        this.items = data.items;
                    } catch (err) {
                        console.error('Lỗi lấy giỏ hàng từ server:', err);
                        this.items = [];
                    }
                } else {
                    this.items = JSON.parse(localStorage.getItem('cart') || '[]');
                }
                this.updateTotal(); // QUAN TRỌNG!
            },

            updateTotal() {
                this.total = this.items.reduce((sum, item) => {
                    return sum + (item.final_price * item.quantity);
                }, 0);
            },

            updateQuantity(index) {
                if (window.userLoggedIn) {
                    fetch('/api/cart/update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            product_id: this.items[index].id,
                            quantity: this.items[index].quantity
                        })
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Lỗi cập nhật');
                        this.updateTotal(); // ✅ gọi ở đây
                    })
                    .catch(err => {
                        console.error('Lỗi cập nhật số lượng:', err);
                    });
                } else {
                    // Khách
                    localStorage.setItem('cart', JSON.stringify(this.items));
                    this.updateTotal();
                }
            },

            removeItem(index) {
                const item = this.items[index];
                if (!window.userLoggedIn) {
                    this.items.splice(index, 1);
                    localStorage.setItem('cart', JSON.stringify(this.items));
                    this.updateTotal();
                    return;
                }

                fetch('/api/cart/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ product_id: item.id })
                })
                .then(res => {
                    if (!res.ok) throw new Error('Lỗi xóa sản phẩm');
                    return res.json(); // 👈 bắt buộc cần .json()
                })
                .then(data => {
                    this.items.splice(index, 1);
                    this.updateTotal();
                })
                .catch(err => {
                    console.error('Lỗi xoá:', err);
                });
            },

            formatCurrency(value) {
                return new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(value);
            },
            confirmRemoveItem(index) {
                if (confirm('Bạn có chắc muốn xoá sản phẩm này?')) {
                    this.removeItem(index);
                }
            },
            checkout() {
                if (!window.userLoggedIn) {
                    alert('Vui lòng đăng nhập để thanh toán.');
                    window.location.href = '/login';
                    return;
                }

                window.location.href = '/checkout';
            }

        };
    }
</script>

