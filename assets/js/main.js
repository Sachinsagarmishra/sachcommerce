/**
 * TrendsOne - Frontend JavaScript
 */

$(document).ready(function() {
    
    // Back to Top Button
    const backToTop = $('#backToTop');
    
    $(window).scroll(function() {
        if ($(this).scrollTop() > 300) {
            backToTop.fadeIn();
        } else {
            backToTop.fadeOut();
        }
    });
    
    backToTop.click(function() {
        $('html, body').animate({scrollTop: 0}, 600);
        return false;
    });
    
    // Add to Cart
    $('.add-to-cart-btn').click(function(e) {
        e.preventDefault();
        const productId = $(this).data('product-id');
        const quantity = $(this).data('quantity') || 1;
        
        $.ajax({
            url: siteUrl + '/api/add-to-cart.php',
            method: 'POST',
            data: {
                product_id: productId,
                quantity: quantity
            },
            success: function(response) {
                if (response.success) {
                    // Update cart count
                    updateCartCount();
                    // Show success message
                    showToast('Success', 'Product added to cart!', 'success');
                } else {
                    showToast('Error', response.message || 'Failed to add product', 'error');
                }
            },
            error: function() {
                showToast('Error', 'Something went wrong', 'error');
            }
        });
    });
    
    // Add to Wishlist (Product Cards)
    $(document).on('click', '.add-to-wishlist-btn', function(e) {
        e.preventDefault();
        const productId = $(this).data('product-id');
        const btn = $(this);
        
        $.ajax({
            url: siteUrl + '/api/add-to-wishlist.php',
            method: 'POST',
            data: {
                product_id: productId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    btn.addClass('active');
                    btn.find('i').removeClass('far').addClass('fas');
                    showToast('Success', 'Added to wishlist!', 'success');
                } else {
                    if (response.login_required) {
                        showToast('Login Required', 'Please login to add items to wishlist', 'error');
                        setTimeout(function() {
                            window.location.href = siteUrl + '/login.php';
                        }, 2000);
                    } else {
                        showToast('Error', response.message || 'Failed to add to wishlist', 'error');
                    }
                }
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    showToast('Login Required', 'Please login to add items to wishlist', 'error');
                    setTimeout(function() {
                        window.location.href = siteUrl + '/login.php';
                    }, 2000);
                } else {
                    showToast('Error', 'Something went wrong. Please try again.', 'error');
                }
            }
        });
    });
    
    // Add to Wishlist (Product Detail Page)
    $(document).on('click', '.add-to-wishlist-btn-detail', function(e) {
        e.preventDefault();
        const productId = $(this).data('product-id');
        const btn = $(this);
        const originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Adding...');
        
        $.ajax({
            url: siteUrl + '/api/add-to-wishlist.php',
            method: 'POST',
            data: {
                product_id: productId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    btn.removeClass('btn-danger').addClass('btn-success');
                    btn.html('<i class="fas fa-check me-2"></i>Added to Wishlist');
                    showToast('Success', 'Product added to your wishlist!', 'success');
                    
                    // Change to "View Wishlist" after 2 seconds
                    setTimeout(function() {
                        btn.html('<i class="fas fa-heart me-2"></i>View Wishlist');
                        btn.off('click').on('click', function() {
                            window.location.href = siteUrl + '/wishlist.php';
                        });
                    }, 2000);
                } else {
                    btn.prop('disabled', false).html(originalText);
                    if (response.login_required) {
                        showToast('Login Required', 'Please login to add items to wishlist', 'error');
                        setTimeout(function() {
                            window.location.href = siteUrl + '/login.php';
                        }, 2000);
                    } else {
                        showToast('Error', response.message || 'Failed to add to wishlist', 'error');
                    }
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalText);
                if (xhr.status === 401) {
                    showToast('Login Required', 'Please login to add items to wishlist', 'error');
                    setTimeout(function() {
                        window.location.href = siteUrl + '/login.php';
                    }, 2000);
                } else {
                    showToast('Error', 'Something went wrong. Please try again.', 'error');
                }
            }
        });
    });
    
    // Update Cart Quantity
    $('.cart-quantity-input').change(function() {
        const cartId = $(this).data('cart-id');
        const quantity = $(this).val();
        
        $.ajax({
            url: siteUrl + '/api/update-cart.php',
            method: 'POST',
            data: {
                cart_id: cartId,
                quantity: quantity
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    showToast('Error', response.message, 'error');
                }
            }
        });
    });
    
    // Remove from Cart
    $('.remove-from-cart-btn').click(function(e) {
        e.preventDefault();
        const cartId = $(this).data('cart-id');
        
        if (confirm('Remove this item from cart?')) {
            $.ajax({
                url: siteUrl + '/api/remove-from-cart.php',
                method: 'POST',
                data: {
                    cart_id: cartId
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        showToast('Error', response.message, 'error');
                    }
                }
            });
        }
    });
    
    // Newsletter Form
    $('.newsletter-form').submit(function(e) {
        e.preventDefault();
        const form = $(this);
        const email = form.find('input[name="email"]').val();
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: {
                email: email
            },
            success: function(response) {
                if (response.success) {
                    showToast('Success', 'Thank you for subscribing!', 'success');
                    form[0].reset();
                } else {
                    showToast('Error', response.message, 'error');
                }
            },
            error: function() {
                showToast('Error', 'Something went wrong', 'error');
            }
        });
    });
    
    // Product Image Gallery
    $('.product-thumbnail').click(function() {
        const newSrc = $(this).data('image');
        $('#mainProductImage').attr('src', newSrc);
        $('.product-thumbnail').removeClass('active');
        $(this).addClass('active');
    });
    
    // Quantity Increment/Decrement
    $('.qty-btn-minus').click(function() {
        const input = $(this).siblings('.qty-input');
        let value = parseInt(input.val());
        if (value > 1) {
            input.val(value - 1);
        }
    });
    
    $('.qty-btn-plus').click(function() {
        const input = $(this).siblings('.qty-input');
        let value = parseInt(input.val());
        const max = parseInt(input.attr('max'));
        if (!max || value < max) {
            input.val(value + 1);
        }
    });
    
    // Price Range Filter
    if ($('#priceRange').length) {
        const priceRange = $('#priceRange');
        const minPrice = $('#minPrice');
        const maxPrice = $('#maxPrice');
        
        priceRange.on('input', function() {
            const value = $(this).val();
            maxPrice.text('₹' + value);
        });
    }
    
});

// Update Cart Count
function updateCartCount() {
    $.ajax({
        url: siteUrl + '/api/get-cart-count.php',
        method: 'GET',
        success: function(response) {
            if (response.count) {
                $('.cart-count').text(response.count);
                $('.cart-badge').text(response.count).show();
            }
        }
    });
}

// Show Toast Notification
function showToast(title, message, type) {
    const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
    const toast = `
        <div class="toast align-items-center text-white ${bgClass} border-0" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${title}</strong><br>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    $('body').append(toast);
    const toastElement = $('.toast').last();
    const bsToast = new bootstrap.Toast(toastElement[0]);
    bsToast.show();
    
    setTimeout(function() {
        toastElement.remove();
    }, 5000);
}

// Set site URL for AJAX calls
const siteUrl = window.location.origin + '/trendsone';
