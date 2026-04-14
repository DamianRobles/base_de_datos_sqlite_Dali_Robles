<?php
$db = new PDO('sqlite:database.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("CREATE TABLE IF NOT EXISTS tareas (
id INTEGER PRIMARY KEY AUTOINCREMENT,
nombre TEXT,
descripcion TEXT
)");

// Guardar los datos antes de imprimir el HTML para que la lista se actualice instantáneamente
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("INSERT INTO tareas (nombre, descripcion) VALUES (?, ?)");
    $stmt->execute([$_POST['nombre'], $_POST['descripcion']]);
    
    // Redirigimos para evitar guardar la tarea doble al actualizar (F5) la página web
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tareas</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light my-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <h2 class="text-center mb-4">Gestor de Tareas</h2>
            
            <!-- Tarjeta para el Formulario -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-bold">Nombre de la tarea</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Ej: Comprar leche" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label fw-bold">Descripción</label>
                            <textarea class="form-control" name="descripcion" id="descripcion" rows="3" placeholder="Detalles de la tarea..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Guardar Tarea</button>
                    </form>
                </div>
            </div>

            <!-- Lista de Tareas -->
            <h4 class="mb-3">Tus Tareas</h4>
            <div class="list-group shadow-sm">
                <?php
                $result = $db->query("SELECT * FROM tareas ORDER BY id DESC");
                $tareas_encontradas = false;

                foreach ($result as $row) {
                    $tareas_encontradas = true;
                    echo '
                    <div class="list-group-item list-group-item-action d-flex flex-column align-items-start">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1 text-primary">' . htmlspecialchars($row['nombre']) . '</h5>
                            <small class="text-muted">#' . $row['id'] . '</small>
                        </div>
                        <p class="mb-1">' . nl2br(htmlspecialchars($row['descripcion'])) . '</p>
                    </div>';
                }

                if (!$tareas_encontradas) {
                    echo '<div class="alert alert-secondary text-center">No hay tareas aún. ¡Añade una nueva arriba!</div>';
                }
                ?>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>