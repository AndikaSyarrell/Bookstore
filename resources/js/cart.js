// public/js/cart-store.js
// Global Cart Store for sharing cart data across components

(function() {
    'use strict';

    // Initialize global cart store
    window.cartStore = {
        items: [],
        count: 0,
        total: 0,

        // Set cart data and trigger update event
        setCart(items) {
            this.items = items;
            this.calculateTotals();
            this.dispatchUpdate();
            this.saveToLocalStorage();
        },

        // Add item to cart
        addItem(item) {
            const existingIndex = this.items.findIndex(i => i.id === item.id);
            
            if (existingIndex !== -1) {
                // Item exists, increase quantity
                this.items[existingIndex].quantity += item.quantity || 1;
            } else {
                // New item
                this.items.push({
                    id: item.id,
                    product_id: item.product_id,
                    name: item.name,
                    price: parseFloat(item.price),
                    quantity: item.quantity || 1,
                    image: item.image || null
                });
            }

            this.calculateTotals();
            this.dispatchUpdate();
            this.saveToLocalStorage();
        },

        // Update item quantity
        updateQuantity(itemId, quantity) {
            const index = this.items.findIndex(i => i.id === itemId);
            
            if (index !== -1) {
                if (quantity <= 0) {
                    this.removeItem(itemId);
                } else {
                    this.items[index].quantity = quantity;
                    this.calculateTotals();
                    this.dispatchUpdate();
                    this.saveToLocalStorage();
                }
            }
        },

        // Increase item quantity
        increaseQuantity(itemId) {
            const index = this.items.findIndex(i => i.id === itemId);
            if (index !== -1) {
                this.items[index].quantity += 1;
                this.calculateTotals();
                this.dispatchUpdate();
                this.saveToLocalStorage();
            }
        },

        // Decrease item quantity
        decreaseQuantity(itemId) {
            const index = this.items.findIndex(i => i.id === itemId);
            if (index !== -1) {
                if (this.items[index].quantity > 1) {
                    this.items[index].quantity -= 1;
                    this.calculateTotals();
                    this.dispatchUpdate();
                    this.saveToLocalStorage();
                } else {
                    this.removeItem(itemId);
                }
            }
        },

        // Remove item from cart
        removeItem(itemId) {
            this.items = this.items.filter(i => i.id !== itemId);
            this.calculateTotals();
            this.dispatchUpdate();
            this.saveToLocalStorage();
        },

        // Clear all cart items
        clearCart() {
            this.items = [];
            this.count = 0;
            this.total = 0;
            this.dispatchUpdate();
            this.saveToLocalStorage();
        },

        // Calculate cart totals
        calculateTotals() {
            this.count = this.items.reduce((sum, item) => sum + item.quantity, 0);
            this.total = this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },

        // Get item by ID
        getItem(itemId) {
            return this.items.find(i => i.id === itemId);
        },

        // Check if item exists in cart
        hasItem(productId) {
            return this.items.some(i => i.product_id === productId);
        },

        // Get item quantity
        getItemQuantity(productId) {
            const item = this.items.find(i => i.product_id === productId);
            return item ? item.quantity : 0;
        },

        // Dispatch custom event for cart updates
        dispatchUpdate() {
            const event = new CustomEvent('cart-updated', {
                detail: {
                    items: this.items,
                    count: this.count,
                    total: this.total
                }
            });
            window.dispatchEvent(event);
        },

        // Save cart to localStorage
        saveToLocalStorage() {
            try {
                localStorage.setItem('cart_data', JSON.stringify({
                    items: this.items,
                    count: this.count,
                    total: this.total,
                    timestamp: Date.now()
                }));
            } catch (error) {
                console.error('Error saving cart to localStorage:', error);
            }
        },

        // Load cart from localStorage
        loadFromLocalStorage() {
            try {
                const data = localStorage.getItem('cart_data');
                if (data) {
                    const parsed = JSON.parse(data);
                    
                    // Check if data is not too old (24 hours)
                    const hoursSinceUpdate = (Date.now() - parsed.timestamp) / (1000 * 60 * 60);
                    if (hoursSinceUpdate < 24) {
                        this.items = parsed.items || [];
                        this.count = parsed.count || 0;
                        this.total = parsed.total || 0;
                        return true;
                    }
                }
            } catch (error) {
                console.error('Error loading cart from localStorage:', error);
            }
            return false;
        },

        // Format currency
        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },

        // Sync cart with server
        async syncWithServer() {
            try {
                const response = await fetch('/cart/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        items: this.items
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.items) {
                        this.setCart(data.items);
                    }
                    return true;
                }
            } catch (error) {
                console.error('Error syncing cart with server:', error);
            }
            return false;
        }
    };

    // Initialize cart on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Try to load from localStorage first
        const loaded = window.cartStore.loadFromLocalStorage();
        
        // If loaded from localStorage, dispatch initial event
        if (loaded) {
            window.cartStore.dispatchUpdate();
        }
    });

})();