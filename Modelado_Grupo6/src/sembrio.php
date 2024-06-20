<?php
session_start();

// Verificar si el usuario está autenticado y tiene el rol adecuado
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'usuario')) {
    header("Location: index.php");
    exit();
}

require '../vendor/autoload.php'; // Incluir el archivo autoload.php de MongoDB

use MongoDB\Client;
use MongoDB\BSON\ObjectId;
use MongoDB\Exception\Exception;

// Conexión a MongoDB
$mongoClient = new Client("mongodb://grupo6:grupo6@localhost:27017");
$db = $mongoClient->selectDatabase("grupo6_agrohub");
$sembríosCollection = $db->sembríos; // Colección de sembríos en MongoDB

// Si se ha enviado el formulario, procesar los datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $producto_nombre = $_POST['producto_nombre'];
    $fecha_siembra = new MongoDB\BSON\UTCDateTime(strtotime($_POST['fecha_siembra']) * 1000); // Convertir a UTCDateTime
    $ubicacion = $_POST['ubicacion'];
    $area = (int)$_POST['area'];

    // Preparar datos para insertar en MongoDB
    $nuevo_sembrío = [
        'usuario_id' => new ObjectId($_SESSION['usuario_id']),
        'producto_nombre' => $producto_nombre,
        'fecha_siembra' => $fecha_siembra,
        'ubicacion' => $ubicacion,
        'area' => $area,
        'tareas' => []  // Puedes agregar las tareas si es necesario
    ];

    try {
        // Insertar el nuevo sembrío en la colección de sembríos
        $insertOneResult = $sembríosCollection->insertOne($nuevo_sembrío);
        
        // Mostrar mensaje de éxito
        if ($insertOneResult->getInsertedCount() === 1) {
            echo '<p style="color: green;">¡El sembrío se ha guardado correctamente!</p>';
        } else {
            echo '<p style="color: red;">Hubo un error al guardar el sembrío.</p>';
        }
    } catch (Exception $e) {
        echo '<p style="color: red;">Error: ' . $e->getMessage() . '</p>';
    }
}

// Obtener sembríos del usuario actual
$usuario_id = new ObjectId($_SESSION['usuario_id']);
$sembríos = $sembríosCollection->find(['usuario_id' => $usuario_id]);

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

                    <!-- Topbar Search -->
                   

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
        <h1 class="h3 mb-0 text-gray-800">Sembrio</h1>
        
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card position-relative">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Agregar/Editar Sembrío</h6>
                </div>
                <div class="card-body">
                    <div class="form-container">
                        <form id="sembríoForm" action="guardar_sembrío.php" method="POST">
                            <input type="hidden" id="sembrío_id" name="sembrío_id">
                            <div class="form-group">
                                <label for="producto_nombre">Producto:</label>
                                <input type="text" class="form-control" id="producto_nombre" name="producto_nombre" required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_siembra">Fecha de Siembra:</label>
                                <input type="date" class="form-control" id="fecha_siembra" name="fecha_siembra" required>
                            </div>
                            <div class="form-group">
                                <label for="ubicacion">Ubicación:</label>
                                <input type="text" class="form-control" id="ubicacion" name="ubicacion" required>
                            </div>
                            <div class="form-group">
                                <label for="area">Área (ha):</label>
                                <input type="number" class="form-control" id="area" name="area" min="1" required>
                            </div>
                            <button type="submit" class="btn btn-primary" id="guardarBtn">Guardar</button>
                            <button type="button" class="btn btn-secondary ml-2" id="cancelarBtn" style="display: none;">Cancelar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card position-relative">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sembríos Registrados</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="myTable" class="display table table-striped">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Fecha de Siembra</th>
                                    <th>Ubicación</th>
                                    <th>Área (ha)</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sembríos as $sembrío): ?>
                                    <tr>
                                        <td><?php echo $sembrío['producto_nombre']; ?></td>
                                        <td><?php echo $sembrío['fecha_siembra']->toDateTime()->format('Y-m-d'); ?></td>
                                        <td><?php echo $sembrío['ubicacion']; ?></td>
                                        <td><?php echo $sembrío['area']; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary editar-btn" data-id="<?php echo $sembrío['_id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $sembrío['_id']; ?>">
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

        <div class="col-lg-12 mt-4">
    <div class="card position-relative">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tareas del Sembrío</h6>
        </div>
        <div class="card-body">
            <form id="formNuevaTarea">
                <input type="hidden" id="sembrío_id" name="sembrío_id">
                <input type="hidden" id="tarea_id" name="tarea_id"> <!-- Campo oculto para almacenar el id de la tarea -->
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputDescripcion">Descripción:</label>
                        <input type="text" class="form-control" id="inputDescripcion" name="descripcion" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="selectEstado">Estado:</label>
                        <select class="form-control" id="selectEstado" name="estado" required>
                            <option value="Pendiente">Pendiente</option>
                            <option value="En Curso">En Curso</option>
                            <option value="Completada">Completada</option>
                        </select>
                    </div>
                </div>
                <button type="submit" id="btnAgregarEditar" class="btn btn-primary">Agregar Tarea</button>
                <button type="button" id="btnCancelarEdicion" class="btn btn-secondary ml-2" style="display: none;">Cancelar</button>
            </form>

            <hr>

            <div class="table-responsive">
                <table id="tablaTareas" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se generarán dinámicamente las filas de la tabla con JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
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
$(document).ready(function() {
    // Mostrar formulario de edición al hacer clic en el botón editar del sembrío
    $('.editar-btn').on('click', function() {
        const id = $(this).data('id');
        const row = $(this).closest('tr');
        const producto = row.find('td:eq(0)').text();
        const fechaSiembra = row.find('td:eq(1)').text();
        const ubicacion = row.find('td:eq(2)').text();
        const area = row.find('td:eq(3)').text();

        // Llenar el formulario de edición con los datos actuales del sembrío
        $('#sembrío_id').val(id);
        $('#producto_nombre').val(producto);
        $('#fecha_siembra').val(fechaSiembra);
        $('#ubicacion').val(ubicacion);
        $('#area').val(area);

        // Cambiar el texto y mostrar el botón de actualizar
        $('#guardarBtn').text('Actualizar');
        $('#cancelarBtn').show();

        // Cargar las tareas del sembrío correspondiente
        cargarTareas(id);
    });

    // Cancelar la edición del sembrío y limpiar el formulario
    $('#cancelarBtn').on('click', function() {
        $('#sembrío_id').val('');
        $('#producto_nombre').val('');
        $('#fecha_siembra').val('');
        $('#ubicacion').val('');
        $('#area').val('');

        $('#guardarBtn').text('Guardar');
        $(this).hide();

        $('#tablaTareas tbody').empty(); // Limpiar la tabla de tareas al cancelar la edición
    });

    // Envío del formulario de edición del sembrío (Guardar o Actualizar)
    $('#sembríoForm').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serialize();

        $.ajax({
            url: 'guardar_sembrío.php', // Ajusta la URL según tu estructura de archivos
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('¡El sembrío se ha guardado correctamente!');
                    location.reload(); // Recargar la página para actualizar la tabla
                } else {
                    alert('Hubo un error al guardar el sembrío: ' + response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error en la solicitud AJAX:', textStatus, errorThrown);
                alert('Error en la solicitud AJAX. Consulta la consola para más detalles.');
            }
        });
    });

    function cargarTareas(sembrío_id) {
        $.ajax({
            url: 'cargar_tareas.php',
            type: 'POST',
            dataType: 'json',
            data: { sembrío_id: sembrío_id },
            success: function(response) {
                if (response.success) {
                    mostrarTareas(response.tareas);
                } else {
                    console.log('Error al cargar las tareas:', response.message);
                    alert('Error al cargar las tareas: ' + response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error en la solicitud AJAX:', textStatus, errorThrown);
                alert('Error en la solicitud AJAX. Consulta la consola para más detalles.');
            }
        });
    }

    function mostrarTareas(tareas) {
        var tbody = $('#tablaTareas tbody');
        tbody.empty(); // Limpiar el contenido actual de la tabla

        tareas.forEach(function(tarea) {
            var row = `<tr>
                            <td>${tarea.descripcion}</td>
                            <td>${tarea.estado}</td>
                            <td>
                                <button class="btn btn-sm btn-primary editar-tarea" data-id="${tarea._id}">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                            </td>
                        </tr>`;
            tbody.append(row);
        });
    }

    $(document).on('click', '.editar-tarea', function() {
        const id = $(this).data('id');
        const row = $(this).closest('tr');
        const descripcion = row.find('td:eq(0)').text();
        const estado = row.find('td:eq(1)').text();

        $('#tarea_id').val(id); // Campo oculto para almacenar el id de la tarea
        $('#inputDescripcion').val(descripcion);
        $('#selectEstado').val(estado);

        $('#btnAgregarEditar').text('Actualizar');
        $('#btnCancelarEdicion').show();
    });

    $('#formNuevaTarea').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serialize();
        const sembrío_id = $('#sembrío_id').val(); // Obtener el ID del sembrío actual
        const tarea_id = $('#tarea_id').val(); // Obtener el ID de la tarea actual

        $.ajax({
            url: 'agregar_actualizar_tarea.php',
            type: 'POST',
            data: formData + '&sembrío_id=' + sembrío_id,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('¡Tarea actualizada correctamente!');
                    cargarTareas(sembrío_id); // Recargar la lista de tareas después de actualizar/agregar
                    $('#formNuevaTarea')[0].reset(); // Limpiar el formulario de nueva tarea
                    $('#btnAgregarEditar').text('Agregar Tarea'); // Restaurar el texto del botón
                    $('#btnCancelarEdicion').hide(); // Ocultar el botón de cancelar edición
                    $('#tarea_id').val(''); // Limpiar el campo oculto del id de la tarea
                } else {
                    alert('Error al actualizar tarea: ' + response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error en la solicitud AJAX:', textStatus, errorThrown);
                alert('Error en la solicitud AJAX. Consulta la consola para más detalles.');
            }
        });
    });

    // Cargar las tareas si hay un ID de sembrío inicial
    const initialSembríoId = $('#sembrío_id').val();
    if (initialSembríoId) {
        cargarTareas(initialSembríoId);
    }
});
</script>


<script>
$(document).ready(function() {
    $('.delete-btn').on('click', function() {
        const id = $(this).data('id');
        const row = $(this).closest('tr');

        if (confirm('¿Estás seguro de que deseas eliminar este sembrío?')) {
            $.ajax({
                url: 'eliminar_sembrío_ajax.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        row.remove();
                        alert('¡El sembrío se ha eliminado correctamente!');
                    } else {
                        alert('Hubo un error al eliminar el sembrío: ' + response.message);
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