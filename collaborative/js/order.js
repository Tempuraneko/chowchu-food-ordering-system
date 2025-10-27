document.addEventListener('DOMContentLoaded', function () {
    const orderBtns = document.querySelectorAll('.order-btn');
    const modal = document.getElementById('orderModal1');
    const closeModalBtn = document.getElementById('closeModal');
    const addToCartBtn = document.getElementById('addToCart');
    const finalPriceSpan = document.getElementById('finalPrice');
    const quantityDisplay = document.getElementById('quantityDisplay');
    
    // CART MODAL FUNCTIONALITY
    const cartButton = document.getElementById("cartButton");
    const cartModal = document.getElementById("cartModal");
    const closeCartButton = document.getElementById("closeCartModal");
    const cartItemsContainer = document.getElementById("cart-items");
    const subtotalElement = document.getElementById("subtotal");
    const totalElement = document.getElementById("total");
    let cart = []; 

    // Load cart from session storage if available
    const savedCart = sessionStorage.getItem("cart");
    if (savedCart) {
        cart = JSON.parse(savedCart);
    }

    // Show Cart Modal
    cartButton.addEventListener('click', function (event) {
        event.preventDefault();
        cartModal.classList.add('active');  
        updateCartDisplay();  
    });

    // Close Cart Modal when close button is clicked
    closeCartButton.addEventListener('click', function () {
        cartModal.classList.remove('active');  // Hide the modal
    });

    // Close the Cart Modal if the user clicks outside of it
    window.addEventListener('click', function (event) {
        if (event.target === cartModal) {
            cartModal.classList.remove('active');  
        }
    });

    //Proceed to checkOut
    document.getElementById('proceedToCheckout').addEventListener('click', function () {
        console.log("Cart being sent to PHP:", cart);  
    
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../member/checkOut.php', true); 
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log('Cart saved successfully:', xhr.responseText);  
                window.location.href = '../member/checkOut.php';  
            }
        };
        xhr.send(JSON.stringify(cart));  
    });

    // Handle order button click to open modal and populate product info
    orderBtns.forEach(btn => {
        btn.addEventListener('click', function (event) {
            event.preventDefault();
            const foodId = btn.getAttribute('data-food-id');
            const foodName = btn.getAttribute('data-food-name');
            const foodPrice = parseFloat(btn.getAttribute('data-food-price'));
            const foodImage = btn.getAttribute('data-food-image');
            const foodDetail = btn.getAttribute('data-food-detail');

            // Populate modal with product details
            document.getElementById('productId').innerText = foodId;
            document.getElementById('productName').innerText = foodName;
            document.getElementById('productDescription').innerText = foodDetail;
            document.getElementById('productPrice').innerText = `RM ${foodPrice.toFixed(2)}`;
            document.getElementById('productImage').src = foodImage;

            // Reset values
            quantityDisplay.innerText = 1;
            document.getElementById('specialInstructions').value = "";

            modal.style.display = 'block';
            updatePrice(foodPrice);
        });
    });

    // Function to update the price based on quantity
    function updatePrice(basePrice) {
        const quantity = parseInt(quantityDisplay.innerText);
        finalPriceSpan.textContent = `RM ${(basePrice * quantity).toFixed(2)}`;
    }

    //Decrease quantity
    document.getElementById('decreaseQuantity').onclick = function () {
        let currentQuantity = parseInt(quantityDisplay.innerText);
        if (currentQuantity > 1) {
            quantityDisplay.innerText = --currentQuantity;
            updatePrice(parseFloat(document.getElementById('productPrice').innerText.replace('RM ', '')));
        }
    };

    //Increase quantity
    document.getElementById('increaseQuantity').onclick = function () {
        let currentQuantity = parseInt(quantityDisplay.innerText);
        if (currentQuantity < 100) {  // Limit quantity to 100
            quantityDisplay.innerText = ++currentQuantity;
            updatePrice(parseFloat(document.getElementById('productPrice').innerText.replace('RM ', '')));
        } else {
            alert("You cannot add more than 100 of this item.");
        }
    };

    // Close the modal
    closeModalBtn.addEventListener('click', function () {
        modal.style.display = 'none';
    });

    // Add to Cart functionality
    addToCartBtn.addEventListener('click', function () {
        const foodId = document.getElementById('productId').innerText;
        const foodName = document.getElementById('productName').innerText;
        const foodPrice = parseFloat(document.getElementById('productPrice').innerText.replace('RM ', ''));
        const foodImage = document.getElementById('productImage').src;
        const foodDetail = document.getElementById('productDescription').innerText;
        const quantity = parseInt(quantityDisplay.innerText);
        const note = document.getElementById('specialInstructions').value;

        addToCart(foodId, foodName, foodPrice, foodImage, foodDetail, quantity, note);

        modal.style.display = 'none';
        alert(`Added ${quantity} of ${foodId} to your cart.`);
    });
    
    // Function to update cart display
    function updateCartDisplay() {
        
        cartItemsContainer.innerHTML = "";
        let subtotal = 0;

        cart.forEach((item, index) => {
            const itemTotal = item.foodPrice * item.quantity;
            subtotal += itemTotal;

            const cartItemHTML = `
                <div class="cart-item" data-index="${index}">
                    <img src="${item.foodImage}" alt="${item.foodName}" class="cart-item-image">
                    <div class="cart-item-details">
                         <p style="display: none;">${item.foodId}</p>
                        <h5>${item.foodName}</h5>
                        <p>${item.foodDetail}</p>
                        <p>RM ${item.foodPrice.toFixed(2)}</p>
                        <p><strong>Note:</strong> ${item.note ? item.note : ""}</p> <!--Show special instructions -->
                    </div>
                    <div class="cart-item-quantity">
                        <button class="btn-decrease" data-index="${index}">-</button>
                        <span class="cart-quantity">${item.quantity}</span>
                        <button class="btn-increase" data-index="${index}">+</button>
                    </div>
                </div>
            `;
            cartItemsContainer.innerHTML += cartItemHTML;
        });

        subtotalElement.textContent = `RM ${subtotal.toFixed(2)}`;
        totalElement.textContent = `RM ${subtotal.toFixed(2)}`;

        document.querySelector(".item_count").textContent = cart.length; 
        saveCartToSession(); 
        attachCartEventListeners();
    }

    // Function to attach event listeners for cart quantity buttons
    function attachCartEventListeners() {
        document.querySelectorAll('.btn-increase').forEach((btn, index) => {
            btn.onclick = function () {
                 if (cart[index].quantity < 100) {
                    cart[index].quantity++; 
                    updateCartDisplay(); 
                } else {
                    alert("You cannot add more than 100 of this item.");
                }
            };
        });

        document.querySelectorAll('.btn-decrease').forEach((btn, index) => {
            btn.onclick = function () {
                if (cart[index].quantity > 1) {
                    cart[index].quantity--;
                } else {
                    cart.splice(index, 1);
                }
                updateCartDisplay();
            };
        });
    }

    // Function to add items to cart
    function addToCart(foodId, foodName, foodPrice, foodImage, foodDetail, quantity, note) {
        const existingItem = cart.find(item => item.foodId === foodId);
        if (existingItem) {
            existingItem.quantity += quantity;
            existingItem.note = note; 
        } else {
            cart.push({ foodId, foodName, foodPrice, foodImage, foodDetail, quantity, note });
        }
        saveCartToSession(); 
        updateCartDisplay();
    }

    function saveCartToSession() {
        sessionStorage.setItem("cart", JSON.stringify(cart));
    }    
    
    updateCartDisplay();
});
