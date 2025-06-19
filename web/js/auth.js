function auth_check() {
    const user = JSON.parse(localStorage.getItem('user'));
    if (user) window.location.href = user.role === 'admin' ? 'admin.html' : 'cashier.html';
}

function auth_login() {
    window.location.href = 'login.html'; 
}

function auth_logout() { 
    localStorage.removeItem('user'); 
    window.location.href = 'login.html'; 
}