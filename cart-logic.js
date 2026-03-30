// Функция для отрисовки корзины
function renderCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const container = document.getElementById('cart-content');
    
    if (cart.length === 0) {
        container.innerHTML = `
            <div class="empty-msg">
                <h2>В корзине пока пусто :(</h2>
                <p>Самое время добавить туда что-нибудь вкусное!</p>
                <br>
                <a href="index.php" class="btn btn-primary">Перейти в меню</a>
            </div>`;
        return;
    }

    let totalSum = 0;
    let itemsHtml = '';

    cart.forEach((item, index) => {
        const itemTotal = item.price * item.qty;
        totalSum += itemTotal;
        itemsHtml += `
            <div class="cart-item">
                <div class="item-info">
                    <div class="item-img" style="background-image: url('${item.img || 'https://via.placeholder.com'}')"></div>
                    <div>
                        <h3 style="margin:0">${item.name}</h3>
                        <p style="color:#888; margin:5px 0 0 0">${item.price} ₽</p>
                    </div>
                </div>
                <div class="qty-btns">
                    <button class="q-btn" onclick="updateQty(${index}, -1)">-</button>
                    <span style="font-weight:600">${item.qty}</span>
                    <button class="q-btn" onclick="updateQty(${index}, 1)">+</button>
                </div>
                <div style="font-weight: bold; min-width: 80px; text-align: right;">${itemTotal} ₽</div>
            </div>`;
    });

    container.innerHTML = `
        <div class="cart-grid">
            <div>${itemsHtml}</div>
            <aside class="checkout-side">
                <h3>Итого</h3>
                <div class="summary-row">
                    <span>Блюда (${cart.reduce((acc, i) => acc + i.qty, 0)})</span>
                    <span>${totalSum} ₽</span>
                </div>
                <div class="summary-row">
                    <span>Доставка</span>
                    <span style="color: #27AE60; font-weight:600">Бесплатно</span>
                </div>
                <div class="total-price summary-row">
                    <span>К оплате</span>
                    <span>${totalSum} ₽</span>
                </div>
                <button class="btn btn-primary btn-full" style="margin-top: 20px; height: 50px;" onclick="checkout()">
                    Оформить заказ
                </button>
            </aside>
        </div>`;
}

// Функция изменения количества
window.updateQty = function(index, delta) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart[index].qty += delta;

    if (cart[index].qty <= 0) {
        cart.splice(index, 1); // Удаляем товар, если его 0
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    renderCart();
};

function checkout() {
    alert("Заказ принят! Мы уже начинаем готовить.");
    localStorage.removeItem('cart'); // Очищаем после покупки
    location.href = 'index.php';
}

// Инициализация при загрузке
document.addEventListener('DOMContentLoaded', renderCart);
