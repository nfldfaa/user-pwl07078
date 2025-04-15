<?php
include 'koneksi.php';

if (!isset($conn) || $conn->connect_error) {
    die("Koneksi database gagal. Silakan cek file koneksi.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Galeri Gambar</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styleku.css">
    <script src="bootstrap/js/bootstrap.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <style>
        .gallery-container {
            padding: 20px;
        }
        .upload-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .gallery-item {
            margin-bottom: 20px;
            text-align: center;
        }
        .gallery-img {
            max-width: 100%;
            height: auto;
            display: block;
            transition: all 0.3s;
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .gallery-img:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .file-info {
            margin-top: 5px;
            font-size: 0.9em;
        }
        .error-message {
            color: #dc3545;
            margin-top: 5px;
        }
        #galleryContainer {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
    </style>
</head>
<body>
<?php require "head.html"; ?>
    <div class="container gallery-container">
        <h2 class="text-center mb-4">GALERI GAMBAR</h2>
        
        <!-- Form Upload -->
        <div class="upload-section">
            <h4 class="text-center mb-3">Unggah Gambar Baru</h4>
            <form id="uploadForm" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="gambar" name="gambar" accept="image/jpeg,image/png,image/gif" required>
                        <label class="custom-file-label" for="gambar">Pilih file gambar (JPEG, PNG, GIF)</label>
                    </div>
                    <small class="form-text text-muted">Ukuran maksimal: 10MB</small>
                    <div id="fileError" class="error-message"></div>
                </div>
                <button type="submit" class="btn btn-primary">Unggah</button>
            </form>
            <div id="uploadMessage" class="mt-3"></div>
        </div>
        
        <!-- Galeri Gambar -->
        <div class="row" id="galleryContainer">
            <?php
            $result = $conn->query("SELECT * FROM galeri_gambar ORDER BY uploaded_at DESC");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="col-md-3 mb-4 gallery-item">';
                    echo '<div class="card h-100">';
                    echo '<a href="' . $row['filepath'] . '" target="_blank">';
                    echo '<img src="' . $row['thumbpath'] . '" class="card-img-top gallery-img" alt="' . htmlspecialchars($row['filename']) . '">';
                    echo '</a>';
                    echo '<div class="card-body">';
                    echo '<p class="card-text small text-truncate">' . htmlspecialchars($row['filename']) . '</p>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12"><div class="alert alert-info">Belum ada gambar yang diupload</div></div>';
            }
            ?>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Update label nama file
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
            
            // Validasi ukuran file
            const file = this.files[0];
            const maxSize = 10 * 1024 * 1024; // 10MB
            
            if (file && file.size > maxSize) {
                $('#fileError').text('Ukuran file terlalu besar (maks 10MB)');
                $(this).val('');
                $(this).next('.custom-file-label').html('Pilih file gambar (JPEG, PNG, GIF)');
            } else {
                $('#fileError').text('');
            }
        });
        
        // Handle form submission dengan AJAX
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $('#uploadMessage').html('').removeClass('alert-danger alert-success');
            
            $.ajax({
                url: 'sv_galeriGambar.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Tambahkan gambar baru ke galeri
                        var newItem = `
                            <div class="col-md-3 mb-4 gallery-item">
                                <div class="card h-100">
                                    <a href="${response.filepath}" target="_blank">
                                        <img src="${response.thumbnail}" class="card-img-top gallery-img" alt="${response.filename}">
                                    </a>
                                    <div class="card-body">
                                        <p class="card-text small text-truncate">${response.filename}</p>
                                    </div>
                                </div>
                            </div>`;
                        
                        $('#galleryContainer').prepend(newItem);
                        $('#uploadMessage').addClass('alert alert-success').html('Upload berhasil!');
                        $('#uploadForm')[0].reset();
                        $('.custom-file-label').html('Pilih file gambar (JPEG, PNG, GIF)');
                    } else {
                        $('#uploadMessage').addClass('alert alert-danger').html('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    $('#uploadMessage').addClass('alert alert-danger').html('Terjadi kesalahan: ' + error);
                }
            });
        });
    });
    </script>
</body>
</html>