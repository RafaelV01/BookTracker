<?php
include 'includes/auth_check.php';
include 'config/database.php';

// Obtener los libros del usuario
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM books WHERE user_id = ? ORDER BY status, title";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$books = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <h1>Mi Estantería</h1>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addBookModal">Agregar Libro</button>

    <!-- Filtros para mostrar por estado -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="read-tab" data-bs-toggle="tab" data-bs-target="#read" type="button" role="tab">Leídos</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reading-tab" data-bs-toggle="tab" data-bs-target="#reading" type="button" role="tab">Leyendo</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="to_read-tab" data-bs-toggle="tab" data-bs-target="#to_read" type="button" role="tab">Por Leer</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="read" role="tabpanel">
            <div class="row mt-3" id="read-books">
                <!-- Libros leídos -->
                <?php foreach ($books as $book): ?>
                    <?php if ($book['status'] == 'read'): ?>
                        <div class="col-md-2 mb-3">
                            <div class="book-spine" data-bs-toggle="modal" data-bs-target="#bookModal" data-book-id="<?php echo $book['id']; ?>">
                                <div class="spine-content">
                                    <h6><?php echo $book['title']; ?></h6>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="tab-pane fade" id="reading" role="tabpanel">
            <div class="row mt-3" id="reading-books">
                <!-- Libros en progreso -->
                <?php foreach ($books as $book): ?>
                    <?php if ($book['status'] == 'reading'): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <img src="<?php echo $book['cover_image'] ? $book['cover_image'] : 'https://via.placeholder.com/150x200?text=Sin+Portada'; ?>" class="card-img-top" alt="Portada">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $book['title']; ?></h5>
                                    <p class="card-text"><?php echo $book['author']; ?></p>
                                    <div class="progress mb-2">
                                        <div class="progress-bar" role="progressbar" style="width: <?php echo ($book['current_page'] / $book['total_pages']) * 100; ?>%"></div>
                                    </div>
                                    <p><?php echo $book['current_page']; ?> / <?php echo $book['total_pages']; ?> páginas</p>
                                    <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="delete_book.php?id=<?php echo $book['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="tab-pane fade" id="to_read" role="tabpanel">
            <div class="row mt-3" id="to-read-books">
                <!-- Libros por leer -->
                <?php foreach ($books as $book): ?>
                    <?php if ($book['status'] == 'to_read'): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <img src="<?php echo $book['cover_image'] ? $book['cover_image'] : 'https://via.placeholder.com/150x200?text=Sin+Portada'; ?>" class="card-img-top" alt="Portada">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $book['title']; ?></h5>
                                    <p class="card-text"><?php echo $book['author']; ?></p>
                                    <p class="card-text"><?php echo $book['premise']; ?></p>
                                    <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="delete_book.php?id=<?php echo $book['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
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
                        <label for="title" class="form-label">Título</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="author" class="form-label">Autor</label>
                        <input type="text" class="form-control" id="author" name="author" required>
                    </div>
                    <div class="mb-3">
                        <label for="genre" class="form-label">Género</label>
                        <input type="text" class="form-control" id="genre" name="genre">
                    </div>
                    <div class="mb-3">
                        <label for="cover_image" class="form-label">Portada</label>
                        <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Estado</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="read">Leído</option>
                            <option value="reading">Leyendo</option>
                            <option value="to_read">Por Leer</option>
                        </select>
                    </div>
                    <div id="read-fields" class="status-fields">
                        <div class="mb-3">
                            <label for="rating" class="form-label">Calificación (1-5)</label>
                            <input type="number" class="form-control" id="rating" name="rating" min="1" max="5">
                        </div>
                        <div class="mb-3">
                            <label for="review" class="form-label">Reseña</label>
                            <textarea class="form-control" id="review" name="review"></textarea>
                        </div>
                    </div>
                    <div id="reading-fields" class="status-fields" style="display: none;">
                        <div class="mb-3">
                            <label for="total_pages" class="form-label">Total de páginas</label>
                            <input type="number" class="form-control" id="total_pages" name="total_pages">
                        </div>
                        <div class="mb-3">
                            <label for="current_page" class="form-label">Páginas leídas</label>
                            <input type="number" class="form-control" id="current_page" name="current_page">
                        </div>
                    </div>
                    <div id="to_read-fields" class="status-fields" style="display: none;">
                        <div class="mb-3">
                            <label for="premise" class="form-label">Premisa</label>
                            <textarea class="form-control" id="premise" name="premise"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
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

<script>
    // Mostrar/ocultar campos según el estado
    document.getElementById('status').addEventListener('change', function() {
        var status = this.value;
        // Ocultar todos los campos
        document.querySelectorAll('.status-fields').forEach(function(field) {
            field.style.display = 'none';
        });
        // Mostrar los campos correspondientes
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
            });
    });
</script>

<?php include 'includes/footer.php'; ?>