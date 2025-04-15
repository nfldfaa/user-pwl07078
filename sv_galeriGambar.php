<?php
require __DIR__ . '/koneksi.php';
header('Content-Type: application/json');

// Response default
$response = ['status' => 'error', 'message' => 'Terjadi kesalahan'];

// Validasi method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Method tidak diizinkan';
    echo json_encode($response);
    exit;
}

// Validasi file upload
if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
    $response['message'] = 'Tidak ada file yang diupload atau terjadi kesalahan upload';
    echo json_encode($response);
    exit;
}

// Setup direktori
$baseDir = __DIR__ . '/gambar/';
$uploadDir = $baseDir . 'uploads/';
$thumbDir = $baseDir . 'thumbs/';

// Buat direktori jika belum ada
if (!file_exists($uploadDir) && !mkdir($uploadDir, 0777, true)) {
    $response['message'] = 'Gagal membuat direktori upload';
    echo json_encode($response);
    exit;
}

if (!file_exists($thumbDir) && !mkdir($thumbDir, 0777, true)) {
    $response['message'] = 'Gagal membuat direktori thumbnail';
    echo json_encode($response);
    exit;
}

// Validasi file
$fileName = basename($_FILES['gambar']['name']);
$fileTmp = $_FILES['gambar']['tmp_name'];
$fileSize = $_FILES['gambar']['size'];
$fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// Ekstensi yang diizinkan
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array($fileType, $allowedTypes)) {
    $response['message'] = 'Hanya file JPG, JPEG, PNG, dan GIF yang diperbolehkan';
    echo json_encode($response);
    exit;
}

// Ukuran maksimal 10MB
if ($fileSize > 10 * 1024 * 1024) {
    $response['message'] = 'Ukuran file terlalu besar (maks 10MB)';
    echo json_encode($response);
    exit;
}

// Generate nama unik
$uniqueName = uniqid() . '_' . time() . '.' . $fileType;
$uploadPath = $uploadDir . $uniqueName;
$thumbPath = $thumbDir . 'thumb_' . $uniqueName;

// Pindahkan file upload
if (!move_uploaded_file($fileTmp, $uploadPath)) {
    $response['message'] = 'Gagal menyimpan file';
    echo json_encode($response);
    exit;
}

// Fungsi buat thumbnail
function createThumbnail($source, $destination, $thumbWidth = 200) {
    $info = getimagesize($source);
    if ($info === false) return false;

    $mime = $info['mime'];
    $width = $info[0];
    $height = $info[1];
    $thumbHeight = floor($height * ($thumbWidth / $width));

    try {
        switch($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($source);
                break;
            case 'image/png':
                $image = imagecreatefrompng($source);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($source);
                break;
            default:
                return false;
        }

        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
        
        // Handle transparansi PNG
        if ($mime === 'image/png') {
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
            $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
            imagefilledrectangle($thumb, 0, 0, $thumbWidth, $thumbHeight, $transparent);
        }

        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

        switch($mime) {
            case 'image/jpeg':
                imagejpeg($thumb, $destination, 80);
                break;
            case 'image/png':
                imagepng($thumb, $destination);
                break;
            case 'image/gif':
                imagegif($thumb, $destination);
                break;
        }

        imagedestroy($image);
        imagedestroy($thumb);
        return true;
    } catch (Exception $e) {
        error_log("Error creating thumbnail: " . $e->getMessage());
        return false;
    }
}

// Buat thumbnail
if (!createThumbnail($uploadPath, $thumbPath)) {
    unlink($uploadPath);
    $response['message'] = 'Gagal membuat thumbnail';
    echo json_encode($response);
    exit;
}

// Simpan ke database
$relativeUpload = 'gambar/uploads/' . $uniqueName;
$relativeThumb = 'gambar/thumbs/thumb_' . $uniqueName;
list($width, $height) = getimagesize($uploadPath);

$stmt = $conn->prepare("INSERT INTO galeri_gambar (filename, filepath, thumbpath, width, height) VALUES (?, ?, ?, ?, ?)");
if ($stmt === false) {
    unlink($uploadPath);
    unlink($thumbPath);
    $response['message'] = 'Error preparing statement: ' . $conn->error;
    echo json_encode($response);
    exit;
}

$stmt->bind_param("sssii", $fileName, $relativeUpload, $relativeThumb, $width, $height);

if ($stmt->execute()) {
    $response = [
        'status' => 'success',
        'message' => 'Upload berhasil',
        'filename' => $fileName,
        'filepath' => $relativeUpload,
        'thumbnail' => $relativeThumb,
        'width' => $width,
        'height' => $height
    ];
} else {
    unlink($uploadPath);
    unlink($thumbPath);
    $response['message'] = 'Database error: ' . $stmt->error;
}

$stmt->close();
$conn->close();
echo json_encode($response);
?>