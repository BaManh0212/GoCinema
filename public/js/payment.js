let isProcessingPayment = false;

document.getElementById('payButton').addEventListener('click', function() {
    const selectedPayment = document.querySelector('input[name="payment_method"]:checked').value;

    // Đánh dấu đang xử lý thanh toán để tránh cảnh báo rời trang
    isProcessingPayment = true;

    // Hiển thị loading
    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang xử lý...';
    this.disabled = true;

    // Gửi request thanh toán
    fetch('/booking/process-payment/' + this.dataset.bookingId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            payment_method: selectedPayment
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.redirect_url) {
                // Redirect đến trang thanh toán bên thứ 3
                window.location.href = data.redirect_url;
            } else {
                // Redirect đến trang xác nhận
                window.location.href = data.redirect;
            }
        } else {
            alert(data.message || 'Có lỗi xảy ra khi thanh toán');
            // Reset button và trạng thái xử lý thanh toán
            isProcessingPayment = false;
            document.getElementById('payButton').innerHTML = 'Thanh toán ' + Number(this.dataset.total).toLocaleString() + 'đ';
            document.getElementById('payButton').disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi thanh toán');
        // Reset button và trạng thái xử lý thanh toán
        isProcessingPayment = false;
        document.getElementById('payButton').innerHTML = 'Thanh toán ' + Number(this.dataset.total).toLocaleString() + 'đ';
        document.getElementById('payButton').disabled = false;
    });
});

// Modal handling and cancel order if user navigates away

const modal = document.getElementById('paymentFailedModal');
const modalConfirmBtn = document.getElementById('modalConfirmBtn');
const modalCancelBtn = document.getElementById('modalCancelBtn');

let navigationCanceled = false; // flag to prevent multiple cancels
let pendingNavigationUrl = null;

function showModalAndCancelOrder(urlToNavigate) {
    if (navigationCanceled) return;
    navigationCanceled = true;
    pendingNavigationUrl = urlToNavigate;
    modal.classList.remove('hidden');
}

modalConfirmBtn.addEventListener('click', function() {
    // Call cancel API
    fetch('/booking/ajax-cancel/' + modalConfirmBtn.dataset.bookingId, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            modal.classList.add('hidden');
            if (pendingNavigationUrl) {
                window.location.href = pendingNavigationUrl;
            }
        } else {
            alert(data.message || 'Có lỗi xảy ra khi hủy đơn.');
            modal.classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Cancel order error:', error);
        alert('Có lỗi xảy ra khi hủy đơn.');
        modal.classList.add('hidden');
    });
});

modalCancelBtn.addEventListener('click', function() {
    modal.classList.add('hidden');
    navigationCanceled = false;
    pendingNavigationUrl = null;
});

// Intercept all link clicks
document.querySelectorAll('a[href]').forEach(function(link) {
    link.addEventListener('click', function(event) {
        const href = link.getAttribute('href');

        // If link is external or no href, ignore
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
            return;
        }

        // Prevent navigation and show modal
        event.preventDefault();
        showModalAndCancelOrder(href);
    });
});

// Listen to beforeunload event for browser back or tab close
window.addEventListener('beforeunload', function(event) {
    // Nếu đang xử lý thanh toán thì không hủy đơn và không hiện cảnh báo
    if (isProcessingPayment) {
        return undefined;
    }

    // Send async cancel request (may not complete before unload)
    navigator.sendBeacon('/booking/ajax-cancel/' + modalConfirmBtn.dataset.bookingId, new Blob([JSON.stringify({})], {type: 'application/json'}));

    // Show default browser message (some browsers ignore custom text)
    const message = 'Bạn có chắc chắn muốn rời khỏi trang thanh toán? Thanh toán sẽ không thành công.';
    (event || window.event).returnValue = message;
    return message;
});
