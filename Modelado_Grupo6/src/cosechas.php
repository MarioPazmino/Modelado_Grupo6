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
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Exception\Exception;

// Conexión a MongoDB
try {
    $mongoClient = new Client("mongodb://grupo6:grupo6@localhost:27017");
    $db = $mongoClient->selectDatabase("grupo6_agrohub");
    $sembríosCollection = $db->selectCollection("sembríos"); // Colección de sembríos en MongoDB
    $cosechasCollection = $db->selectCollection("cosechas"); // Colección de cosechas en MongoDB
    
} catch (Exception $e) {
    die("Error de conexión a MongoDB: " . $e->getMessage());
}

// Si se ha enviado el formulario, procesar los datos de la cosecha
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $id = $_POST['cosecha_id'];
    $productoNombre = $_POST['producto_nombre'];
    $fechaCosecha = new UTCDateTime(strtotime($_POST['fecha_cosecha']) * 1000); // Convertir a UTCDateTime
    $cantidad = (int)$_POST['cantidad'];
    $unidad = $_POST['unidad'];
    $descripcion = $_POST['descripcion'];
    $calidad = $_POST['calidad'];

    // Crear el array de detalles de cosecha
    $detallesCosecha = [
        'descripcion' => $descripcion,
        'calidad' => $calidad
    ];

    // Preparar datos para MongoDB
    $datosCosecha = [
        'producto_nombre' => $productoNombre,
        'fecha_cosecha' => $fechaCosecha,
        'cantidad' => $cantidad,
        'unidad' => $unidad,
        'detalles_cosecha' => [$detallesCosecha] // Guardar como un array de objetos
    ];

    try {
        if (empty($id)) {
            // Insertar nueva cosecha
            $insertResult = $cosechasCollection->insertOne($datosCosecha);
            $mensaje = '<p style="color: green;">¡La cosecha se ha guardado correctamente!</p>';
        } else {
            // Actualizar cosecha existente
            $updateResult = $cosechasCollection->updateOne(
                ['_id' => new ObjectID($id)],
                ['$set' => $datosCosecha]
            );

            // Verificar si se realizó la actualización correctamente
            if ($updateResult->getModifiedCount() === 1) {
                $mensaje = '<p style="color: green;">¡La cosecha se ha actualizado correctamente!</p>';
            } else {
                $mensaje = '<p style="color: red;">No se pudo actualizar la cosecha.</p>';
            }
        }
    } catch (Exception $e) {
        $mensaje = '<p style="color: red;">Error al procesar la cosecha: ' . $e->getMessage() . '</p>';
    }
}

// Obtener todos los sembríos para mostrar en el formulario
$sembríosCursor = $sembríosCollection->find();
$sembríos = iterator_to_array($sembríosCursor);

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
                <div class="sidebar-brand-text mx-3">Agro HUB  <sup></sup></div>
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

                    

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                       
                        
                        
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
                                <a class="dropdown-item" href="user.php">
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
        <h1 class="h3 mb-0 text-gray-800">Cosechas</h1>
        
    </div>

    <div class="row">
    <div class="col-lg-3">
    <div class="card position-relative">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Agregar/Editar Cosecha</h6>
        </div>
        <div class="card-body">
            <div class="form-container">
            <form id="cosechaForm" action="cosechas.php" method="POST">
    <input type="hidden" id="cosecha_id" name="cosecha_id">
    <div class="form-group">
    <label for="producto_nombre">Producto de Cosecha:</label>
    <select class="form-control" id="producto_nombre" name="producto_nombre" required>
    <option value="">Selecciona un producto...</option>
<?php foreach ($sembríos as $sembrío): ?>
    <option value="<?php echo htmlspecialchars($sembrío['producto_nombre'], ENT_QUOTES, 'UTF-8'); ?>">
        <?php echo htmlspecialchars($sembrío['producto_nombre'], ENT_QUOTES, 'UTF-8'); ?>
    </option>
<?php endforeach; ?>

    </select>
</div>

    <div class="form-group">
        <label for="fecha_cosecha">Fecha de Cosecha:</label>
        <input type="date" class="form-control" id="fecha_cosecha" name="fecha_cosecha" required>
    </div>
    <div class="form-group">
        <label for="cantidad">Cantidad:</label>
        <input type="number" class="form-control" id="cantidad" name="cantidad" required min="1">
    </div>
    <div class="form-group">
        <label for="unidad">Unidad:</label>
        <select class="form-control" id="unidad" name="unidad" required>
            <option value="">Selecciona una unidad...</option>
            <option value="lb">Libras (lb)</option>
    <option value="st">Stones (st)</option>
    <option value="cwt">Quintales (cwt)</option>
    <option value="long-ton">Toneladas largas (long ton)</option>

        </select>
    </div>
    <div class="form-group">
        <label for="descripcion">Descripción:</label>
        <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
    </div>
    <div class="form-group">
        <label for="calidad">Calidad:</label>
        <select class="form-control" id="calidad" name="calidad" required>
            <option value="">Selecciona una calidad...</option>
            <option value="alta">Alta</option>
            <option value="media">Media</option>
            <option value="baja">Baja</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary" id="guardarCosechaBtn">Guardar</button>
    <button type="button" class="btn btn-secondary ml-2" id="cancelarCosechaBtn" style="display: none;">Cancelar</button>
</form>

            </div>
        </div>
    </div>
</div>


<div class="col-lg-9">
    <div class="card position-relative">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Cosechas Registradas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="myTable" class="display table table-striped">
                    <thead>
                        <tr>
                            <th>Producto Cosechado</th>
                            <th>Fecha de Cosecha</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cosechas as $cosecha): ?>
                            <tr>
                                
                                <td><?php echo $cosecha['producto_nombre']; ?></td>
                                <td><?php echo $cosecha['fecha_cosecha']->toDateTime()->format('Y-m-d'); ?></td>
                                <td><?php echo $cosecha['cantidad']; ?></td>
                                <td><?php echo $cosecha['unidad']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info viewDetailsBtn" data-toggle="modal" data-target="#detailsModal" data-descripcion="<?php echo htmlspecialchars($cosecha['detalles_cosecha'][0]['descripcion'], ENT_QUOTES, 'UTF-8'); ?>" data-calidad="<?php echo htmlspecialchars($cosecha['detalles_cosecha'][0]['calidad'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary editarCosechaBtn" data-id="<?php echo $cosecha['_id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-cosecha-btn" data-id="<?php echo $cosecha['_id']; ?>">
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




<!-- Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Detalles de la Cosecha</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Descripción:</strong> <span id="descripcionText"></span></p>
                <p><strong>Calidad:</strong> <span id="calidadText"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Evento que se dispara cuando se muestra el modal
        $('#detailsModal').on('show.bs.modal', function(event) {
            // Botón que activó el modal
            var button = $(event.relatedTarget);

            // Extraer los datos de los atributos data-* del botón
            var descripcion = button.data('descripcion');
            var calidad = button.data('calidad');

            // Actualizar el contenido del modal con los datos obtenidos
            var modal = $(this);
            modal.find('#descripcionText').text(descripcion);
            modal.find('#calidadText').text(calidad);
        });
    });
</script>




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

<script>
$(document).ready(function() {
    // Función para cargar datos de la cosecha en el formulario de edición
    $('.editarCosechaBtn').on('click', function() {
        const cosechaId = $(this).data('id');

        // Realizar una solicitud AJAX para obtener los detalles de la cosecha
        $.ajax({
            url: 'obtener_cosecha_ajax.php', // Ajustar la URL según tu estructura de archivos
            type: 'POST',
            data: { id: cosechaId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Llenar el formulario con los datos de la cosecha
                    $('#cosecha_id').val(response.cosecha._id);
                    $('#producto_nombre').val(response.cosecha.producto_nombre).change();
                    $('#fecha_cosecha').val(response.cosecha.fecha_cosecha);
                    $('#cantidad').val(response.cosecha.cantidad);
                    $('#unidad').val(response.cosecha.unidad).change();
                    $('#descripcion').val(response.cosecha.detalles_cosecha.descripcion);
                    $('#calidad').val(response.cosecha.detalles_cosecha.calidad);
                    
                    // Cambiar el texto del botón de guardar a actualizar
                    $('#guardarCosechaBtn').text('Actualizar');
                    $('#cancelarCosechaBtn').show();
                } else {
                    alert('Hubo un error al cargar los datos de la cosecha.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error en la solicitud AJAX:', textStatus, errorThrown);
                alert('Error en la solicitud AJAX. Consulta la consola para más detalles.');
            }
        });
    });

    // Función para cancelar la edición
    $('#cancelarCosechaBtn').on('click', function() {
        // Limpiar el formulario y restaurar el estado inicial
        $('#cosecha_id').val('');
        $('#producto_nombre').val('').change();
        $('#fecha_cosecha').val('');
        $('#cantidad').val('');
        $('#unidad').val('').change();
        $('#descripcion').val('');
        $('#calidad').val('');
        
        // Cambiar el texto del botón de guardar a guardar
        $('#guardarCosechaBtn').text('Guardar');
        $('#cancelarCosechaBtn').hide();
    });
});
</script>

<script>
$(document).ready(function() {
    $('.delete-cosecha-btn').on('click', function() {
        const id = $(this).data('id');
        const row = $(this).closest('tr');

        if (confirm('¿Estás seguro de que deseas eliminar esta cosecha?')) {
            $.ajax({
                url: 'eliminar_cosecha_ajax.php', // Ajusta la URL según tu estructura de archivos
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        row.remove();
                        alert('¡La cosecha se ha eliminado correctamente!');
                    } else {
                        alert('Hubo un error al eliminar la cosecha: ' + response.message);
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
<script>
    $(document).ready(function() {
        // Inicializar DataTables en la tabla #cosechasTable
        let table = $('#myTable').DataTable({
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


</body>

</html>