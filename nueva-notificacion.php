<?php


//se inicia sesion
session_start();

//conectar con la base de datos
$usuario_db = "root";
$password = "";

$conn = new PDO("mysql:host=localhost:3306;dbname=test", $usuario_db, $password);

$id = $_POST["id"];

//traer nuevas notificaciones
if (isset($_POST["consulta"])) {
    $consultaSQL = $_POST["consulta"];

    $newNotificaciones = $consultaSQL;
    $notificaciones = $newNotificaciones;
    echo json_encode($notificaciones);
}

exit();
