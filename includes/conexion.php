<?php
    // Activa las sesiones en todas las páginas .
    session_start();

    $host = '127.0.0.1';
    $puerto = '8889';
    $baseDatos = 'water_db';
    $usuarioBd = 'root';
    $claveBd = 'root';


    try{
        $pdo = new PDO("mysql:host=$host;port=$puerto;dbname=$baseDatos;charset=utf8mb4", $usuarioBd, $claveBd,[
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //si una consulta SQL falla, PHP lanzará una excepción en lugar de continuar ejecutando el script
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //los datos se leen por su nombre: $fila['nombre']
        ]);
    #crea una conexión nueva (new PDO) a un MySQL que está en $host, puerto $puerto, base de datos $baseDatos, entrando con $usuarioBd y $claveBd, y guárdala en $pdo
    }catch(PDOException $e){
        die("Lo sentimos, ahora mismo no podemos conectar con la base de datos. Inténtelo más tarde.");
    }
    
