document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('loginForm');
    if(form) {
        form.addEventListener('submit', function() {
            const email = document.getElementById('email').value;
            const fullnameInput = document.getElementById('fullname');
            
            let displayName = '';
            if (fullnameInput && fullnameInput.value.trim() !== '') {
                // Jika dari halaman Register, ambil langsung dari input Nama Lengkap
                displayName = fullnameInput.value.trim();
            } else {
                // Jika dari halaman Login, fallback pakai sisa localStorage yang ada, 
                // atau pakai email prefix jika pertama kali login dari device baru.
                const existingName = localStorage.getItem('takosaving_user');
                if (existingName) {
                    displayName = existingName;
                } else {
                    const name = email.split('@')[0];
                    displayName = name.charAt(0).toUpperCase() + name.slice(1);
                }
            }
            
            localStorage.setItem('takosaving_user', displayName);
            localStorage.setItem('takosaving_user_id', email.toLowerCase().trim());
        });
    }
});
