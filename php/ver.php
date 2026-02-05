<?php
require_once 'config.php';
require_once 'functions.php';

$id = $_GET['id'] ?? 0;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Obtener factura
$stmt = $pdo->prepare("SELECT * FROM facturas WHERE id = ?");
$stmt->execute([$id]);
$factura = $stmt->fetch();

if (!$factura) {
    die("Factura no encontrada.");
}

// Obtener detalles
$stmt = $pdo->prepare("SELECT * FROM detalles_factura WHERE factura_id = ?");
$stmt->execute([$id]);
$detalles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura <?= h($factura['numero_factura']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <a href="index.php" class="btn btn-outline-secondary mb-3">&larr; Volver al inicio</a>
        
        <div class="card shadow">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-6">
                        <h2 class="mb-0">Factura</h2>
                        <h4 class="text-primary"><?= h($factura['numero_factura']) ?></h4>
                        <?php if ($factura['es_negociable']): ?>
                            <span class="badge bg-warning text-dark">Factura Negociable</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-6 text-end">
                        <p class="mb-1"><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($factura['fecha_emision'])) ?></p>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-md-6">
                        <h5 class="text-secondary">Cliente:</h5>
                        <p class="mb-0"><strong><?= h($factura['cliente_nombre']) ?></strong></p>
                        <p class="mb-0">RUC: <?= h($factura['cliente_ruc']) ?></p>
                        <p class="mb-0"><?= h($factura['cliente_direccion']) ?></p>
                        <p class="mb-0"><?= h($factura['cliente_email']) ?></p>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-center">Unidad</th>
                                <th class="text-end">Precio Unit.</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detalles as $item): ?>
                                <tr>
                                    <td><?= h($item['producto_nombre']) ?></td>
                                    <td class="text-center"><?= $item['cantidad'] + 0 ?></td>
                                    <td class="text-center"><?= h($item['unidad_medida']) ?></td>
                                    <td class="text-end"><?= formatear_moneda($item['precio_unitario']) ?></td>
                                    <td class="text-end"><?= formatear_moneda($item['subtotal']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row justify-content-end">
                    <div class="col-md-4">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Subtotal:</strong></td>
                                <td class="text-end"><?= formatear_moneda($factura['subtotal']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>IVA (<?= $factura['porcentaje_iva'] + 0 ?>%):</strong></td>
                                <td class="text-end"><?= formatear_moneda($factura['monto_iva']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Propina:</strong></td>
                                <td class="text-end"><?= formatear_moneda($factura['propina']) ?></td>
                            </tr>
                            <tr class="border-top">
                                <td class="fs-5"><strong>Total:</strong></td>
                                <td class="text-end fs-5 fw-bold"><?= formatear_moneda($factura['total']) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white text-center d-print-none">
                <button onclick="window.print()" class="btn btn-secondary">Imprimir</button>
            </div>
        </div>
    </div>
</body>
</html>
