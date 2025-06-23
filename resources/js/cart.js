export default {
    getCart() {
        return JSON.parse(localStorage.getItem('cart')) || [];
    },

    saveCart(cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
        document.dispatchEvent(new CustomEvent('cartUpdated')); // Trigger global update
    },

    addToCart(product) {
        if (window.userLoggedIn) {
            // Gửi AJAX lên server để lưu vào DB
            fetch('/api/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    product_id: product.id,
                    quantity: 1
                })
            })
            .then(res => res.json())
            .then(data => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Đã thêm vào giỏ hàng!',
                        text: product.name,
                        toast: true,
                        position: 'top-end',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
    
                document.dispatchEvent(new CustomEvent('cartUpdated'));
            });
        } else {
            // Dùng localStorage như trước
            const cart = this.getCart();
            const existing = cart.find(p => p.id === product.id);
    
            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push({ ...product, quantity: 1 });
            }
    
            this.saveCart(cart);
            this.updateCartCountBadge();
    
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Đã thêm vào giỏ hàng!',
                    text: product.name,
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        }
    },

    updateQuantity(id, qty) {
        if (window.userLoggedIn) {
            fetch('/api/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ product_id: id, quantity: qty })
            }).then(() => {
                document.dispatchEvent(new CustomEvent('cartUpdated'));
            });
        } else {
            const cart = this.getCart().map(p => {
                if (p.id === id) p.quantity = qty;
                return p;
            });
            this.saveCart(cart);
            this.updateCartCountBadge();
        }
    },

    removeItem(id) {
        if (window.userLoggedIn) {
            fetch('/api/cart/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ product_id: id })
            }).then(() => {
                document.dispatchEvent(new CustomEvent('cartUpdated'));
            });
        } else {
            const cart = this.getCart().filter(p => p.id !== id);
            this.saveCart(cart);
            this.updateCartCountBadge();
        }
    },

    clearCart() {
        if (window.userLoggedIn) {
            fetch('/api/cart/clear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => {
                document.dispatchEvent(new CustomEvent('cartUpdated'));
            });
        } else {
            localStorage.removeItem('cart');
            document.dispatchEvent(new CustomEvent('cartUpdated'));
        }
    },
    
    updateCartCountBadge() {
        if (window.userLoggedIn) {
            fetch('/api/cart/count')
                .then(res => res.json())
                .then(data => {
                    const badge = document.getElementById('cart-count');
                    if (badge) badge.textContent = data.count;
                });
        } else {
            const count = this.getCart().reduce((sum, item) => sum + item.quantity, 0);
            const badge = document.getElementById('cart-count');
            if (badge) badge.textContent = count;
        }
    },    
    
};

