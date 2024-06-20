<?php
session_start();

// Verificar si el usuario está autenticado y tiene el rol adecuado
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'usuario')) {
    header("Location: index.php");
    exit();
}

// Incluir el archivo autoload.php de MongoDB
require '../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\Exception\Exception;

// Conexión a MongoDB

    $mongoClient = new Client("mongodb://grupo6:grupo6@localhost:27017");
    $db = $mongoClient->selectDatabase("grupo6_agrohub");
    $ventasCollection = $db->selectCollection("ventas"); // Colección de ventas en MongoDB
    $cosechasCollection = $db->selectCollection("cosechas"); // Colección de cosechas en MongoDB


// Obtener todas las ventas para mostrar en la tabla
$ventasCursor = $ventasCollection->find();
$ventas = iterator_to_array($ventasCursor);
// Obtener todas las cosechas para mostrar en la tabla
$cosechasCursor = $cosechasCollection->find();
$cosechas = iterator_to_array($cosechasCursor);


?>
 

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
        <link rel="stylesheet" href="styles.css"> <!-- Ajusta la ruta según sea necesario -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
</head>
    <!-- Custom styles for this template-->
    <link href="css/user/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="user.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Agro HUB <sup></sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="user.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Interface
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-tractor"></i>
                    <span>Agrícola</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Mi granja:</h6>
                        <a class="collapse-item" href="sembrio.php">Sembríos</a>
                        <a class="collapse-item" href="cosechas.php">Cosechas</a>
                    </div>
                </div>
            </li>

           
            <!-- Divider -->
            <hr class="sidebar-divider">

   
                        <!-- Nav Item - Charts -->
                        <li class="nav-item">
                <a class="nav-link" href="ventas.php">
                    <i class="fas fa-fw fa-cart-plus"></i>
                    <span>Ventas</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

          
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                           

                       

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></span>

                                <img class="img-profile rounded-circle"
                                    src="assets/images/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php">
    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
    Salir
</a>

                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

            
                





                <div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ventas</h1>
        
    </div>

    <div class="row">
    <div class="col-lg-12">
        <div class="card position-relative">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Agregar/Editar Venta</h6>
            </div>
            <div class="card-body">
                <div class="form-container">
                    <form id="ventaForm" action="guardar_venta.php" method="POST" onsubmit="return validarFormulario()">
                        <input type="hidden" id="venta_id" name="venta_id">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="fecha_venta">Fecha de Venta:</label>
                                <input type="date" class="form-control" id="fecha_venta" name="fecha_venta" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="cantidad_vendida">Cantidad Vendida:</label>
                                <input type="number" class="form-control" id="cantidad_vendida" name="cantidad_vendida" min="1" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="precio_total">Precio Total:</label>
                                <input type="number" class="form-control" id="precio_total" name="precio_total" step="0.01" required>
                                <small id="precioTotalHelp" class="form-text text-danger d-none">El precio total debe ser mayor o igual a 0.01</small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="comprador_nombre">Nombre del Comprador:</label>
                                <input type="text" class="form-control" id="comprador_nombre" name="comprador[nombre]" required>
                                <small id="nombreCompradorHelp" class="form-text text-danger d-none">El nombre del comprador no debe contener números.</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="comprador_contacto_tipo">Tipo de Contacto:</label>
                                <select class="form-control" id="comprador_contacto_tipo" name="comprador[contactos][tipo]" required>
                                    <option value="" disabled selected>Seleccione el tipo de contacto</option>
                                    <option value="teléfono">Teléfono</option>
                                    <option value="email">Email</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="comprador_contacto_valor">Contacto del Comprador:</label>
                                <input type="text" class="form-control" id="comprador_contacto_valor" name="comprador[contactos][valor]" required>
                                <small id="contactoCompradorHelp" class="form-text text-danger d-none">Ingrese un teléfono válido (10 dígitos) o un email válido.</small>
                            </div>
                        </div>
                     
                        
                        <hr>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="venta_tipo">Tipo de Venta:</label>
                                <select class="form-control" id="venta_tipo" name="venta_tipo" required>
                                    <option value="">Selecciona un tipo de venta...</option>
                                    <option value="cosecha">Cosecha</option>
                                    <option value="producto">Producto</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="cosecha_id_group" style="display: none;">
                            <label for="cosecha_nombre">Nombre de la Cosecha:</label>
                            <select class="form-control" id="cosecha_nombre" name="item_id">
                                <!-- Opciones dinámicas cargadas desde la base de datos -->
                                <?php foreach ($cosechas as $cosecha): ?>
                                    <option value="<?php echo htmlspecialchars($cosecha['producto_nombre'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($cosecha['producto_nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-row" id="producto_detalle_group" style="display: none;">
                            <div class="form-group col-md-4">
                                <label for="producto_nombre">Nombre del Producto:</label>
                                <input type="text" class="form-control" id="producto_nombre" name="producto_nombre">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="producto_descripcion">Descripción del Producto:</label>
                                <input type="text" class="form-control" id="producto_descripcion" name="producto_descripcion">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="producto_tipo">Tipo de Producto:</label>
                                <input type="text" class="form-control" id="producto_tipo" name="producto_tipo">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="guardarBtn">Guardar</button>
                        <button type="button" class="btn btn-secondary ml-2" id="cancelarBtn" style="display: none;">Cancelar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
     
        

        

    <div class="col-lg-12">
    <div class="card position-relative">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Ventas Registradas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="myTable" class="display table table-striped">
                    <thead>
                        <tr>
                            <th>Producto Vendido</th>
                            <th>Fecha de Venta</th>
                            <th>Cantidad</th>
                            <th>Precio Total</th>
                            <th>Comprador</th>
                            <th>Tipo de Venta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ventas as $venta): ?>
                            <tr>
                                <td><?php echo $venta['producto_detalle']['producto_nombre']; ?></td>
                                <td><?php echo $venta['fecha_venta']->toDateTime()->format('Y-m-d'); ?></td>
                                <td><?php echo $venta['cantidad_vendida']; ?></td>
                                <td><?php echo $venta['precio_total']; ?></td>
                                <td><?php echo $venta['comprador']['nombre']; ?></td>
                                <td><?php echo $venta['venta_tipo']; ?></td>
                                <td>

                                
                                    <button class="btn btn-sm btn-danger delete-venta-btn" data-id="<?php echo $venta['_id']; ?>">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


























            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; AgroHUB 2024</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="components/user/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="components/user/demo/chart-area-demo.js"></script>
    <script src="components/user/demo/chart-pie-demo.js"></script>

    <script>
        $(document).ready(function() {
            let table = new DataTable('#myTable', {
                "pageLength": 7,
                "lengthMenu": [7, 14, 21, 28],
                "language": {
                    "processing": "Procesando...",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "emptyTable": "Ningún dato disponible en esta tabla",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "infoPostFix": "",
                    "search": "Buscar:",
                    "url": "",
                    "infoThousands": ",",
                    "loadingRecords": "Cargando...",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                        "sortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                }
            });

            
        });
    </script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>

<script>
    // Función para validar el formulario antes de enviarlo
    function validarFormulario() {
        // Validar precio total
        var precioTotal = parseFloat(document.getElementById('precio_total').value);
        if (precioTotal < 0.01) {
            document.getElementById('precioTotalHelp').classList.remove('d-none');
            return false;
        } else {
            document.getElementById('precioTotalHelp').classList.add('d-none');
        }

        // Validar nombre del comprador (no debe contener números)
        var nombreComprador = document.getElementById('comprador_nombre').value;
        if (/\d/.test(nombreComprador)) {
            document.getElementById('nombreCompradorHelp').classList.remove('d-none');
            return false;
        } else {
            document.getElementById('nombreCompradorHelp').classList.add('d-none');
        }

        // Validar contacto del comprador (según el tipo seleccionado)
        var tipoContacto = document.getElementById('comprador_contacto_tipo').value;
        var contactoComprador = document.getElementById('comprador_contacto_valor').value;
        var contactoValido = false;

        if (tipoContacto === 'teléfono') {
            // Validar teléfono (exactamente 10 dígitos numéricos)
            if (/^\d{10}$/.test(contactoComprador)) {
                contactoValido = true;
            }
        } else if (tipoContacto === 'email') {
            // Validar email (formato de email básico)
            if (/^\S+@\S+\.\S+$/.test(contactoComprador)) {
                contactoValido = true;
            }
        }

        if (!contactoValido) {
            document.getElementById('contactoCompradorHelp').classList.remove('d-none');
            return false;
        } else {
            document.getElementById('contactoCompradorHelp').classList.add('d-none');
        }

        // Validar nombre del producto y tipo de producto (solo si es tipo de venta 'producto')
        var tipoVenta = document.getElementById('venta_tipo').value;
        if (tipoVenta === 'producto') {
            var nombreProducto = document.getElementById('producto_nombre').value;
            var tipoProducto = document.getElementById('producto_tipo').value;

            // Validar nombre del producto (no debe contener números)
            if (/\d/.test(nombreProducto)) {
                document.getElementById('nombreProductoHelp').classList.remove('d-none');
                return false;
            } else {
                document.getElementById('nombreProductoHelp').classList.add('d-none');
            }

            // Validar tipo de producto (no debe contener números)
            if (/\d/.test(tipoProducto)) {
                document.getElementById('tipoProductoHelp').classList.remove('d-none');
                return false;
            } else {
                document.getElementById('tipoProductoHelp').classList.add('d-none');
            }
        }

        // Si todas las validaciones pasan, enviar el formulario
        return true;
    }

    document.getElementById('venta_tipo').addEventListener('change', function() {
        var tipoVenta = this.value;
        if (tipoVenta === 'cosecha') {
            document.getElementById('cosecha_id_group').style.display = 'block';
            document.getElementById('producto_detalle_group').style.display = 'none';
        } else if (tipoVenta === 'producto') {
            document.getElementById('cosecha_id_group').style.display = 'none';
            document.getElementById('producto_detalle_group').style.display = 'block';
        } else {
            document.getElementById('cosecha_id_group').style.display = 'none';
            document.getElementById('producto_detalle_group').style.display = 'none';
        }
    });

    document.getElementById('agregarContactoBtn').addEventListener('click', function() {
        var nuevoContacto = document.createElement('input');
        nuevoContacto.type = 'text';
        nuevoContacto.className = 'form-control mt-2';
        nuevoContacto.placeholder = 'Agregar otro contacto...';
        document.getElementById('contactos_extra').appendChild(nuevoContacto);
    });
</script>




<script>
$(document).ready(function() {
    $('.delete-venta-btn').on('click', function() {
        const id = $(this).data('id');
        const row = $(this).closest('tr');

        if (confirm('¿Estás seguro de que deseas eliminar esta venta?')) {
            $.ajax({
                url: 'eliminar_venta_ajax.php', // Ajusta la URL según tu estructura de archivos
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        row.remove();
                        alert('¡La venta se ha eliminado correctamente!');
                    } else {
                        alert('Hubo un error al eliminar la venta: ' + response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Error en la solicitud AJAX:', textStatus, errorThrown);
                    alert('Error en la solicitud AJAX. Consulta la consola para más detalles.');
                }
            });
        }
    });
});
</script>

</body>

</html>