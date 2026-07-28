<?php

require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "Solicitud no válida.";
    exit;
}

$accion = $_POST["accion"] ?? "";

if ($accion === "registrar_producto") {
    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"]);
    $precio = (float) $_POST["precio"];
    $stock = (int) $_POST["stock"];

    if (
        $nombre === "" ||
        $descripcion === "" ||
        $precio <= 0 ||
        $stock < 0
    ) {
        echo "Los datos del producto no son válidos.";
        exit;
    }

    $sql = "INSERT INTO producto
            (nombre, descripcion, precio, stock)
            VALUES
            ('$nombre', '$descripcion', '$precio', '$stock')";

    if ($conexion->query($sql) === true) {
        echo "Producto registrado correctamente.";
        echo "<br><br>";
        echo "<a href='tienda.php'>Volver a la tienda</a>";
    } else {
        echo "Error al registrar el producto: ";
        echo $conexion->error;
    }
}

if ($accion === "registrar_cliente") {
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $direccion = trim($_POST["direccion"]);

    if (
        $nombre === "" ||
        $email === "" ||
        $direccion === ""
    ) {
        echo "Los datos del cliente no son válidos.";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "El correo electrónico no es válido.";
        exit;
    }

    $sql = "INSERT INTO cliente
            (nombre, email, direccion)
            VALUES
            ('$nombre', '$email', '$direccion')";

    if ($conexion->query($sql) === true) {
        echo "Cliente registrado correctamente.";
        echo "<br><br>";
        echo "<a href='tienda.php'>Volver a la tienda</a>";
    } else {
        echo "Error al registrar el cliente: ";
        echo $conexion->error;
    }
}

if ($accion === "registrar_compra") {
    $id_cliente = (int) $_POST["id_cliente"];
    $id_producto = (int) $_POST["id_producto"];
    $cantidad = (int) $_POST["cantidad"];

    if (
        $id_cliente <= 0 ||
        $id_producto <= 0 ||
        $cantidad <= 0
    ) {
        echo "Los datos de la compra no son válidos.";
        exit;
    }

    $sqlProducto = "SELECT precio, stock
                    FROM producto
                    WHERE id_producto = $id_producto";

    $resultadoProducto = $conexion->query(
        $sqlProducto
    );

    if ($resultadoProducto->num_rows === 0) {
        echo "El producto seleccionado no existe.";
        exit;
    }

    $producto = $resultadoProducto->fetch_assoc();

    if ($cantidad > $producto["stock"]) {
        echo "No existe stock suficiente para realizar la compra.";
        echo "<br><br>";
        echo "<a href='tienda.php'>Volver a la tienda</a>";
        exit;
    }

    $precio = $producto["precio"];
    $total = $precio * $cantidad;
    $fecha = date("Y-m-d");

    $sqlCompra = "INSERT INTO compra
                  (cantidad, total, fecha,
                  id_producto, id_cliente)
                  VALUES
                  ('$cantidad', '$total', '$fecha',
                  '$id_producto', '$id_cliente')";

    if ($conexion->query($sqlCompra) === true) {
        $nuevoStock = $producto["stock"] - $cantidad;

        $sqlStock = "UPDATE producto
                     SET stock = $nuevoStock
                     WHERE id_producto = $id_producto";

        $conexion->query($sqlStock);

        echo "Compra registrada correctamente.";
        echo "<br><br>";
        echo "<a href='tienda.php'>Volver a la tienda</a>";
    } else {
        echo "Error al registrar la compra: ";
        echo $conexion->error;
    }
}

$conexion->close();