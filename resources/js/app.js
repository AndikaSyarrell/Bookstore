import './bootstrap';

// // Inisialisasi daftar user online global
// window.onlineUsers = [];

// console.log('--- Inisialisasi WebSocket Reverb ---');

// window.Echo.join('chat-room')
//     .here((users) => {
//         // Berjalan saat pertama kali Anda berhasil masuk ke channel
//         window.onlineUsers = users;
        
//         console.group('Reverb: Status Awal');
//         console.log('Koneksi berhasil terhubung.');
//         console.log('Jumlah user online saat ini:', users.length);
//         console.table(users); // Menampilkan daftar user dalam bentuk tabel di console
//         console.groupEnd();

//         // Kirim event ke Alpine.js jika diperlukan
//         window.dispatchEvent(new CustomEvent('users-updated'));
//     })
//     .joining((user) => {
//         // Berjalan saat ada user lain yang baru masuk
//         window.onlineUsers.push(user);
        
//         console.log(`%c[ONLINE] %c${user.name} (ID: ${user.id}) bergabung.`, 'color: green; font-weight: bold;', 'color: inherit;');
        
//         window.dispatchEvent(new CustomEvent('users-updated'));
//     })
//     .leaving((user) => {
//         // Berjalan saat ada user yang menutup browser/tab
//         window.onlineUsers = window.onlineUsers.filter(u => u.id !== user.id);
        
//         console.log(`%c[OFFLINE] %c${user.name} (ID: ${user.id}) telah keluar.`, 'color: red; font-weight: bold;', 'color: inherit;');
        
//         window.dispatchEvent(new CustomEvent('users-updated'));
//     })
//     .error((error) => {
//         // Berjalan jika koneksi gagal (misal: Reverb server belum menyala)
//         console.error('%c[ERROR] %cGagal terhubung ke Reverb Server!', 'color: white; background: red; padding: 2px 5px; border-radius: 3px;', 'color: red;');
//         console.error('Detail Error:', error);
//     });

// document.addEventListener('alpine:init', () => {
//     // Membuat store global di Alpine
//     Alpine.store('status', {
//         onlineUsers: [],
        
//         setUsers(users) {
//             this.onlineUsers = users;
//         },
//         addUser(user) {
//             this.onlineUsers.push(user);
//         },
//         removeUser(user) {
//             this.onlineUsers = this.onlineUsers.filter(u => u.id !== user.id);
//         }
//     });
// });

// window.Echo.join('chat-room')
//     .here((users) => {
//         console.log('Users online:', users);
//         Alpine.store('status').setUsers(users);
//     })
//     .joining((user) => {
//         console.log('Joining:', user.name);
//         Alpine.store('status').addUser(user);
//     })
//     .leaving((user) => {
//         console.log('Leaving:', user.name);
//         Alpine.store('status').removeUser(user);
//     });