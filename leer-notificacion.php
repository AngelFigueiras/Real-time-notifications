<?php

//iniciar la sesion

session_start();

// conectar con la base de datos
$usuario_db = "root";
$password = "";

//verificar que el puerto de la base sea el mismo donde se esta ejecutado MySql
$conn = new PDO("mysql:host=localhost:3306;dbname=test", $usuario_db, $password);

//obtener el id desde AJAX

$id = $_POST["id"];

//marcar la notitifacion como leída

$sql = "UPDATE notifications SET is_read = 1 WHERE id  = ? AND user_id = ?";
$statement = $conn->prepare($sql);
$statement->execute([
    $id,
    $_SESSION["user_id"]
]);

// mandar la respuesta del back al cliente
echo json_encode([
    "status" => "success",
]);
exit();
?>
