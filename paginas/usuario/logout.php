<?php
$raiz = '../../';
require $raiz . 'includes/conexion.php';

//cerramos sesion vaciando los datos y destruyendo la sesión
$_SESSION = [];
session_destroy();

header('Location: ' . $raiz . 'index.php');
exit;