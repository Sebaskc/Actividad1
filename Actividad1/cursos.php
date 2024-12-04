<?php
// Conexión a la base de datos
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'cursos_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar el formulario de inscripción
if (isset($_POST['inscribir'])) {
    $curso_id = $_POST['curso'];
    $nombre = $_POST['nombre'];
    $celular = $_POST['celular'];
    $correo = $_POST['correo'];

    $sql = "INSERT INTO inscripciones (curso_id, nombre, celular, correo) VALUES ('$curso_id', '$nombre', '$celular', '$correo')";
    if ($conn->query($sql) === TRUE) {
        echo "Inscripción realizada con éxito";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Obtener cursos desde la base de datos
$sql = "SELECT * FROM cursos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos - Consejo Provincial de Tungurahua</title>
    <link rel="stylesheet" href="styles.css">
    
    <style>
        
        .header-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .header-container img {
            height: 150px; 
        }
    </style>
</head>
<body>
   
    <div class="header-container">
        <h1>CONSEJO PROVINCIAL DE TUNGURAHUA</h1>
        <img src="imagenes/logo.png" alt="Logo Consejo Provincial">
    </div>
    
    <p>
        El honorable Consejo Provincial de Tungurahua ha decidido apoyar a la ciudadanía 
        con la creación y apertura de los siguientes cursos para este año.
    </p>
    
    <!-- Subtítulo alineado a la izquierda -->
    <h2 style="text-align: left;">Oferta de cursos disponibles 2025</h2>
    
    <!-- Contenedor de los cursos -->

    <?php while ($row = $result->fetch_assoc()): ?>
        <!-- Curso individual -->
    <div class="courses-box"><h3 class="course-name"><?php echo $row['nombre']; ?></h3>
            <p class="description"><?php echo $row['descripcion']; ?></p>
            <p><strong>Duración:</strong> <?php echo $row['duracion']; ?></p>  
        
        </div>   
    <?php endwhile; ?>

     
    
    
    
   <h2 style="text-align: left;">INSCRIPCIONES CURSOS 2025</h2>
    
    <!-- Formulario de inscripción -->
<form method="POST" action="cursos.php">
    <label for="curso">Selecciona el curso:</label>
    <select name="curso" id="curso" required>
        <?php
        // Mostrar los cursos disponibles en el select
        $result = $conn->query("SELECT * FROM cursos");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['nombre'] . "</option>";
        }
        ?>
    </select><br><br>

    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" required><br><br>

    <label for="celular">Celular:</label>
    <input type="text" name="celular" required><br><br>

    <label for="correo">Correo:</label>
    <input type="email" name="correo" required><br><br>

    <button type="submit" name="inscribir">Inscribirse</button>
</form>
    
    

</body>
</html>

<?php
$conn->close();
?>