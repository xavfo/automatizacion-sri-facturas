<?php
require_once 'config.php';
require_once 'functions.php';

// Obtener las últimas facturas
$stmt = $pdo->query("SELECT * FROM facturas ORDER BY created_at DESC LIMIT 20");
$facturas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Facturación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Facturas Recientes</h1>
            <a href="crear.php" class="btn btn-primary btn-lg">+ Nueva Factura</a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th># Factura</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($facturas) > 0): ?>
                            <?php foreach ($facturas as $factura): ?>
                                <tr>
                                    <td><?= h($factura['numero_factura']) ?></td>
                                    <td>
                                        <?= h($factura['cliente_nombre']) ?><br>
                                        <small class="text-muted"><?= h($factura['cliente_ruc']) ?></small>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($factura['fecha_emision'])) ?></td>
                                    <td class="fw-bold"><?= formatear_moneda($factura['total']) ?></td>
                                    <td>
                                        <?php if ($factura['es_negociable']): ?>
                                            <span class="badge bg-warning text-dark">Negociable</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Normal</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="ver.php?id=<?= $factura['id'] ?>" class="btn btn-sm btn-info text-white">Ver Detalle</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">No hay facturas registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
