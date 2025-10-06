<?php
include '../vistas/includes/auth_check.php';
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $genre = trim($_POST['genre'] ?? '');
    $status = $_POST['status'];

    // Directorios
    $baseUploadDir = "../uploads/";
    $coverDir = $baseUploadDir;
    $pdfDir = $baseUploadDir . "pdfs/";

    // Crear carpetas si no existen
    if (!is_dir($coverDir))
        mkdir($coverDir, 0777, true);
    if (!is_dir($pdfDir))
        mkdir($pdfDir, 0777, true);

    // ---- Subir imagen de portada (opcional) ----
    $cover_image = '';
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $f = $_FILES['cover_image'];
        // Validar tipo de archivo de forma segura
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $f['tmp_name']);
        finfo_close($finfo);

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($mime, $allowed_types)) {
            $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
            $filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . strtolower($ext);
            $dest = $coverDir . $filename;
            if (move_uploaded_file($f['tmp_name'], $dest)) {
                // Guarda la ruta tal como la ven tus scripts (tu código original usaba "../uploads/...")
                $cover_image = $dest;
            } else {
                // Si falla mover, mantener vacío (puedes loggear)
                $cover_image = '';
            }
        } else {
            $_SESSION['book_error'] = "Formato de imagen no permitido. Usa JPG, PNG, GIF o WebP.";
            header('Location: dashboard.php');
            exit;
        }
    }

    // ---- Subir PDF (opcional) ----
    $pdf_file = null;
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] != UPLOAD_ERR_NO_FILE) {
        $f = $_FILES['pdf_file'];
        if ($f['error'] === UPLOAD_ERR_OK) {
            // Limite 10 MB
            $maxSize = 10 * 1024 * 1024;
            if ($f['size'] > $maxSize) {
                $_SESSION['book_error'] = "El PDF supera el límite de 10 MB.";
                header('Location: dashboard.php');
                exit;
            }
            // Validar MIME con finfo
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $f['tmp_name']);
            finfo_close($finfo);

            if ($mime !== 'application/pdf') {
                $_SESSION['book_error'] = "El archivo debe ser un PDF válido.";
                header('Location: dashboard.php');
                exit;
            }

            $ext = 'pdf';
            $filename = time() . '_pdf_' . bin2hex(random_bytes(6)) . '.' . $ext;
            $dest = $pdfDir . $filename;
            if (move_uploaded_file($f['tmp_name'], $dest)) {
                $pdf_file = $dest;
            } else {
                $_SESSION['book_error'] = "Error al subir el PDF.";
                header('Location: dashboard.php');
                exit;
            }
        } else {
            $_SESSION['book_error'] = "Error al subir el PDF (código {$f['error']}).";
            header('Location: dashboard.php');
            exit;
        }
    }

    // Campos según el estado
    $total_pages = null;
    $current_page = null;
    $rating = null;
    $review = null;
    $premise = null;

    if ($status == 'reading') {
        $total_pages = !empty($_POST['total_pages']) ? (int) $_POST['total_pages'] : null;
        $current_page = !empty($_POST['current_page']) ? (int) $_POST['current_page'] : null;
    } elseif ($status == 'read') {
        $rating = !empty($_POST['rating']) ? (int) $_POST['rating'] : null;
        $review = trim($_POST['review'] ?? '');
    } elseif ($status == 'to_read') {
        $premise = trim($_POST['premise'] ?? '');
    }

    try {
        // Inserta pdf_file como nueva columna
        $sql = "INSERT INTO books (user_id, title, author, genre, cover_image, pdf_file, total_pages, current_page, rating, review, premise, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $user_id,
            $title,
            $author,
            $genre,
            $cover_image ?: null,
            $pdf_file ?: null,
            $total_pages,
            $current_page,
            $rating,
            $review,
            $premise,
            $status
        ]);

        $_SESSION['book_success'] = "Libro agregado exitosamente: " . htmlspecialchars($title);
        header('Location: dashboard.php');
        exit;

    } catch (PDOException $e) {
        // Borrar archivos subidos en caso de error para no dejar basura
        if (!empty($cover_image) && file_exists($cover_image))
            @unlink($cover_image);
        if (!empty($pdf_file) && file_exists($pdf_file))
            @unlink($pdf_file);

        $_SESSION['book_error'] = "Error al agregar el libro: " . $e->getMessage();
        header('Location: dashboard.php');
        exit;
    }
} else {
    // Si no es POST, redirigir al dashboard
    header('Location: dashboard.php');
    exit;
}
