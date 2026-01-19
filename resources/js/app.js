import './bootstrap';

let onlineUsers = [];

window.Echo.join('chat-room')
    .here((users) => {
        // Dipanggil saat kita baru join, 'users' berisi daftar semua user yang sudah online
        onlineUsers = users;
        console.log('User yang sedang online:', onlineUsers);
    })
    .joining((user) => {
        // Dipanggil saat ada user baru yang masuk
        onlineUsers.push(user);
        console.log(user.name + ' baru saja online');
    })
    .leaving((user) => {
        // Dipanggil saat user menutup browser/tab (instan!)
        onlineUsers = onlineUsers.filter(u => u.id !== user.id);
        console.log(user.name + ' telah offline');
    })
    .error((error) => {
        console.error('Gagal terhubung ke Reverb:', error);
    });
