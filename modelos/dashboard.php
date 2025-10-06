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

<style>
    :root {
        --bg-1: #f6efe4;
        --bg-2: #e9dcc7;
        --card: #fffdfb;
        --accent-brown: #8B5E3C;
        --dark-brown: #4E342E;
        --muted: #6b6b6b;
        --glass: rgba(255, 255, 255, 0.6);
    }

    /* Page */
    body {
        background: linear-gradient(160deg, var(--bg-1), var(--bg-2));
        min-height: 100vh;
        font-family: "Merriweather", serif;
        color: #222;
    }

    /* Container spacing */
    .container.mt-4 {
        padding-top: 1.25rem;
        padding-bottom: 2.25rem;
    }

    /* Header row */
    .d-flex.justify-content-between.align-items-center.mb-4 h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark-brown);
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    /* Primary action */
    .btn-primary {
        background: linear-gradient(90deg, var(--accent-brown), #C9B37F);
        border: none;
        border-radius: 12px;
        padding: 0.55rem 0.9rem;
        font-weight: 700;
        box-shadow: 0 6px 18px rgba(139, 94, 60, 0.18);
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.18);
        background: linear-gradient(90deg, #6b4a2d, var(--accent-brown));
        color: #fff;
    }

    /* Alerts polished */
    .alert {
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        font-weight: 600;
    }

    /* Cards */
    .card {
        background: var(--card);
        border-radius: 18px;
        box-shadow: 0 10px 28px rgba(19, 19, 19, 0.06);
        border: none;
        overflow: hidden;
    }

    /* Layout for stats: responsive grid */
    .row.mb-4 {
        margin-bottom: 1.25rem !important;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }

    @media (max-width: 900px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 520px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Stats cards */
    .stats-card {
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.9rem;
        min-height: 86px;
        transition: transform .25s ease, box-shadow .25s ease;
        justify-content: space-between;
    }

    .stats-card .left {
        display: flex;
        flex-direction: column;
        gap: 0.125rem;
    }

    .stats-card .icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        box-shadow: inset 0 -6px 18px rgba(255, 255, 255, 0.08);
    }

    .stats-card .num {
        font-size: 1.6rem;
        font-weight: 800;
        line-height: 1;
    }

    .stats-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 36px rgba(0, 0, 0, 0.12);
    }

    /* Distinct styles */
    .stats-leidos {
        background: linear-gradient(135deg, #e8faf0, #bff0cf);
        color: var(--dark-brown);
    }

    .stats-leidos .icon {
        background: rgba(75, 181, 104, 0.12);
        color: #2b6a3a;
    }

    .stats-leyendo {
        background: linear-gradient(135deg, #fff6db, #ffe08a);
        color: var(--dark-brown);
    }

    .stats-leyendo .icon {
        background: rgba(255, 196, 0, 0.08);
        color: #8b5e3c;
    }

    .stats-porleer {
        background: linear-gradient(135deg, #dff7fb, #9feaf2);
        color: var(--dark-brown);
    }

    .stats-porleer .icon {
        background: rgba(0, 188, 212, 0.08);
        color: #027a8a;
    }

    .stats-total {
        background: linear-gradient(135deg, #dce9ff, #9fc6ff);
        color: var(--dark-brown);
    }

    .stats-total .icon {
        background: rgba(0, 102, 255, 0.08);
        color: #164ea6;
    }

    /* Tabs styling */
    .nav-tabs {
        border-bottom: none;
        margin-top: 6px;
    }

    .nav-tabs .nav-link {
        border: none;
        padding: 0.6rem 1rem;
        margin-right: 0.35rem;
        border-radius: 12px;
        color: var(--muted);
        font-weight: 700;
    }

    .nav-tabs .nav-link.active {
        background: linear-gradient(90deg, var(--accent-brown), #C9B37F);
        color: #fff;
        box-shadow: 0 8px 20px rgba(139, 94, 60, 0.14);
    }

    /* Read section spine container */
    #read-books {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    /* Book spine improved */
    .book-spine {
        background: linear-gradient(160deg, #7a3f13, #9d4d24);
        color: #fff;
        padding: 10px;
        height: 220px;
        width: 52px;
        writing-mode: vertical-rl;
        text-orientation: mixed;
        cursor: pointer;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform .32s cubic-bezier(.2, .8, .2, 1), box-shadow .32s;
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.18);
        position: relative;
        perspective: 800px;
    }

    .book-spine::after {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 8px;
        box-shadow: inset -10px 0 24px rgba(0, 0, 0, 0.12);
        pointer-events: none;
    }

    .book-spine:hover {
        transform: translateY(-6px) rotateY(6deg) scale(1.06);
        box-shadow: 0 18px 36px rgba(0, 0, 0, 0.28);
    }

    .spine-content h6 {
        margin: 0;
        font-weight: 700;
        font-size: 0.86rem;
        text-align: center;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.36);
    }

    /* Rating stars small */
    .rating i {
        margin: 0 1px;
        font-size: 0.85rem;
    }

    /* Book cards grid */
    .card-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }

    @media (max-width: 992px) {
        .card-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .card-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Book card (reading/por leer) */
    .book-card {
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .book-card img {
        height: 210px;
        object-fit: cover;
        width: 100%;
    }

    .book-card .card-body {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        padding: 1rem;
    }

    .book-card .meta {
        color: var(--muted);
        font-size: 0.93rem;
    }

    .book-card .actions {
        display: flex;
        gap: 0.5rem;
    }

    /* Progress bar custom */
    .progress {
        height: 10px;
        border-radius: 8px;
        overflow: hidden;
        background: linear-gradient(90deg, #eee, #f7f7f7);
        box-shadow: inset 0 -2px 6px rgba(0, 0, 0, 0.04);
    }

    .progress-bar {
        transition: width 1s cubic-bezier(.2, .9, .2, 1);
    }

    /* Modal tweaks */
    .modal-content {
        border-radius: 14px;
        overflow: hidden;
    }

    /* Small utilities */
    .text-muted {
        color: #7a7a7a !important;
    }

    /* Footer spacing from included footer */
    .container+footer {
        margin-top: 2.5rem;
    }

    /* Mensaje centrado que ocupa toda la grid cuando no hay ítems */
    .card-grid .no-items,
    #read-books .no-items {
        grid-column: 1 / -1;
        /* ocupa todas las columnas de la grid */
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 220px;
        /* altura mínima para centrar visualmente */
        padding: 2rem;
        background: transparent;
        /* si quieres ponerlo tipo card, cambia esto */
        border-radius: 12px;
        color: var(--muted, #7a7a7a);
    }

    /* Si preferís que el mensaje parezca una tarjeta suave */
    .card-grid .no-items .empty-card {
        padding: 1.6rem;
        max-width: 720px;
        width: 100%;
    }
</style>

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
        <div class="col-12">
            <div class="stats-grid">
                <div class="stats-card stats-leidos">
                    <div class="left">
                        <div style="font-size:0.95rem;color:rgba(0,0,0,0.6);">Leídos</div>
                        <div class="num stat-number" data-target="<?php echo count(array_filter($books, function ($book) {
                            return $book['status'] == 'read';
                        })); ?>">
                            0</div>
                    </div>
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                </div>

                <div class="stats-card stats-leyendo">
                    <div class="left">
                        <div style="font-size:0.95rem;color:rgba(0,0,0,0.6);">Leyendo</div>
                        <div class="num stat-number" data-target="<?php echo count(array_filter($books, function ($book) {
                            return $book['status'] == 'reading';
                        })); ?>">
                            0</div>
                    </div>
                    <div class="icon"><i class="fas fa-book-reader"></i></div>
                </div>

                <div class="stats-card stats-porleer">
                    <div class="left">
                        <div style="font-size:0.95rem;color:rgba(0,0,0,0.6);">Por Leer</div>
                        <div class="num stat-number" data-target="<?php echo count(array_filter($books, function ($book) {
                            return $book['status'] == 'to_read';
                        })); ?>">
                            0</div>
                    </div>
                    <div class="icon"><i class="fas fa-bookmark"></i></div>
                </div>

                <div class="stats-card stats-total">
                    <div class="left">
                        <div style="font-size:0.95rem;color:rgba(0,0,0,0.6);">Total</div>
                        <div class="num stat-number" data-target="<?php echo count($books); ?>">0</div>
                    </div>
                    <div class="icon"><i class="fas fa-layer-group"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros para mostrar por estado -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="read-tab" data-bs-toggle="tab" data-bs-target="#read" type="button"
                role="tab">
                <i class="fas fa-check-circle"></i> Leídos
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reading-tab" data-bs-toggle="tab" data-bs-target="#reading" type="button"
                role="tab">
                <i class="fas fa-book-reader"></i> Leyendo
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="to_read-tab" data-bs-toggle="tab" data-bs-target="#to_read" type="button"
                role="tab">
                <i class="fas fa-bookmark"></i> Por Leer
            </button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <!-- Pestaña de Libros Leídos -->
        <div class="tab-pane fade show active" id="read" role="tabpanel">
            <div class="row mt-3" id="read-books">
                <?php $read_books = array_filter($books, function ($book) {
                    return $book['status'] == 'read';
                }); ?>
                <?php if (empty($read_books)): ?>
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-books fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay libros leídos</h4>
                        <p class="text-muted">Agrega algunos libros a tu colección de leídos.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($read_books as $book): ?>
                        <div class="col-auto mb-3">
                            <div class="book-spine" data-bs-toggle="modal" data-bs-target="#bookModal"
                                data-book-id="<?php echo $book['id']; ?>">
                                <div class="spine-content">
                                    <h6><?php echo htmlspecialchars($book['title']); ?></h6>
                                    <?php if ($book['rating']): ?>
                                        <div class="rating mt-2 text-center">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i
                                                    class="fas fa-star<?php echo $i <= $book['rating'] ? ' text-warning' : ' text-muted'; ?>"></i>
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
            <div class="row mt-3 card-grid">
                <?php $reading_books = array_filter($books, function ($book) {
                    return $book['status'] == 'reading';
                }); ?>
                <?php if (empty($reading_books)): ?>
                    <div class="no-items">
                        <div class="empty-card text-center">
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted mb-1">No hay libros en progreso</h4>
                            <p class="text-muted mb-0">Comienza a leer algún libro de tu lista.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($reading_books as $book): ?>
                        <div>
                            <div class="book-card card h-100" role="button" tabindex="0" style="cursor:pointer;"
                                data-bs-toggle="modal" data-bs-target="#bookModal" data-book-id="<?php echo $book['id']; ?>">
                                <img src="<?php echo $book['cover_image'] ? htmlspecialchars($book['cover_image']) : 'https://via.placeholder.com/300x420?text=Sin+Portada'; ?>"
                                    alt="Portada de <?php echo htmlspecialchars($book['title']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                                    <div class="meta"><?php echo htmlspecialchars($book['author']); ?></div>

                                    <?php if ($book['total_pages'] > 0):
                                        $pct = min(100, ($book['current_page'] / $book['total_pages']) * 100);
                                        ?>
                                        <div class="mt-2">
                                            <div class="progress">
                                                <div class="progress-bar bg-warning" role="progressbar"
                                                    style="width: <?php echo $pct; ?>%;"></div>
                                            </div>
                                            <p class="small text-muted mt-1"><?php echo $book['current_page']; ?> /
                                                <?php echo $book['total_pages']; ?> páginas · <?php echo round($pct, 1); ?>%
                                            </p>
                                        </div>
                                    <?php endif; ?>

                                    <div class="mt-auto actions">
                                        <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="btn btn-warning btn-sm"
                                            onclick="event.stopPropagation();">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <a href="delete_book.php?id=<?php echo $book['id']; ?>" class="btn btn-danger btn-sm"
                                            onclick="event.stopPropagation(); return confirm('¿Estás seguro de que quieres eliminar este libro?')">
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
            <div class="row mt-3 card-grid" id="to-read-books">
                <?php $to_read_books = array_filter($books, function ($book) {
                    return $book['status'] == 'to_read';
                }); ?>
                <?php if (empty($to_read_books)): ?>
                    <div class="no-items">
                        <div class="empty-card text-center">
                            <i class="fas fa-bookmark fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted mb-1">No hay libros por leer</h4>
                            <p class="text-muted mb-0">Agrega algunos libros a tu lista de deseos.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($to_read_books as $book): ?>
                        <div>
                            <div class="book-card card h-100" role="button" tabindex="0" style="cursor:pointer;"
                                data-bs-toggle="modal" data-bs-target="#bookModal" data-book-id="<?php echo $book['id']; ?>">
                                <img src="<?php echo $book['cover_image'] ? htmlspecialchars($book['cover_image']) : 'https://via.placeholder.com/300x420?text=Sin+Portada'; ?>"
                                    alt="Portada de <?php echo htmlspecialchars($book['title']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                                    <div class="meta"><?php echo htmlspecialchars($book['author']); ?></div>

                                    <?php if ($book['premise']): ?>
                                        <p class="card-text flex-grow-1"><?php echo nl2br(htmlspecialchars($book['premise'])); ?>
                                        </p>
                                    <?php endif; ?>

                                    <div class="mt-auto actions">
                                        <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="btn btn-warning btn-sm"
                                            onclick="event.stopPropagation();">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <a href="delete_book.php?id=<?php echo $book['id']; ?>" class="btn btn-danger btn-sm"
                                            onclick="event.stopPropagation(); return confirm('¿Estás seguro de que quieres eliminar este libro?')">
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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Libro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="add_book.php" method="POST" enctype="multipart/form-data" id="addBookForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="title" class="form-label">Título *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="col-md-6">
                            <label for="author" class="form-label">Autor *</label>
                            <input type="text" class="form-control" id="author" name="author" required>
                        </div>
                        <div class="col-md-6">
                            <label for="genre" class="form-label">Género</label>
                            <input type="text" class="form-control" id="genre" name="genre"
                                placeholder="Ej: Novela, Ciencia Ficción">
                        </div>
                        <div class="col-md-6">
                            <label for="cover_image" class="form-label">Portada</label>
                            <input type="file" class="form-control" id="cover_image" name="cover_image"
                                accept="image/*">
                            <div class="form-text">JPG, PNG, GIF, WebP</div>
                        </div>

                        <div class="col-md-6">
                            <label for="pdf_file" class="form-label">Archivo PDF (opcional)</label>
                            <input type="file" class="form-control" id="pdf_file" name="pdf_file"
                                accept="application/pdf">
                            <div class="form-text">Sube un PDF (máx 10 MB). Si adjuntas PDF, se mostrará en el modal de
                                detalles.</div>
                        </div>


                        <div class="col-md-6">
                            <label for="status" class="form-label">Estado *</label>
                            <select class="form-control form-select" id="status" name="status" required>
                                <option value="to_read">Por Leer</option>
                                <option value="reading">Leyendo</option>
                                <option value="read">Leído</option>
                            </select>
                        </div>

                        <!-- Dynamic fields -->
                        <div class="col-md-6 status-fields" id="to_read-fields">
                            <label for="premise" class="form-label">Premisa / Sinopsis</label>
                            <textarea class="form-control" id="premise" name="premise" rows="2"></textarea>
                        </div>

                        <div class="col-md-6 status-fields" id="reading-fields" style="display:none;">
                            <label for="total_pages" class="form-label">Total de páginas</label>
                            <input type="number" class="form-control" id="total_pages" name="total_pages" min="1"
                                placeholder="Total de páginas">
                            <label for="current_page" class="form-label mt-2">Páginas leídas</label>
                            <input type="number" class="form-control" id="current_page" name="current_page" min="0"
                                placeholder="Página actual">
                        </div>

                        <div class="col-md-6 status-fields" id="read-fields" style="display:none;">
                            <label for="rating" class="form-label">Calificación (1-5)</label>
                            <input type="number" class="form-control" id="rating" name="rating" min="1" max="5"
                                placeholder="1 a 5">
                            <label for="review" class="form-label mt-2">Reseña</label>
                            <textarea class="form-control" id="review" name="review" rows="2"></textarea>
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
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Libro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="bookModalBody">
                <!-- Los detalles se cargan via AJAX -->
                <div class="text-center text-muted py-4">Cargando...</div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ===== Animate stat counters =====
    document.addEventListener('DOMContentLoaded', function () {
        const counters = document.querySelectorAll('.stat-number');
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target') || 0;
            let duration = 800; // ms
            let start = 0;
            const stepTime = Math.max(10, Math.floor(duration / Math.max(1, target)));
            const startTime = performance.now();
            const step = (now) => {
                const progress = Math.min(1, (now - startTime) / duration);
                const current = Math.floor(progress * target);
                counter.textContent = current;
                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    counter.textContent = target;
                }
            };
            requestAnimationFrame(step);
        });
    });

    // ===== Status fields toggle in modal form =====
    (function () {
        const statusEl = document.getElementById('status');
        if (!statusEl) return;
        const fields = document.querySelectorAll('.status-fields');

        function showFor(status) {
            fields.forEach(f => f.style.display = 'none');
            const el = document.getElementById(status + '-fields');
            if (el) el.style.display = 'block';
        }

        statusEl.addEventListener('change', function () {
            showFor(this.value);
        });

        // initialize on load
        showFor(statusEl.value || 'to_read');
    })();

    // ===== Load book details via fetch into modal =====
    (function () {
        const bookModalEl = document.getElementById('bookModal');
        if (!bookModalEl) return;
        bookModalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const bookId = button.getAttribute('data-book-id');
            const modalBody = document.getElementById('bookModalBody');
            modalBody.innerHTML = '<div class="text-center text-muted py-4">Cargando...</div>';
            fetch('get_book.php?id=' + encodeURIComponent(bookId))
                .then(r => r.text())
                .then(html => { modalBody.innerHTML = html; })
                .catch(() => { modalBody.innerHTML = '<div class="alert alert-danger">Error al cargar los detalles del libro.</div>'; });
        });
    })();

    // ===== Auto-dismiss alerts =====
    setTimeout(function () {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function (alert) {
            try { new bootstrap.Alert(alert).close(); } catch (e) { alert.remove(); }
        });
    }, 5000);

    // ===== Simple progressive width animation for visible progress bars (on load) =====
    window.addEventListener('load', function () {
        document.querySelectorAll('.progress-bar').forEach(function (pb) {
            const width = pb.style.width || '0%';
            pb.style.width = '0%';
            setTimeout(() => pb.style.width = width, 50);
        });
    });
</script>

<?php include '../vistas/includes/footer.php'; ?>