<?php

//conecta con la base de datos

print_r("Mandar notificacion");
$usuario_db = "root";
$password = "";

//el puerto es el de la base de datos 
$conn = new PDO("mysql:host=localhost:3306;dbname=test", $usuario_db, $password);

//crear tabla si no existe

$sql = "CREATE TABLE IF NOT EXISTS notifications(
    id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER,
    message TEXT,
    is_read BOOLEAN,
    created_at DATETIME
)";

//preparar query

$statement = $conn->prepare($sql);

// ejecutar la sentencia

$statement->execute();

//usuario que necesita recibir la notificacion

$user_id = 565;
$message = "Se te cayó la pierna";
$is_read = 0;


//insertar notitifacion en la base de datos

$sql = "INSERT INTO notifications(user_id, message, is_read, created_at)
    VALUES (?, ?, ?, NOW())";
$statement = $conn->prepare($sql);
$statement->execute([
    $user_id, $message, $is_read
]);

?>

<input type="hidden" id="user-id" value="<?php echo $user_id; ?>" />
<!-- include socket IO JS -->

<script src="socket.io.js"></script>

<script>
    // conectar con Node JS server
    var socketIO = io("http://localhost:3000", {
        transports: ['websocket', 'polling', 'flashsocket']
    });

    // obtener id de usarios
    var userId = document.getElementById("user-id").value;

    //send notification to the server
    socketIO.emit("newNotification", userId);

    socketIO.emit("aumentarPuntero")

    socketIO.emit("updateNotificaciones");
</script>