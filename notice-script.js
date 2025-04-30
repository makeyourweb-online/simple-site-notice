document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('license_key_form');
    const spinner = form.querySelector('.spinner');

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const licenseKey = form.querySelector('input[name="ssn_license_key"]').value;

            // Pokaż spinner
            spinner.style.visibility = 'visible';

            // Usuń stare komunikaty
            const oldMessage = form.querySelector('.ssn-license-message');
            if (oldMessage) oldMessage.remove();

            fetch(ssn_ajax_object.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'ssn_validate_license',
                    license_key: licenseKey,
                    _ajax_nonce: ssn_ajax_object.nonce,
                }),
            })
            .then(response => response.json())
            .then(data => {
                console.log(data.data.data);
                const message = document.createElement('p');
                message.className = 'ssn-license-message';
                message.innerHTML = `<strong>${data.data.data}</strong>`;
                message.style.color = data.success ? 'green' : 'red';
                form.querySelector('table').appendChild(message);

                // setTimeout(location.reload(), 3000);
            })
            .finally(() => {
                // Ukryj spinner
                spinner.style.visibility = 'hidden';
            });
        });
    }
});