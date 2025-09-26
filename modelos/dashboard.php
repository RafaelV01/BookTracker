<?php
include '../vistas/includes/auth_check.php';
include '../config/database.php';

// Mostrar mensajes de éxito/error
if (isset($_SESSION['login_success'])) {
    $success_message = $_SESSION['login_success'];
    unset($_SESSION['login_success']);
}

if (isset($_SESSION['book_success'])) {
    $book_success = $_SESSION['book_success'];
    unset($_SESSION['book_success']);
}

if (isset($_SESSION['book_error'])) {
    $book_error = $_SESSION['book_error'];
    unset($_SESSION['book_error']);
}

// Obtener los libros del usuario
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM books WHERE user_id = ? ORDER BY status, title";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$books = $stmt->fetchAll();
?>

<?php include '../vistas/includes/header.php'; ?>

<div class="container mt-4">
    <!-- Notificación de éxito de login -->
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>¡Éxito!</strong> <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Notificación de éxito de libro -->
    <?php if (isset($book_success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>¡Éxito!</strong> <?php echo $book_success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Notificación de error de libro -->
    <?php if (isset($book_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error:</strong> <?php echo $book_error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-book"></i> Mi Estantería</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">
            <i class="fas fa-plus"></i> Agregar Libro
        </button>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Leídos</h5>
                    <p class="card-text h4">
                        <?php echo count(array_filter($books, function($book) { return $book['status'] == 'read'; })); ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Leyendo</h5>
                    <p class="card-text h4">
                        <?php echo count(array_filter($books, function($book) { return $book['status'] == 'reading'; })); ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Por Leer</h5>
                    <p class="card-text h4">
                        <?php echo count(array_filter($books, function($book) { return $book['status'] == 'to_read'; })); ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total</h5>
                    <p class="card-text h4"><?php echo count($books); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros para mostrar por estado -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="read-tab" data-bs-toggle="tab" data-bs-target="#read" type="button" role="tab">
                <i class="fas fa-check-circle"></i> Leídos
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reading-tab" data-bs-toggle="tab" data-bs-target="#reading" type="button" role="tab">
                <i class="fas fa-book-reader"></i> Leyendo
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="to_read-tab" data-bs-toggle="tab" data-bs-target="#to_read" type="button" role="tab">
                <i class="fas fa-bookmark"></i> Por Leer
            </button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <!-- Pestaña de Libros Leídos -->
        <div class="tab-pane fade show active" id="read" role="tabpanel">
            <div class="row mt-3" id="read-books">
                <?php $read_books = array_filter($books, function($book) { return $book['status'] == 'read'; }); ?>
                <?php if (empty($read_books)): ?>
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-books fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay libros leídos</h4>
                        <p class="text-muted">Agrega algunos libros a tu colección de leídos.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($read_books as $book): ?>
                        <div class="col-md-2 mb-3">
                            <div class="book-spine" data-bs-toggle="modal" data-bs-target="#bookModal" data-book-id="<?php echo $book['id']; ?>">
                                <div class="spine-content">
                                    <h6><?php echo htmlspecialchars($book['title']); ?></h6>
                                    <?php if ($book['rating']): ?>
                                        <div class="rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?php echo $i <= $book['rating'] ? ' text-warning' : ' text-muted'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pestaña de Libros en Progreso -->
        <div class="tab-pane fade" id="reading" role="tabpanel">
            <div class="row mt-3" id="reading-books">
                <?php $reading_books = array_filter($books, function($book) { return $book['status'] == 'reading'; }); ?>
                <?php if (empty($reading_books)): ?>
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay libros en progreso</h4>
                        <p class="text-muted">Comienza a leer algún libro de tu lista.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($reading_books as $book): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <img src="<?php echo $book['cover_image'] ? '../' . htmlspecialchars($book['cover_image']) : 'https://via.placeholder.com/150x200?text=Sin+Portada'; ?>" 
                                     class="card-img-top" alt="Portada de <?php echo htmlspecialchars($book['title']); ?>"
                                     style="height: 200px; object-fit: cover;">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                                    <p class="card-text text-muted"><?php echo htmlspecialchars($book['author']); ?></p>
                                    <?php if ($book['total_pages'] > 0): ?>
                                        <div class="progress mb-2">
                                            <div class="progress-bar bg-warning" role="progressbar" 
                                                 style="width: <?php echo min(100, ($book['current_page'] / $book['total_pages']) * 100); ?>%">
                                            </div>
                                        </div>
                                        <p class="card-text small">
                                            <?php echo $book['current_page']; ?> / <?php echo $book['total_pages']; ?> páginas
                                            (<?php echo round(($book['current_page'] / $book['total_pages']) * 100, 1); ?>%)
                                        </p>
                                    <?php endif; ?>
                                    <div class="mt-auto">
                                        <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <a href="delete_book.php?id=<?php echo $book['id']; ?>" class="btn btn-danger btn-sm" 
                                           onclick="return confirm('¿Estás seguro de que quieres eliminar este libro?')">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pestaña de Libros por Leer -->
        <div class="tab-pane fade" id="to_read" role="tabpanel">
            <div class="row mt-3" id="to-read-books">
                <?php $to_read_books = array_filter($books, function($book) { return $book['status'] == 'to_read'; }); ?>
                <?php if (empty($to_read_books)): ?>
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-bookmark fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay libros por leer</h4>
                        <p class="text-muted">Agrega algunos libros a tu lista de deseos.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($to_read_books as $book): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <img src="<?php echo $book['cover_image'] ? '../' . htmlspecialchars($book['cover_image']) : 'https://via.placeholder.com/150x200?text=Sin+Portada'; ?>" 
                                     class="card-img-top" alt="Portada de <?php echo htmlspecialchars($book['title']); ?>"
                                     style="height: 200px; object-fit: cover;">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                                    <p class="card-text text-muted"><?php echo htmlspecialchars($book['author']); ?></p>
                                    <?php if ($book['premise']): ?>
                                        <p class="card-text flex-grow-1"><?php echo nl2br(htmlspecialchars($book['premise'])); ?></p>
                                    <?php endif; ?>
                                    <div class="mt-auto">
                                        <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <a href="delete_book.php?id=<?php echo $book['id']; ?>" class="btn btn-danger btn-sm" 
                                           onclick="return confirm('¿Estás seguro de que quieres eliminar este libro?')">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar libro -->
<div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBookModalLabel">Agregar Libro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="add_book.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título *</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="author" class="form-label">Autor *</label>
                        <input type="text" class="form-control" id="author" name="author" required>
                    </div>
                    <div class="mb-3">
                        <label for="genre" class="form-label">Género</label>
                        <input type="text" class="form-control" id="genre" name="genre" placeholder="Ej: Novela, Ciencia Ficción, etc.">
                    </div>
                    <div class="mb-3">
                        <label for="cover_image" class="form-label">Portada</label>
                        <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
                        <div class="form-text">Formatos aceptados: JPG, PNG, GIF, WebP</div>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Estado *</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="to_read">Por Leer</option>
                            <option value="reading">Leyendo</option>
                            <option value="read">Leído</option>
                        </select>
                    </div>
                    <div id="read-fields" class="status-fields" style="display: none;">
                        <div class="mb-3">
                            <label for="rating" class="form-label">Calificación (1-5)</label>
                            <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" placeholder="1 a 5">
                        </div>
                        <div class="mb-3">
                            <label for="review" class="form-label">Reseña</label>
                            <textarea class="form-control" id="review" name="review" rows="3" placeholder="Tu opinión sobre el libro"></textarea>
                        </div>
                    </div>
                    <div id="reading-fields" class="status-fields" style="display: none;">
                        <div class="mb-3">
                            <label for="total_pages" class="form-label">Total de páginas</label>
                            <input type="number" class="form-control" id="total_pages" name="total_pages" min="1" placeholder="Número total de páginas">
                        </div>
                        <div class="mb-3">
                            <label for="current_page" class="form-label">Páginas leídas</label>
                            <input type="number" class="form-control" id="current_page" name="current_page" min="0" placeholder="Páginas que has leído">
                        </div>
                    </div>
                    <div id="to_read-fields" class="status-fields">
                        <div class="mb-3">
                            <label for="premise" class="form-label">Premisa o sinopsis</label>
                            <textarea class="form-control" id="premise" name="premise" rows="3" placeholder="Breve descripción o por qué quieres leerlo"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Libro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para ver libro (leídos) -->
<div class="modal fade" id="bookModal" tabindex="-1" aria-labelledby="bookModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookModalLabel">Detalles del Libro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="bookModalBody">
                <!-- Los detalles se cargan via AJAX -->
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Mostrar/ocultar campos según el estado
    document.getElementById('status').addEventListener('change', function() {
        var status = this.value;
        document.querySelectorAll('.status-fields').forEach(function(field) {
            field.style.display = 'none';
        });
        document.getElementById(status + '-fields').style.display = 'block';
    });

    // Inicializar campos según el estado seleccionado
    document.addEventListener('DOMContentLoaded', function() {
        var status = document.getElementById('status').value;
        document.querySelectorAll('.status-fields').forEach(function(field) {
            field.style.display = 'none';
        });
        document.getElementById(status + '-fields').style.display = 'block';
    });

    // Cargar detalles del libro via AJAX
    var bookModal = document.getElementById('bookModal');
    bookModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var bookId = button.getAttribute('data-book-id');
        var modalBody = bookModal.querySelector('.modal-body');

        fetch('get_book.php?id=' + bookId)
            .then(response => response.text())
            .then(data => {
                modalBody.innerHTML = data;
            })
            .catch(error => {
                modalBody.innerHTML = '<div class="alert alert-danger">Error al cargar los detalles del libro.</div>';
            });
    });

    // Auto-dismiss alerts después de 5 segundos
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Validación básica del formulario
    document.querySelector('#addBookModal form').addEventListener('submit', function(e) {
        var title = document.getElementById('title').value.trim();
        var author = document.getElementById('author').value.trim();
        
        if (!title || !author) {
            e.preventDefault();
            alert('Por favor, completa los campos obligatorios (Título y Autor).');
        }
    });
</script>

<?php include '../vistas/includes/footer.php'; ?>