-- Ejecutar una sola vez en la base de datos caprino.
-- Guarda el medio de pago asociado al número de pedido de la factura.
ALTER TABLE Factura
    ADD COLUMN MedioPagoPedido VARCHAR(20) NOT NULL DEFAULT '' AFTER NumeroPayu;
