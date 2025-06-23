import './bootstrap';
import Swal from 'sweetalert2';

window.Swal = Swal;
import Alpine from 'alpinejs'

window.Alpine = Alpine

Alpine.start()
import 'swiper/swiper-bundle.css';
import Swiper from 'swiper/bundle';

window.initBannerSlider = function () {
    new Swiper('.banner-swiper', {
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });
};
import cart from './cart';
window.cart = cart;

document.addEventListener('DOMContentLoaded', () => {
    if (window.cart) {
        window.cart.updateCartCountBadge();
    }
});
document.addEventListener('DOMContentLoaded', async () => {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Chỉ sync nếu có cart và user đã login
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const isLoggedIn = document.body.dataset.loggedIn === 'true'; // bạn phải set biến này từ backend

    if (cart.length > 0 && isLoggedIn) {
        try {
            await fetch('/cart/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    items: cart.map(item => ({
                        product_id: item.id,
                        quantity: item.quantity
                    }))
                })
            });

            // Sau khi sync thành công, có thể xóa local cart
            localStorage.removeItem('cart');

            // Reload để dùng dữ liệu từ DB
            location.reload();

        } catch (err) {
            console.error('Lỗi đồng bộ giỏ hàng:', err);
        }
    }
});
