import './bootstrap';
import './cart'

// // Inisialisasi daftar user online global
window.onlineUsers = [];


Echo.join('online-users')
    .here(users => {
        Alpine.store('status').setUsers(users);
    })
    .joining(user => {
        Alpine.store('status').addUser(user);
    })
    .leaving(user => {
        Alpine.store('status').removeUser(user);
    });

document.addEventListener('alpine:init', () => {

    Alpine.store('status', {
        onlineUsers: [],

        setUsers(users) {
            this.onlineUsers = users;
        },

        addUser(user) {
            if (!this.onlineUsers.some(u => u.id === user.id)) {
                this.onlineUsers.push(user);
            }
        },

        removeUser(user) {
            this.onlineUsers =
                this.onlineUsers.filter(u => u.id !== user.id);
        }
    });

});

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