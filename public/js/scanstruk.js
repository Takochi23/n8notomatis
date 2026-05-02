document.addEventListener('DOMContentLoaded', function () {
    const uidInput = document.getElementById('scan_user_id');
    if (uidInput && typeof getUserId === 'function') {
        uidInput.value = getUserId();
    }

    const form = document.getElementById('scanForm');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const fileInput = document.getElementById('file');
        const file = fileInput?.files[0];

        if (!file) {
            alert('Pilih gambar dulu!');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);
        formData.append('user_id', getUserId() || 'guest');

        try {
            const response = await fetch("https://raditnugroho.app.n8n.cloud/webhook/finance-receipt", {
                method: "POST",
                body: formData
            });

            // DEBUG WAJIB
            console.log("STATUS:", response.status);

            const text = await response.text();
            console.log("RESPONSE:", text);

            if (!response.ok) {
                alert("Webhook error: " + response.status);
                return;
            }

            // coba parse JSON kalau bisa
            let data;
            try {
                data = JSON.parse(text);
            } catch {
                data = text;
            }

            console.log("PARSED:", data);

            alert("Berhasil kirim ke n8n!");
        } catch (err) {
            console.error("FETCH ERROR:", err);
            alert("Gagal koneksi ke n8n");
        }
    });
});