<?php
//inciar sesion
session_start();

//conectar con la base de datos mysql

$usuario_db = "root";
$password = "";

$conn = new PDO("mysql:host=localhost:3306;dbname=test", $usuario_db, $password);

//Obtenemos las notificaciones de la base de datos ordenadas por las que no estan leidas

$sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY is_read ASC";
$statement = $conn->prepare($sql);
$statement->execute([
    $_SESSION["user_id"]
]);
$notificaciones = $statement->fetchAll();


//obtenemos las notificaciones y las almacenamos como no leíadas

$sql_unread_not = "SELECT COUNT(*) AS notificaciones_no_leidas FROM notifications
        WHERE user_id = ? AND is_read = 0";
$statement = $conn->prepare($sql_unread_not);
$statement->execute([
    $_SESSION["user_id"]
]);
$row = $statement->fetch();
$notificaciones_no_leidas = $row['notificaciones_no_leidas'];
?>


<div class="modulo-notificaciones">

    <div class="notificaciones">
        <button id="btn-notificaciones" class="btn-notificaciones" onclick="mostrarNotificaciones();">
            <p id="notificaciones-no-leidas"> <?php echo $notificaciones_no_leidas; ?> </p>
        </button>

        <div id="tabla-notificaciones" class="tabla-notificaciones">
            <table>
                <tr>
                    <th>Mensaje</th>
                </tr>

                <?php foreach ($notificaciones as $notificacion) : ?>
                    <tr>
                        <td id="notificacion"><?php echo $notificacion["message"] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

</div>

<!-- Se incluye el archivo js para poder usar Socket.io -->
<script src="socket.io.js"></script>

<script>
    //variables para manioular el estado de las variables
    var state = false

    //notificaciones
    var notificaciones = <?php echo json_encode($notificaciones); ?>;

    var notificaciones_no_leidas = document.getElementById('notificaciones_no_leidas');

    //nos conectamos con el servidor node.js
    var socketIO = io("http://localhost:3000", {
        transports: ['websocket', 'polling', 'flashsocket']
    });

    //funcion para mostrar las notificaciones
    function mostrarNotificaciones() {
        if (state == false) {
            document.getElementById("tabla-notificaciones").style.visibility = "visible";
            state = !state;
        } else {
            document.getElementById("tabla-notificaciones").style.visibility = "hidden";
            state = false
        }
    }

    //Funcion para aumentar puntero
    function aumentarPuntero() {
        var puntero = document.getElementById("notificaciones-no-leidas");
        var punteroValue = parseInt(puntero.innerHTML);
        var punteroNewValue = punteroValue + 1
        puntero.innerHTML = punteroNewValue
    }


    function updateNotificaciones() {
        location.reload();
    }


    //funciones escuchadas del lado del cliente
    socketIO.on("aumentarPuntero", () => {
        aumentarPuntero();
    })

    socketIO.on("updateNotificaciones", () => {
        updateNotificaciones();
    })
</script>

<style>
    .modulo-notificaciones {
        width: 100%;
        height: 100%;
        display: flex;
    }

    .tabla-notificaciones {
        visibility: hidden;
    }

    .btn-notificaciones {
        height: 100px;
        width: 100px;
        font-size: 16px;
        border-radius: 50% !important;
        background-color: #004A7F;
        -webkit-border-radius: 10px;
        border-radius: 10px;
        border: none;
        color: #FFFFFF;
        cursor: pointer;
        display: inline-block;
        font-family: Arial;
        font-size: 20px;
        padding: 5px 10px;
        text-align: center;
        text-decoration: none;
        -webkit-animation: glowing 1500ms infinite;
        -moz-animation: glowing 1500ms infinite;
        -o-animation: glowing 1500ms infinite;
        animation: glowing 1500ms infinite;
    }

    @-webkit-keyframes glowing {
        0% {
            background-color: #B20000;
            -webkit-box-shadow: 0 0 3px #B20000;
        }

        50% {
            background-color: #FF0000;
            -webkit-box-shadow: 0 0 40px #FF0000;
        }

        100% {
            background-color: #B20000;
            -webkit-box-shadow: 0 0 3px #B20000;
        }
    }

    @-moz-keyframes glowing {
        0% {
            background-color: #B20000;
            -moz-box-shadow: 0 0 3px #B20000;
        }

        50% {
            background-color: #FF0000;
            -moz-box-shadow: 0 0 40px #FF0000;
        }

        100% {
            background-color: #B20000;
            -moz-box-shadow: 0 0 3px #B20000;
        }
    }

    @-o-keyframes glowing {
        0% {
            background-color: #B20000;
            box-shadow: 0 0 3px #B20000;
        }

        50% {
            background-color: #FF0000;
            box-shadow: 0 0 40px #FF0000;
        }

        100% {
            background-color: #B20000;
            box-shadow: 0 0 3px #B20000;
        }
    }

    @keyframes glowing {
        0% {
            background-color: #B20000;
            box-shadow: 0 0 3px #B20000;
        }

        50% {
            background-color: #FF0000;
            box-shadow: 0 0 40px #FF0000;
        }

        100% {
            background-color: #B20000;
            box-shadow: 0 0 3px #B20000;
        }
    }
</style>