<?php

require_once "conexion.php";

$sqlProductos = "SELECT * FROM producto ORDER BY id_producto";

$productosTabla = $conexion->query($sqlProductos);
$productosFormulario = $conexion->query($sqlProductos);

$sqlClientes = "SELECT * FROM cliente ORDER BY id_cliente";

$clientesTabla = $conexion->query($sqlClientes);
$clientesFormulario = $conexion->query($sqlClientes);

$sqlCompras = "SELECT
                    compra.id_compra,
                    cliente.nombre AS cliente,
                    producto.nombre AS producto,
                    compra.cantidad,
                    compra.total,
                    compra.fecha
               FROM compra
               INNER JOIN cliente
                    ON compra.id_cliente = cliente.id_cliente
               INNER JOIN producto
                    ON compra.id_producto = producto.id_producto
               ORDER BY compra.id_compra";

$compras = $conexion->query($sqlCompras);

$sqlClientesFrecuentes = "SELECT
                                cliente.nombre,
                                cliente.email,
                                COUNT(compra.id_compra) AS numero_compras
                           FROM cliente
                           INNER JOIN compra
                                ON cliente.id_cliente = compra.id_cliente
                           GROUP BY
                                cliente.id_cliente,
                                cliente.nombre,
                                cliente.email
                           HAVING COUNT(compra.id_compra) > 2
                           ORDER BY numero_compras DESC";

$clientesFrecuentes = $conexion->query($sqlClientesFrecuentes);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de tienda</title>

    <link rel="stylesheet" href="styles.css">

    <script>
        function validarProducto() {
            let nombre = document.getElementById(
                "nombre_producto"
            ).value.trim();

            let descripcion = document.getElementById(
                "descripcion"
            ).value.trim();

            let precio = document.getElementById(
                "precio"
            ).value;

            let stock = document.getElementById(
                "stock"
            ).value;

            if (
                nombre === "" ||
                descripcion === "" ||
                precio === "" ||
                stock === ""
            ) {
                alert("Debe completar todos los datos del producto.");
                return false;
            }

            if (precio <= 0) {
                alert("El precio debe ser mayor que cero.");
                return false;
            }

            if (stock < 0) {
                alert("El stock no puede ser negativo.");
                return false;
            }

            return true;
        }

        function validarCliente() {
            let nombre = document.getElementById(
                "nombre_cliente"
            ).value.trim();

            let email = document.getElementById(
                "email"
            ).value.trim();

            let direccion = document.getElementById(
                "direccion"
            ).value.trim();

            if (
                nombre === "" ||
                email === "" ||
                direccion === ""
            ) {
                alert("Debe completar todos los datos del cliente.");
                return false;
            }

            return true;
        }

        function validarCompra() {
            let cliente = document.getElementById(
                "id_cliente"
            ).value;

            let producto = document.getElementById(
                "id_producto"
            ).value;

            let cantidad = document.getElementById(
                "cantidad"
            ).value;

            if (
                cliente === "" ||
                producto === "" ||
                cantidad === ""
            ) {
                alert("Debe completar los datos de la compra.");
                return false;
            }

            if (cantidad <= 0) {
                alert("La cantidad debe ser mayor que cero.");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <header>
        <h1>Tienda de comercio electrónico</h1>
        <p>Gestión de productos, clientes y compras</p>
    </header>

    <main>
        <section>
            <h2>Registrar producto</h2>

            <form action="procesar.php" method="post" onsubmit="return validarProducto()">
                <input type="hidden" name="accion" value="registrar_producto">

                <label for="nombre_producto">Nombre del producto:</label>
                <input type="text" id="nombre_producto" name="nombre" required>

                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" required></textarea>

                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" min="1" required>

                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" min="0" required>

                <button type="submit">Registrar producto</button>
            </form>
        </section>

        <section>
            <h2>Registrar cliente</h2>

            <form action="procesar.php" method="post" onsubmit="return validarCliente()">
                <input type="hidden" name="accion" value="registrar_cliente">

                <label for="nombre_cliente">Nombre del cliente:</label>
                <input type="text" id="nombre_cliente" name="nombre" required>

                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="email" required>

                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" required>

                <button type="submit">Registrar cliente</button>
            </form>
        </section>

        <section>
            <h2>Registrar compra</h2>

            <form action="procesar.php" method="post" onsubmit="return validarCompra()">
                <input type="hidden" name="accion" value="registrar_compra">

                <label for="id_cliente">Cliente:</label>

                <select id="id_cliente" name="id_cliente" required>
                    <option value="">Seleccione un cliente</option>

                    <?php while ($cliente = $clientesFormulario->fetch_assoc()): ?>
                        <option value="<?php echo $cliente["id_cliente"]; ?>">
                            <?php echo htmlspecialchars($cliente["nombre"]); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="id_producto">Producto:</label>

                <select id="id_producto" name="id_producto" required>
                    <option value="">Seleccione un producto</option>

                    <?php while ($producto = $productosFormulario->fetch_assoc()): ?>
                        <?php if ($producto["stock"] > 0): ?>
                            <option value="<?php echo $producto["id_producto"]; ?>">
                                <?php
                                echo htmlspecialchars($producto["nombre"]);
                                echo " - Stock: ";
                                echo $producto["stock"];
                                ?>
                            </option>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </select>

                <label for="cantidad">Cantidad:</label>
                <input type="number" id="cantidad" name="cantidad" min="1" required>

                <button type="submit">Registrar compra</button>
            </form>
        </section>

        <section>
            <h2>Productos registrados</h2>

            <div class="contenedor-tabla">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Disponibilidad</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($productosTabla->num_rows > 0): ?>
                            <?php while ($producto = $productosTabla->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $producto["id_producto"]; ?></td>

                                    <td>
                                        <?php echo htmlspecialchars($producto["nombre"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($producto["descripcion"]); ?>
                                    </td>

                                    <td>
                                        $<?php
                                        echo number_format(
                                            $producto["precio"],
                                            0,
                                            ",",
                                            "."
                                        );
                                        ?>
                                    </td>

                                    <td><?php echo $producto["stock"]; ?></td>

                                    <td>
                                        <?php
                                        if ($producto["stock"] > 0) {
                                            echo "Disponible";
                                        } else {
                                            echo "Sin stock";
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">
                                    No existen productos registrados.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
                <section>
            <h2>Clientes registrados</h2>

            <div class="contenedor-tabla">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo electrónico</th>
                            <th>Dirección</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($clientesTabla->num_rows > 0): ?>
                            <?php while ($cliente = $clientesTabla->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $cliente["id_cliente"]; ?></td>

                                    <td>
                                        <?php echo htmlspecialchars($cliente["nombre"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($cliente["email"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($cliente["direccion"]); ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">
                                    No existen clientes registrados.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section>
            <h2>Compras registradas</h2>

            <div class="contenedor-tabla">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($compras->num_rows > 0): ?>
                            <?php while ($compra = $compras->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $compra["id_compra"]; ?></td>

                                    <td>
                                        <?php echo htmlspecialchars($compra["cliente"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($compra["producto"]); ?>
                                    </td>

                                    <td><?php echo $compra["cantidad"]; ?></td>

                                    <td>
                                        $<?php
                                        echo number_format(
                                            $compra["total"],
                                            0,
                                            ",",
                                            "."
                                        );
                                        ?>
                                    </td>

                                    <td><?php echo $compra["fecha"]; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">
                                    No existen compras registradas.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section>
            <h2>Clientes con más de dos compras</h2>

            <div class="contenedor-tabla">
                <table>
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Correo electrónico</th>
                            <th>Número de compras</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($clientesFrecuentes->num_rows > 0): ?>
                            <?php while (
                                $cliente = $clientesFrecuentes->fetch_assoc()
                            ): ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($cliente["nombre"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($cliente["email"]); ?>
                                    </td>

                                    <td>
                                        <?php echo $cliente["numero_compras"]; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">
                                    No existen clientes con más de dos compras registradas.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer>
        <p>Gestión de compras en línea</p>
    </footer>
</body>
</html>