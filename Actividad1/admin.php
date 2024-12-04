<?php
// Conexión a la base de datos
$host = 'localhost';
$user = 'root'; 
$pass = ''; 
$dbname = 'cursos_db'; 

// Crear la conexión
$conn = new mysqli($host, $user, $pass, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}



// Crear un curso
if (isset($_POST['crear'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $duracion = $_POST['duracion'];

    $sql = "INSERT INTO cursos (nombre, descripcion, duracion) VALUES ('$nombre', '$descripcion', '$duracion')";
    if ($conn->query($sql) === TRUE) {
        echo "Curso creado exitosamente";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Editar un curso
if (isset($_POST['editar'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $duracion = $conn->real_escape_string($_POST['duracion']);

    $sql = "UPDATE cursos SET nombre='$nombre', descripcion='$descripcion', duracion='$duracion' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        header("Location: admin.php?mensaje=Curso editado exitosamente");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Eliminar un curso
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM cursos WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "Curso eliminado exitosamente";
    } else {
        echo "Error: " . $conn->error;
    }
}
// Cargar datos de curso para edición
$curso = null;
if (isset($_GET['editar'])) {
    $id = $conn->real_escape_string($_GET['editar']);
    $sql = "SELECT * FROM cursos WHERE id=$id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $curso = $result->fetch_assoc();
    }
}
// Mostrar cursos
$sql = "SELECT * FROM cursos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cursos</title>
    <style>
    body {
    color: darkslateblue;
}
/* Estilo para H1 */
h1 {
    color: darkgreen !important;
}

/* Estilo para H2 */
h2 {
    color: white;
    background-color: forestgreen;
    display: inline-block;
    padding: 5px; 
}
h3 {
    color: darkgreen;
    font-size: 25px;
}
        
        .button {
            cursor: pointer;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #0056b3;
        }        
       
        .hidden {
        display: none;
        }

        
        }
        .table-container {
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
        }
        
       ------------------------------------------
        
    </style>
    
    <script>
        function toggleCursos() {
            const cursosDiv = document.getElementById('cursos-list');
            if (cursosDiv.classList.contains('hidden')) {
                cursosDiv.classList.remove('hidden');
            } else {
                cursosDiv.classList.add('hidden');
            }
        }               
        
        function toggleInscripciones() {
    const table = document.getElementById('inscripciones-table');
    if (table.classList.contains('hidden')) {
        table.classList.remove('hidden'); // Muestra la tabla
    } else {
        table.classList.add('hidden'); // Oculta la tabla
    }
}
        
    </script>
</head>
<body>
    
    <h1>Gestión de Cursos</h1> 

    <!-- Formulario para crear o editar un curso -->
    <form method="POST" action="admin.php" class="formulario">
    <input type="hidden" name="id" value="<?php echo isset($curso) ? $curso['id'] : ''; ?>">
    
    <label for="nombre" class="label">Nombre del curso:</label>
    <input type="text" name="nombre" required class="input" value="<?php echo isset($curso) ? $curso['nombre'] : ''; ?>"><br>
    
    <label for="descripcion" class="label">Descripción:</label>
    <textarea name="descripcion" required class="textarea"><?php echo isset($curso) ? $curso['descripcion'] : ''; ?></textarea><br>
    
    <label for="duracion" class="label">Duración:</label>
    <input type="text" name="duracion" required class="input" value="<?php echo isset($curso) ? $curso['duracion'] : ''; ?>"><br>
    
    <?php if (isset($curso)): ?>
        <button type="submit" name="editar" class="btn">Editar Curso</button>
    <?php else: ?>
        <button type="submit" name="crear" class="btn">Crear Curso</button>
    <?php endif; ?>
</form>

<h2 class="subtitulo">Cursos Disponibles</h2> <!-- Subtítulo -->
   

<ul class="lista-cursos">
    <?php while($row = $result->fetch_assoc()): ?>
        <li class="curso-item">
            <h3 class="curso-nombre"><?php echo $row['nombre']; ?></h3>
            <p class="descripcion-curso"><?php echo $row['descripcion']; ?></p>
            <p><strong>Duración:</strong> <?php echo $row['duracion']; ?></p>
            <a href="admin.php?editar=<?php echo $row['id']; ?>" class="btn-editar">Editar</a>
            <a href="admin.php?eliminar=<?php echo $row['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar este curso?')">Eliminar</a>
        </li>
    <?php endwhile; ?>
</ul>

    
    ____________________________________________________
     <h2 class="subtitulo button" onclick="toggleInscripciones()" role="button">Mostrar inscripciones</h2>

<!-- Tabla de inscripciones -->
<div id="inscripciones-table" class="table-container hidden">
    <?php
    // Obtener inscripciones
    $sql = "SELECT inscripciones.id, cursos.nombre AS curso, inscripciones.nombre, 
                   inscripciones.celular, inscripciones.correo 
            FROM inscripciones 
            JOIN cursos ON inscripciones.curso_id = cursos.id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0):
    ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Curso</th>
                <th>Nombre</th>
                <th>Celular</th>
                <th>Correo</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['curso']; ?></td>
                <td><?php echo $row['nombre']; ?></td>
                <td><?php echo $row['celular']; ?></td>
                <td><?php echo $row['correo']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php
    else: ?>
    <p>No hay inscripciones registradas.</p>
    <?php endif; ?>
</div>

    ______________________________________________________
        
    
</body>
</html>



<?php
// Cerrar la conexión
$conn->close();
?>
