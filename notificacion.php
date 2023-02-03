<?php

// inciciar sesion 

session_start();

// conectar con la base de datos

$usuario_db = "root";
$password = "";

$conn = new PDO("mysql:host=localhost:3306;dbname=test", $usuario_db, $password);

//obtener las notificaciones oirdendas por por los no leidos primero

$sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY is_read ASC";
$statement = $conn->prepare($sql);
$statement->execute([
    $_SESSION["user_id"]
]);
$notifications = $statement->fetchAll();


?>

<!-- Mostrar notificaciones en una tabla -->

<table>
    <tr>
        <th>Mensaje</th>
        <th>Accion</th>
    </tr>

    <?php foreach ($notifications as $notification) : ?>
        <tr>
            <td><?php echo $notification["message"] ?></td>
            <td>
                <?php if (!$notification['is_read']) : ?>
                    <form onsubmit="return markAsRead();">
                        <input type="hidden" name="id" value=" <?php echo $notification['id']; ?> " />
                        <input type="hidden" name="user_id" value=" <?php echo $notification['user_id']; ?> " />
                        <input type="submit" value="Read" />
                    </form>
                <?php endif ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<script src="socket.io.js"></script>

<script>
    //conectar con el servidor Node.js
    //var socketIO = io("http://localhost:80", {
    //    transports: ['websocket', 'polling', 'flashsocket'],
    //});

    var socketIO = io("http://localhost:3000", {
        transports: ['websocket', 'polling', 'flashsocket']
    });

    function markAsRead() {
        //evitar que el formulario se envie
        event.preventDefault();

        //obtener el nodo del formulario
        var form = event.target;

        //crear objeto AJAX
        var ajax = new XMLHttpRequest();

        //seleccionar el metodo y la URL of request
        ajax.open("POST", "leer-notificacion.php", true);

        //cuando el status de la peticion cambie
        ajax.onreadystatechange = function() {

            //cuando la respuesta es recibida desde el servidor
            if (this.readyState == 4 && this.status == 200) {
                var data = JSON.parse(this.responseText);
            }


            if (data.status == "success") {

                //se remueve el boton 'read'
                form.remove();

                socketIO.emit("notificationRead", form.user_id.value);
            }
        }

        //crear un objeto con la data del formulario
        var formData = new FormData(form);

        // mandar peticion AJAX con la data del formulario
        ajax.send(formData);
    }
</script>