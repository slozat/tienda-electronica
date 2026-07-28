USE tienda;

DELETE FROM compra;
DELETE FROM producto;
DELETE FROM cliente;

ALTER TABLE compra AUTO_INCREMENT = 1;
ALTER TABLE producto AUTO_INCREMENT = 1;
ALTER TABLE cliente AUTO_INCREMENT = 1;

INSERT INTO producto (nombre, descripcion, precio, stock) VALUES
('Notebook', 'Notebook de 15 pulgadas para trabajo y estudio', 450000, 10),
('Mouse inalámbrico', 'Mouse inalámbrico con conexión USB', 12000, 30),
('Teclado USB', 'Teclado alámbrico con distribución en español', 25000, 20);

INSERT INTO cliente (nombre, email, direccion) VALUES
('Ana Pérez', 'ana.perez@email.com', 'Avenida Central 125'),
('Carlos Soto', 'carlos.soto@email.com', 'Los Robles 350'),
('María González', 'maria.gonzalez@email.com', 'Pasaje Las Flores 82');

INSERT INTO compra
(cantidad, total, fecha, id_producto, id_cliente) VALUES
(1, 450000, '2026-07-01', 1, 1),
(2, 24000,  '2026-07-02', 2, 1),
(1, 25000,  '2026-07-03', 3, 1),
(1, 12000,  '2026-07-04', 2, 1),
(1, 450000, '2026-07-05', 1, 2),
(2, 50000,  '2026-07-06', 3, 2),
(3, 36000,  '2026-07-07', 2, 2),
(1, 25000,  '2026-07-08', 3, 3),
(2, 24000,  '2026-07-09', 2, 3),
(1, 450000, '2026-07-10', 1, 3);

UPDATE producto
SET stock = stock - 3
WHERE id_producto = 1;

UPDATE producto
SET stock = stock - 8
WHERE id_producto = 2;

UPDATE producto
SET stock = stock - 4
WHERE id_producto = 3;