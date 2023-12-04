<?php

$servidor ="mysql:dbname=sistema;host=127.0.0.1";
$usuario = "root";
$pass = "";

try{
    $pdo = new PDO($servidor,$usuario,$pass);//pdo es un objeto instanciado
    echo "Estamos conectados...";

}catch(PDOException $e){
    echo "Error en la conexión | " . $e->getMessage();
}


?>