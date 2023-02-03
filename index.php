<?php

print_r("Hola desde el index ");
$usuario_db = "root";
$password = "";

// se inicia sesion y el login del usuario
session_start();

// se debera guardar la sesion durante el login
// en esta ocasion se hard-codea el valor por testeo 

$_SESSION["user_id"] = 565;

// se conecta con la base de datos

$conn = new PDO("mysql:host=localhost:3306;dbname=test", $usuario_db, $password);

// Obtener numero de las notificaciones no leidas

$sql = "SELECT COUNT(*) AS total_unread_notifications FROM notifications
    WHERE user_id = ? AND is_read = 0";
$statement = $conn->prepare($sql);
$statement->execute([
    $_SESSION["user_id"]
]);
$row = $statement->fetch();
$total_unread_notifications = $row["total_unread_notifications"];


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PWST</title>
</head>

<body>
    <input type="hidden" id="total-unread-notifications" value="<?php echo $total_unread_notifications; ?>" />
    <input type="hidden" id="user-id" value="<?php echo $_SESSION['user_id']; ?>" />

    <p id="total-unread-notifications">
        <?php echo $total_unread_notifications; ?>
    </p>
</body>

</html>


<!-- save variables in hidden input field to access -->



<script src="socket.io.js"></script>

<script>
    // obtener las variables en javascript

    var totalUnreadNotifications = document.getElementById("total-unread-notifications").value;
    totalUnreadNotifications = parseInt(totalUnreadNotifications);

    //mostrar contador en la barra de titulo
    showTitleBarNotification();

    //conectar con el servidor Node JS
    var socketIO = io("http://localhost:3000", {
        transports: ['websocket', 'polling', 'flashsocket']
    });


    //connect user con Node JS server
    var userId = document.getElementById("user-id").value;
    socketIO.emit("connected", userId);

    // Eventos del lado del cliente
    socketIO.on("newNotification", function(data) {
        totalUnreadNotifications++;
        showTitleBarNotification();
    });

    socketIO.on("notificationRead", function(data) {
        totalUnreadNotifications--;
        showTitleBarNotification();
    });



    function showTitleBarNotification() {
        var pattern = /^\(\d+\)/;

        if (totalUnreadNotifications == 0) {
            document.title = document.title.replace(pattern, "");
            return;
        }


        //actualizar el contador
        if (pattern.test(document.title)) {
            document.title = document.title.replace(pattern, "(" + totalUnreadNotifications + ")");
        } else {
            //anteponer el contador
            document.title = "(" + totalUnreadNotifications + ")" + document.title;
        }

    }
</script>