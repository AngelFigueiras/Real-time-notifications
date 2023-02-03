//inicializar servidor express

var express = require('express');
var app = express();

// crear servidor http desde la instancia de express
var http = require('http').createServer(app)

//incluir socket IO
var socketIO = require('socket.io')(http,{
    cors: {
        origin: ["http://localhost:80"]
    }
});

// inicializar el servidor

http.listen(process.env.PORT || 3000, function(){
    console.log("Servidor corriendo en el puerto 3000");

    // un array para alamcenar todos los IDs de los usuarios
    var users = [];

    // se llamara cuando io() sea llamdo por el cliente npm install express --save
    socketIO.on("connection", function(socket){
        //llamar manualmente desde el cliente para conectar al usaurio al servidor
        socket.on("connected", function(id){
            users[id] = socket.id
        })

        //eventos que escucha el servidor del lado del cliente
        socket.on("newNotification", function (userId) { 
            // mandar notificacion al usuario seleccionado
            socketIO.to(users[userId]).emit("newNotification", userId)
        })

        socket.on("notificationRead", function (userId) { 
            socketIO.to(users[userId]).emit("notificationRead", userId)
        })

        socket.on("holaMundo", () => {
            socketIO.emit("holaMundo")
        })

        socket.on("aumentarPuntero", () => {
            socketIO.emit("aumentarPuntero")
        })

        socket.on("updateNotificaciones", () => { 
            socketIO.emit("updateNotificaciones")
        })

    })
})

