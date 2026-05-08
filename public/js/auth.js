document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('loginForm');

    if(form){

        form.addEventListener('submit', async function(e){

            e.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const fullnameInput = document.getElementById('fullname');

            //Daftar
            if(fullnameInput){

                const fullname = fullnameInput.value;

                const { data, error } = await supabaseClient.auth.signUp({
                    email: email,
                    password: password,
                    options: {
                        data: {
                            fullname: fullname
                        }
                    }
                });

                if(error){
                    alert(error.message);
                    return;
                }

                // Simpan login
                localStorage.setItem('isLoggedIn', 'true');
                localStorage.setItem('takosaving_user', fullname);
                localStorage.setItem('takosaving_user_id', email);

                alert("Register berhasil");

                window.location.href = "/dashboard";
            }

            //Login 
            else{

                const { data, error } = await supabaseClient.auth.signInWithPassword({
                    email: email,
                    password: password,
                });

                if(error){
                    alert(error.message);
                    return;
                }

                // Ambil nama lengkap dari metadata jika tersedia, atau gunakan bagian sebelum '@' dari email
                const displayName =
                    localStorage.getItem('takosaving_user') ||
                    email.split('@')[0];

                // SIMPAN SESSION
                localStorage.setItem('isLoggedIn', 'true');
                localStorage.setItem('takosaving_user', displayName);
                localStorage.setItem('takosaving_user_id', email);

                alert("Login berhasil");

                window.location.href = "/dashboard";
            }

        });

    }

});