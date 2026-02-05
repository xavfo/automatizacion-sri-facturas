<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Factura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-row { background-color: #fff; transition: background-color 0.3s; }
        .product-row:hover { background-color: #f1f1f1; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Nueva Factura</h4>
                        <a href="index.php" class="btn btn-outline-light btn-sm">Volver</a>
                    </div>
                    <div class="card-body">
                        <form id="invoiceForm">
                            <!-- Sección Cliente -->
                            <h5 class="mb-3 text-secondary">Datos del Cliente</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre / Razón Social *</label>
                                    <input type="text" class="form-control" name="cliente_nombre" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">RUC / CI *</label>
                                    <input type="text" class="form-control" name="cliente_ruc" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="cliente_email">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" class="form-control" name="cliente_direccion">
                                </div>
                            </div>

                            <hr>

                            <!-- Sección Configuración -->
                            <div class="row g-3 mb-4 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">Fecha Emisión</label>
                                    <input type="date" class="form-control" name="fecha_emision" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">% IVA</label>
                                    <select class="form-select" name="porcentaje_iva" id="porcentaje_iva" onchange="calcularTotales()">
                                        <option value="12">12%</option>
                                        <option value="15">15%</option>
                                        <option value="8">8%</option>
                                        <option value="0">0% (Exento)</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="es_negociable" id="es_negociable">
                                        <label class="form-check-label" for="es_negociable">
                                            Factura Negociable
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Sección Productos -->
                            <h5 class="mb-3 text-secondary">Detalle de Productos</h5>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered" id="tablaProductos">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 30%">Producto</th>
                                            <th style="width: 15%">Cantidad</th>
                                            <th style="width: 15%">Unidad</th>
                                            <th style="width: 15%">Precio Unit.</th>
                                            <th style="width: 15%">Subtotal</th>
                                            <th style="width: 5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="listaProductos">
                                        <!-- Filas dinámicas -->
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-outline-success mb-4" onclick="agregarFila()">
                                + Agregar Producto
                            </button>

                            <!-- Sección Totales -->
                            <div class="row justify-content-end">
                                <div class="col-md-5">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Subtotal:</strong></td>
                                            <td class="text-end" id="lblSubtotal">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td><strong>IVA (<span id="lblPorcentajeIva">12</span>%):</strong></td>
                                            <td class="text-end" id="lblIva">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Propina:</strong>
                                                <input type="number" class="form-control form-control-sm d-inline-block w-50 ms-2" name="propina" id="propina" value="0.00" step="0.01" min="0" oninput="calcularTotales()">
                                            </td>
                                            <td class="text-end align-middle" id="lblPropina">$ 0.00</td>
                                        </tr>
                                        <tr class="table-active fs-5">
                                            <td><strong>TOTAL:</strong></td>
                                            <td class="text-end fw-bold" id="lblTotal">$ 0.00</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">Guardar Factura</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Función para agregar fila de producto
        function agregarFila() {
            const tbody = document.getElementById('listaProductos');
            const tr = document.createElement('tr');
            tr.className = 'product-row';
            tr.innerHTML = `
                <td><input type="text" class="form-control form-control-sm item-nombre" placeholder="Nombre del producto" required></td>
                <td><input type="number" class="form-control form-control-sm item-cantidad" value="1" step="0.01" min="0.01" required oninput="calcularFila(this)"></td>
                <td>
                    <select class="form-select form-select-sm item-unidad">
                        <option value="unidades">Unidades</option>
                        <option value="cajas">Cajas</option>
                        <option value="litros">Litros</option>
                        <option value="kg">Kg</option>
                        <option value="horas">Horas</option>
                    </select>
                </td>
                <td><input type="number" class="form-control form-control-sm item-precio" value="0.00" step="0.01" min="0.01" required oninput="calcularFila(this)"></td>
                <td class="text-end align-middle item-subtotal">$ 0.00</td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarFila(this)">×</button>
                </td>
            `;
            tbody.appendChild(tr);
        }

        // Eliminar fila
        function eliminarFila(btn) {
            const row = btn.closest('tr');
            row.remove();
            calcularTotales();
        }

        // Calcular subtotal de una fila
        function calcularFila(input) {
            const row = input.closest('tr');
            const cantidad = parseFloat(row.querySelector('.item-cantidad').value) || 0;
            const precio = parseFloat(row.querySelector('.item-precio').value) || 0;
            const subtotal = cantidad * precio;
            
            row.querySelector('.item-subtotal').textContent = '$ ' + subtotal.toFixed(2);
            calcularTotales();
        }

        // Calcular totales generales
        function calcularTotales() {
            let subtotalGeneral = 0;
            document.querySelectorAll('#listaProductos tr').forEach(row => {
                const cantidad = parseFloat(row.querySelector('.item-cantidad').value) || 0;
                const precio = parseFloat(row.querySelector('.item-precio').value) || 0;
                subtotalGeneral += cantidad * precio;
            });

            const porcentajeIva = parseFloat(document.getElementById('porcentaje_iva').value) || 0;
            const propina = parseFloat(document.getElementById('propina').value) || 0;

            const montoIva = subtotalGeneral * (porcentajeIva / 100);
            const total = subtotalGeneral + montoIva + propina;

            // Actualizar DOM
            document.getElementById('lblSubtotal').textContent = '$ ' + subtotalGeneral.toFixed(2);
            document.getElementById('lblPorcentajeIva').textContent = porcentajeIva;
            document.getElementById('lblIva').textContent = '$ ' + montoIva.toFixed(2);
            document.getElementById('lblPropina').textContent = '$ ' + propina.toFixed(2);
            document.getElementById('lblTotal').textContent = '$ ' + total.toFixed(2);
        }

        // Manejar envío del formulario
        document.getElementById('invoiceForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Recopilar items
            const items = [];
            document.querySelectorAll('#listaProductos tr').forEach(row => {
                items.push({
                    producto: row.querySelector('.item-nombre').value,
                    cantidad: row.querySelector('.item-cantidad').value,
                    unidad: row.querySelector('.item-unidad').value,
                    precio: row.querySelector('.item-precio').value
                });
            });

            if(items.length === 0) {
                alert('Debe agregar al menos un producto.');
                return;
            }

            const formData = new FormData(this);
            formData.append('items_json', JSON.stringify(items));

            try {
                const response = await fetch('guardar_factura.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Factura guardada correctamente!');
                    window.location.href = 'index.php'; // Volver al inicio
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al procesar la solicitud.');
            }
        });

        // Iniciar con una fila vacía
        window.addEventListener('DOMContentLoaded', agregarFila);
    </script>
</body>
</html>
