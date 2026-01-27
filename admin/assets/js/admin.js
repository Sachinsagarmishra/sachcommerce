/**
 * Admin Panel JavaScript
 */

$(document).ready(function() {
    
    // Create sidebar overlay if it doesn't exist
    if (!$('.sidebar-overlay').length) {
        $('body').append('<div class="sidebar-overlay"></div>');
    }
    
    // Sidebar Toggle
    $('#sidebarToggle, #sidebarClose').on('click', function(e) {
        e.preventDefault();
        $('.sidebar').toggleClass('active');
        $('.sidebar-overlay').toggleClass('active');
        $('body').toggleClass('sidebar-open');
    });
    
    // Close sidebar when clicking overlay
    $(document).on('click', '.sidebar-overlay', function() {
        $('.sidebar').removeClass('active');
        $('.sidebar-overlay').removeClass('active');
        $('body').removeClass('sidebar-open');
    });
    
    // Close sidebar when clicking a link on mobile (but not dropdown toggles)
    $('.sidebar-nav .nav-link').on('click', function(e) {
        // Don't close if it's a dropdown toggle
        if ($(this).attr('data-bs-toggle') === 'collapse') {
            return; // Let the dropdown work
        }
        
        // Close sidebar only for actual page links on mobile
        if ($(window).width() < 992 && $(this).attr('href') && !$(this).attr('href').startsWith('#')) {
            setTimeout(function() {
                $('.sidebar').removeClass('active');
                $('.sidebar-overlay').removeClass('active');
                $('body').removeClass('sidebar-open');
            }, 300); // Delay to allow click to register
        }
    });
    
    // Initialize DataTables
    if ($('.data-table').length) {
        $('.data-table').DataTable({
            responsive: true,
            pageLength: 20,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search..."
            }
        });
    }
    
    // Initialize Select2
    if ($('.select2').length) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }
    
    // Image Preview
    $('input[type="file"][data-preview]').on('change', function() {
        const file = this.files[0];
        const previewId = $(this).data('preview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $(`#${previewId}`).attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Confirm Delete
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const itemName = $(this).data('name') || 'this item';
        
        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to delete ${itemName}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
    
    // Bulk Actions
    $('#selectAll').on('change', function() {
        $('.item-checkbox').prop('checked', $(this).prop('checked'));
    });
    
    $('.item-checkbox').on('change', function() {
        if ($('.item-checkbox:checked').length === $('.item-checkbox').length) {
            $('#selectAll').prop('checked', true);
        } else {
            $('#selectAll').prop('checked', false);
        }
    });
    
    $('#bulkActionBtn').on('click', function() {
        const action = $('#bulkAction').val();
        const selectedIds = [];
        
        $('.item-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });
        
        if (selectedIds.length === 0) {
            Swal.fire('Error', 'Please select at least one item', 'error');
            return;
        }
        
        if (!action) {
            Swal.fire('Error', 'Please select an action', 'error');
            return;
        }
        
        Swal.fire({
            title: 'Confirm Action',
            text: `Apply ${action} to ${selectedIds.length} item(s)?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, proceed'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form or AJAX request
                $('#bulkActionForm').submit();
            }
        });
    });
    
    // Auto-generate slug from title
    $('#productName, #postTitle, #categoryName').on('keyup', function() {
        const title = $(this).val();
        const slug = title.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        $('#slug').val(slug);
    });
    
    // Calculate discount percentage
    $('#price, #salePrice').on('keyup', function() {
        const price = parseFloat($('#price').val()) || 0;
        const salePrice = parseFloat($('#salePrice').val()) || 0;
        
        if (price > 0 && salePrice > 0 && salePrice < price) {
            const discount = Math.round(((price - salePrice) / price) * 100);
            $('#discountPercentage').val(discount);
        } else {
            $('#discountPercentage').val(0);
        }
    });
    
    // Add variant row
    $('#addVariant').on('click', function() {
        const variantHtml = `
            <div class="row mb-2 variant-row">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="variant_type[]" placeholder="Type (e.g., Size)">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="variant_value[]" placeholder="Value (e.g., M)">
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="variant_price[]" placeholder="Price" step="0.01">
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="variant_stock[]" placeholder="Stock">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-variant">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        $('#variantsContainer').append(variantHtml);
    });
    
    // Remove variant row
    $(document).on('click', '.remove-variant', function() {
        $(this).closest('.variant-row').remove();
    });
    
    // Order status update
    $('.update-order-status').on('click', function() {
        const orderId = $(this).data('order-id');
        const currentStatus = $(this).data('current-status');
        
        Swal.fire({
            title: 'Update Order Status',
            html: `
                <select id="newStatus" class="form-select mb-3">
                    <option value="pending" ${currentStatus === 'pending' ? 'selected' : ''}>Pending</option>
                    <option value="processing" ${currentStatus === 'processing' ? 'selected' : ''}>Processing</option>
                    <option value="shipped" ${currentStatus === 'shipped' ? 'selected' : ''}>Shipped</option>
                    <option value="delivered" ${currentStatus === 'delivered' ? 'selected' : ''}>Delivered</option>
                    <option value="cancelled" ${currentStatus === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                    <option value="refunded" ${currentStatus === 'refunded' ? 'selected' : ''}>Refunded</option>
                </select>
                <textarea id="adminNotes" class="form-control" placeholder="Admin notes (optional)" rows="3"></textarea>
            `,
            showCancelButton: true,
            confirmButtonText: 'Update',
            preConfirm: () => {
                return {
                    status: document.getElementById('newStatus').value,
                    notes: document.getElementById('adminNotes').value
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit AJAX request
                $.ajax({
                    url: 'update-order-status.php',
                    method: 'POST',
                    data: {
                        order_id: orderId,
                        status: result.value.status,
                        notes: result.value.notes
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success', 'Order status updated', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to update order status', 'error');
                    }
                });
            }
        });
    });
    
    // Copy to clipboard
    $('.copy-to-clipboard').on('click', function() {
        const text = $(this).data('text');
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Text copied to clipboard',
                timer: 1500,
                showConfirmButton: false
            });
        });
    });
    
    // Print invoice
    $('.print-invoice').on('click', function() {
        window.print();
    });
    
    // Export table to CSV
    $('.export-csv').on('click', function() {
        const table = $(this).data('table');
        window.location.href = `export-reports.php?type=csv&table=${table}`;
    });
    
    // Date range picker (if using)
    if ($('.daterange').length) {
        $('.daterange').daterangepicker({
            opens: 'left',
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
    }
    
    // Tooltip initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Form validation
    $('.needs-validation').on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        $(this).addClass('was-validated');
    });
    
});

// Number formatting
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Format currency
function formatCurrency(amount) {
    return '₹' + formatNumber(parseFloat(amount).toFixed(2));
}
