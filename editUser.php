<!DOCTYPE html>
<html>
<head>
	<title>Sistem Informasi::Edit Data User</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/styleku.css">
	<script src="bootstrap/js/bootstrap.js"></script>
</head>
<body>
	<?php
	require "fungsi.php";
	require "head.html";

	$id = isset($_GET['kode']) ? $_GET['kode'] : '';

	if (!empty($id)) {
		$stmt = $koneksi->prepare("SELECT * FROM user WHERE iduser = ?");
		$stmt->bind_param("s", $id);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$stmt->close();
	}
	?>

	<div class="container mt-4">
		<h2 class="mb-3 text-center">EDIT DATA USER</h2>	
		<div class="row">
			<div class="col-sm-9">
				<form enctype="multipart/form-data" method="post" action="sv_editUser.php">
					<div class="form-group">
						<label for="iduser">ID User:</label>
						<input class="form-control" type="text" name="iduser" id="iduser" value="<?php echo htmlspecialchars($row['iduser'] ?? ''); ?>" readonly>
					</div>
					<div class="form-group">
						<label for="username">Username:</label>
						<input class="form-control" type="text" name="username" id="username" value="<?php echo htmlspecialchars($row['username'] ?? ''); ?>">
					</div>
					<div class="form-group">
						<label for="password">Password:</label>
						<input class="form-control" type="password" name="password" id="password" value="<?php echo htmlspecialchars($row['password'] ?? ''); ?>">
					</div>		
					<div class="form-group">
						<label for="status">Status:</label>
						<input class="form-control" type="text" name="status" id="status" value="<?php echo htmlspecialchars($row['status'] ?? ''); ?>">
					</div>			
					<div class="mt-3">		
						<button class="btn btn-primary" type="submit" id="submit">Simpan</button>
					</div>
					<input type="hidden" name="id" id="id" value="<?php echo htmlspecialchars($id); ?>">
				</form>
			</div>
		</div>
	</div>
</body>
</html>
