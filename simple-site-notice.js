document.addEventListener('DOMContentLoaded', function() {
    var notice = document.getElementById('simsino-myw-notice');
    var closeBtn = document.querySelector('.simsino-myw-notice-close');

    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            notice.style.display = 'none';
            document.cookie = "simple_site_notice_closed=1; path=/; max-age=2592000"; // 30 dni
        });
    }
});