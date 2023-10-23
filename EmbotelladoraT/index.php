<!DOCTYPE html>
<html>

<head>
    <title>Historial de Registros</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Cambiar el color de fondo del encabezado */
        h1 {
            background-color: #336699;
            color: white;
            padding: 10px;
            text-align: center; /* Centrar el texto del encabezado */
            margin-top: 0; /* Eliminar el margen superior para que se ajuste a la parte superior de la pantalla */
        }

        /* Cambiar el color de fondo de la tabla */
        table {
            background-color: #f2f2f2;
            width: 100%; /* Hacer que la tabla ocupe todo el ancho disponible */
        }

         /* Cambiar el color del encabezado de la tabla */
        th {
            background-color: green;
            color: white;
        }

        /* Cambiar el color de fondo de las celdas de la tabla */
        td {
            background-color: #fff;
        }

        /* Cambiar el color del botón "Llenar Botellón" */
        .btn-primary {
            background-color: #0066cc;
            border-color: #0066cc;
        }

        /* Cambiar el color del botón "Generar Reporte PDF" */
        #generarPDF {
            background-color: #cc0000;
            border-color: #cc0000;
        }

        /* Cambiar el color del texto del botón "Generar Reporte PDF" */
        #generarPDF {
            color: white;
        }

        /* Ajustar el tamaño del contenedor de la tabla para que se estire por completo en la pantalla */
        .table-container {
            height: 100vh; /* Tamaño completo de la ventana */
            display: flex;
            flex-direction: column;
        }

        /* Hacer que la tabla se expanda y ocupe todo el espacio disponible */
        .table-responsive {
            flex: 1;
            overflow-y: auto;
        }
    </style>
</head>
    <div class="container">

        <h1 class="mt-5">EMBOTELLADORA THOMSOM</h1>

        <div class="table-responsive" style="max-height: 500px;">
            <table class="table table-bordered mt-4">
                <thead class="thead-dark sticky-top">
                    <tr>
                        <th>Nombre Cliente</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Cantidad de Botellas</th>
                        <th>Ubicación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Datos de conexión a la base de datos
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "botellones";

                    // Conexión a la base de datos
                    $conn = new mysqli($servername, $username, $password, $dbname);

                    // Verificar la conexión
                    if ($conn->connect_error) {
                        die("Error de conexión a la base de datos: " . $conn->connect_error);
                    }

                    
                    // Llenar el botellón
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['llenarBotellon'])) {
                        // Verificar si ya se ha enviado el formulario antes
                        if (!isset($_SESSION['formulario_enviado'])) {
                            $_SESSION['formulario_enviado'] = true;

                            date_default_timezone_set('America/Caracas');
                            $fecha = date('Y-m-d');
                            $hora = date('h:i:s A');
                            $cantidadBotellas = $_POST['cantidadBotellas'];

                            // Validar y escapar el valor de $cantidadBotellas
                            $cantidadBotellas = intval($cantidadBotellas);
                            $cantidadBotellas = $conn->real_escape_string($cantidadBotellas);

                            // Obtén el valor del nombre del cliente del formulario
                            $nombreCliente = $_POST['nombreCliente'];

                            // Validar y escapar el valor del nombre del cliente
                            $nombreCliente = $conn->real_escape_string($nombreCliente);

                            // Obtén el valor de la ubicación del cliente del formulario
                            $ubicacionCliente = $_POST['ubicacionCliente'];

                            // Validar y escapar el valor de la ubicación del cliente
                            $ubicacionCliente = $conn->real_escape_string($ubicacionCliente);

                            $insertSql = "INSERT INTO botellones (nombre_cliente, fecha_llenado, hora_llenado, cantidad, ubicacion) VALUES ('$nombreCliente', '$fecha', '$hora', $cantidadBotellas, '$ubicacionCliente')";
                            if ($conn->query($insertSql) === TRUE) {
                                echo "<script>alert('Botellón llenado correctamente');</script>";

                                // Redireccionar a la misma página para evitar la inserción duplicada al recargar
                                header("Location: " . $_SERVER['PHP_SELF']);
                                exit;
                            } else {
                                echo "Error al llenar el botellón: " . $conn->error;
                            }
                        }
                    }

                    // Obtener los registros del historial (incluyendo el registro recién insertado)
                    $sql = "SELECT nombre_cliente, fecha_llenado, hora_llenado, cantidad, ubicacion FROM botellones";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['nombre_cliente'] . "</td>";
                            echo "<td>" . $row['fecha_llenado'] . "</td>";
                            echo "<td>" . $row['hora_llenado'] . "</td>";
                            echo "<td>" . $row['cantidad'] . "</td>";
                            echo "<td>" . $row['ubicacion'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No hay registros en el historial</td></tr>";
                    }

                    ?>
                </tbody>
            </table>
        </div>

        <form method="post">
            <div class="form-group">
                <label for="nombreCliente">Nombre del Cliente:</label>
                <input type="text" class="form-control" id="nombreCliente" name="nombreCliente">
            </div>
            <div class="form-group">
                <label for="ubicacionCliente">Ubicación del Cliente:</label>
                <input type="text" class="form-control" id="ubicacionCliente" name="ubicacionCliente">
            </div>
            <div class="form-group">
                <label for="cantidadBotellas">Cantidad de Botellas:</label>
                <input type="number" class="form-control" id="cantidadBotellas" name="cantidadBotellas">
            </div>
            <button type="submit" name="llenarBotellon" class="btn btn-primary">Llenar Botellón</button>
            <button type="submit" id="generarPDF" name="generarPDF" formtarget="_blank" formaction="generar_pdf.php"
                class="btn btn-primary">Descargar Reporte</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
 <script>
    $(document).ready(function() {
  $('#generarPDF').click(function() {
    $.ajax({
      url: 'generar_pdf.php', // Ruta al archivo PHP que genera el PDF
      type: 'GET',
      success: function(response) {
        // La solicitud AJAX se completó correctamente
        alert('Reporte generado correctamente');
      },
      error: function(xhr, status, error) {
        // Ocurrió un error al realizar la solicitud AJAX
        alert('Error al generar el PDF: ' + error);
      }
    });
  });
});
 </script>
</html>