document.addEventListener('DOMContentLoaded', function() {
    const uidInput = document.getElementById('scan_user_id');
    if (uidInput && typeof getUserId === 'function') {
        uidInput.value = getUserId();
    }

    const form = document.getElementById('scanForm'); // pastikan id form benar
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const fileInput = document.getElementById('file'); // input gambar
            const file = fileInput.files[0];

            if (!file) {
                alert('Pilih gambar dulu!');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);
            formData.append('user_id', getUserId());

            try {
                const response = await fetch("https://raditnugroho.app.n8n.cloud/webhook/finance-receipt", {
                    method: "POST",
                    body: formData
                });

                const result = await response.json();
                console.log(result);

                alert("Berhasil kirim ke n8n!");
            } catch (err) {
                console.error(err);
                alert("Gagal kirim ke n8n");
            }
        });
    }
});