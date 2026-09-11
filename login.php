<?php

require_once "conexao.php";

$usuario = $_POST['usuario'];
$senha = $_POST['senha'];

var_dump($_POST);
var_dump($usuario);
var_dump($senha);

$sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND senha = '$senha'";

echo "<pre>";
echo $sql;
echo "</pre>";

$resultado = $pdo->query($sql);

$usuarioEncontrado = $resultado->fetch(PDO::FETCH_ASSOC);

if ($usuarioEncontrado !== false) {
    echo "<h1>Login realizado com sucesso!</h1>";
} else {
    echo "<h1>Usuário ou senha incorretos!</h1>";
}