document.addEventListener("DOMContentLoaded", function () {
    updateCartCount();
    displayCartItems(); // عرض المنتجات المخزنة عند تحميل الصفحة

    document.querySelectorAll(".add-to-cart").forEach(button => {
        button.addEventListener("click", function (event) {
            event.preventDefault(); // منع إعادة تحميل الصفحة

            let productId = this.getAttribute("data-id");
            let productName = this.getAttribute("data-name");
            let productPrice = parseFloat(this.getAttribute("data-price"));
            let productImage = this.getAttribute("data-image");

            let cart = JSON.parse(localStorage.getItem("cart")) || [];

            // التحقق مما إذا كان المنتج موجودًا بالفعل في السلة
            let existingProduct = cart.find(item => item.id === productId);
            if (existingProduct) {
                existingProduct.quantity += 1; // زيادة الكمية إذا كان المنتج موجودًا
            } else {
                cart.push({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    image: productImage,
                    quantity: 1
                });
            }

            localStorage.setItem("cart", JSON.stringify(cart));

            updateCartCount();
            displayCartItems(); // تحديث عرض السلة
        });
    });
});

// تحديث عدد العناصر في السلة
function updateCartCount() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let totalQuantity = cart.reduce((sum, item) => sum + item.quantity, 0);
    document.getElementById("cart-count").textContent = totalQuantity;
}

// عرض المنتجات داخل صفحة السلة
function displayCartItems() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let cartContainer = document.getElementById("cart-items");

    if (!cartContainer) return; // إذا لم تكن الصفحة تحتوي على عنصر عرض السلة، لا تفعل شيئًا

    cartContainer.innerHTML = ""; // مسح المحتويات القديمة قبل إعادة العرض

    cart.forEach(item => {
        let itemHTML = `
            <div class="cart-item d-flex justify-content-between align-items-center border-bottom py-2">
                <img src="${item.image}" width="50" height="50" class="rounded">
                <span>${item.name}</span>
                <span>${item.quantity} x $${item.price.toFixed(2)}</span>
                <span>$${(item.quantity * item.price).toFixed(2)}</span>
                <button class="btn btn-sm btn-danger remove-from-cart" data-id="${item.id}">X</button>
            </div>
        `;
        cartContainer.innerHTML += itemHTML;
    });

    attachRemoveListeners();
}

// حذف منتج من السلة
function attachRemoveListeners() {
    document.querySelectorAll(".remove-from-cart").forEach(button => {
        button.addEventListener("click", function () {
            let productId = this.getAttribute("data-id");
            let cart = JSON.parse(localStorage.getItem("cart")) || [];

            cart = cart.filter(item => item.id !== productId); // إزالة المنتج

            localStorage.setItem("cart", JSON.stringify(cart));

            updateCartCount();
            displayCartItems();
        });
    });
}


document.addEventListener("DOMContentLoaded", function () {
    updateCartCount();
    displayCartItems(); // عرض المنتجات المخزنة عند تحميل الصفحة

    document.querySelectorAll(".add-to-cart").forEach(button => {
        button.addEventListener("click", function (event) {
            event.preventDefault(); // منع إعادة تحميل الصفحة

            let productId = this.getAttribute("data-id");
            let productName = this.getAttribute("data-name");
            let productPrice = parseFloat(this.getAttribute("data-price"));
            let productImage = this.getAttribute("data-image");

            let cart = JSON.parse(localStorage.getItem("cart")) || [];

            // التحقق مما إذا كان المنتج موجودًا بالفعل في السلة
            let existingProduct = cart.find(item => item.id === productId);

            if (existingProduct) {
                existingProduct.quantity += 1; // زيادة الكمية إذا كان المنتج موجودًا
            } else {
                cart.push({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    image: productImage,
                    quantity: 1
                });
            }

            // حفظ السلة في localStorage
            localStorage.setItem("cart", JSON.stringify(cart));

            updateCartCount();
            displayCartItems(); // تحديث عرض السلة
        });
    });
});

// تحديث عدد العناصر في السلة
function updateCartCount() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let totalQuantity = cart.reduce((sum, item) => sum + item.quantity, 0);
    document.getElementById("cart-count").textContent = totalQuantity;
}

// عرض المنتجات داخل صفحة السلة
function displayCartItems() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let cartContainer = document.getElementById("cart-items");

    if (!cartContainer) return; // إذا لم تكن الصفحة تحتوي على عنصر عرض السلة، لا تفعل شيئًا

    cartContainer.innerHTML = ""; // مسح المحتويات القديمة قبل إعادة العرض

    cart.forEach(item => {
        let itemHTML = `
            <div class="cart-item d-flex justify-content-between align-items-center border-bottom py-2">
                <img src="${item.image}" width="50" height="50" class="rounded">
                <span>${item.name}</span>
                <span>${item.quantity} x $${item.price.toFixed(2)}</span>
                <span>$${(item.quantity * item.price).toFixed(2)}</span>
                <button class="btn btn-sm btn-danger remove-from-cart" data-id="${item.id}">X</button>
            </div>
        `;
        cartContainer.innerHTML += itemHTML;
    });

    attachRemoveListeners();
}

// حذف منتج من السلة
function attachRemoveListeners() {
    document.querySelectorAll(".remove-from-cart").forEach(button => {
        button.addEventListener("click", function () {
            let productId = this.getAttribute("data-id");
            let cart = JSON.parse(localStorage.getItem("cart")) || [];

            cart = cart.filter(item => item.id !== productId); // إزالة المنتج

            localStorage.setItem("cart", JSON.stringify(cart));

            updateCartCount();
            displayCartItems();
        });
    });
}

// حساب المجموع الكلي من السلة في localStorage
function calculateTotal() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let totalAmount = 0;

    cart.forEach(item => {
        totalAmount += item.price * item.quantity;
    });

    document.getElementById("total-amount").textContent = "$" + totalAmount.toFixed(2);
}
