document.addEventListener('DOMContentLoaded', function() {
    const uidInput = document.getElementById('scan_user_id');
    if (uidInput && typeof getUserId === 'function') {
        uidInput.value = getUserId();
    }
});
