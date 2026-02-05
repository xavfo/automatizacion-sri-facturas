<?php
require_once 'config.php';
require_once 'functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Iniciar transacción
        $pdo->beginTransaction();

        // 1. Recibir y validar datos del header
        $cliente_nombre = $_POST['cliente_nombre'] ?? '';
        $cliente_ruc = $_POST['cliente_ruc'] ?? '';
        $cliente_direccion = $_POST['cliente_direccion'] ?? '';
        $cliente_email = $_POST['cliente_email'] ?? '';
        $fecha_emision = $_POST['fecha_emision'] ?? date('Y-m-d');
        $porcentaje_iva = floatval($_POST['porcentaje_iva'] ?? 12);
        $propina = floatval($_POST['propina'] ?? 0);
        $es_negociable = isset($_POST['es_negociable']) ? 1 : 0;
        
        $numero_factura = generar_numero_factura();

        // Validaciones básicas
        if (empty($cliente_nombre) || empty($cliente_ruc)) {
            throw new Exception("Nombre del cliente y RUC/CI son obligatorios.");
        }

        // 2. Procesar detalles para calculos totales
        $items = json_decode($_POST['items_json'] ?? '[]', true);
        if (empty($items)) {
            throw new Exception("La factura debe tener al menos un item.");
        }

        $subtotal = 0;
        foreach ($items as $item) {
            $cantidad = floatval($item['cantidad']);
            $precio = floatval($item['precio']);
            $subtotal += $cantidad * $precio;
        }

        $monto_iva = $subtotal * ($porcentaje_iva / 100);
        $total = $subtotal + $monto_iva + $propina;

        // 3. Insertar factura
        $stmt = $pdo->prepare("INSERT INTO facturas (numero_factura, cliente_nombre, cliente_ruc, cliente_direccion, cliente_email, fecha_emision, subtotal, porcentaje_iva, monto_iva, propina, total, es_negociable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $numero_factura,
            $cliente_nombre,
            $cliente_ruc,
            $cliente_direccion,
            $cliente_email,
            $fecha_emision,
            $subtotal,
            $porcentaje_iva,
            $monto_iva,
            $propina,
            $total,
            $es_negociable
        ]);
        
        $factura_id = $pdo->lastInsertId();

        // 4. Insertar detalles
        $stmt_detalle = $pdo->prepare("INSERT INTO detalles_factura (factura_id, producto_nombre, cantidad, unidad_medida, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
        
        foreach ($items as $item) {
            $cantidad = floatval($item['cantidad']);
            $precio = floatval($item['precio']);
            $item_subtotal = $cantidad * $precio;
            
            $stmt_detalle->execute([
                $factura_id,
                $item['producto'],
                $cantidad,
                $item['unidad'] ?? 'unidades',
                $precio,
                $item_subtotal
            ]);
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Factura guardada correctamente', 'id' => $factura_id]);

    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
