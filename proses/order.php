<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../koneksi/koneksi.php';

// Mengambil data dari POST dan membersihkannya untuk menghindari SQL Injection
$kd_cs = mysqli_real_escape_string($conn, $_POST['kode_cs']);
$nama = mysqli_real_escape_string($conn, $_POST['nama']);
$prov = mysqli_real_escape_string($conn, $_POST['prov']);
$kota = mysqli_real_escape_string($conn, $_POST['kota']);
$alamat = mysqli_real_escape_string($conn, $_POST['almt']);
$kopos = mysqli_real_escape_string($conn, $_POST['kopos']);
$pengiriman = mysqli_real_escape_string($conn, $_POST['pengiriman']);
$ongkir = mysqli_real_escape_string($conn, $_POST['ongkir']);
$grand_total = mysqli_real_escape_string($conn, $_POST['grand_total']);
$tanggal = date('Y-m-d');

// Upload file bukti pengiriman
$target_dir = "C:/laragon/www/toko-online-roti/bukti_pengiriman/";
$target_file = $target_dir . basename($_FILES["bukti_pengiriman"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Check if file already exists
if (file_exists($target_file)) {
    echo "<script>alert('Sorry, file already exists.'); window.location='../keranjang.php';</script>";
    $uploadOk = 0;
}

// Check file size
if ($_FILES["bukti_pengiriman"]["size"] > 500000) {
    echo "<script>alert('Sorry, your file is too large.'); window.location='../keranjang.php';</script>";
    $uploadOk = 0;
}

// Allow certain file formats
if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
    echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.'); window.location='../keranjang.php';</script>";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "<script>alert('Sorry, your file was not uploaded.'); window.location='../keranjang.php';</script>";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["bukti_pengiriman"]["tmp_name"], $target_file)) {
        $bukti_pengiriman = basename($_FILES["bukti_pengiriman"]["name"]);
    } else {
        echo "<script>alert('Sorry, there was an error uploading your file.'); window.location='../keranjang.php';</script>";
        exit;
    }
}

// Logging input data
error_log("Data POST: kode_cs=$kd_cs, nama=$nama, prov=$prov, kota=$kota, alamat=$alamat, kopos=$kopos, pengiriman=$pengiriman, tanggal=$tanggal, bukti_pengiriman=$bukti_pengiriman, grand_total=$grand_total");

// Mengambil invoice terakhir dari produksi
$kode = mysqli_query($conn, "SELECT invoice FROM produksi ORDER BY invoice DESC LIMIT 1");
if (!$kode) {
    $error_message = mysqli_error($conn);
    echo "<script>console.error('Query gagal: " . addslashes($error_message) . "'); window.location='../keranjang.php';</script>";
    exit;
}
$data = mysqli_fetch_assoc($kode);
$num = $data ? substr($data['invoice'], 3, 4) : 0;
$add = (int) $num + 1;
if (strlen($add) == 1) {
    $format = "INV000" . $add;
} else if (strlen($add) == 2) {
    $format = "INV00" . $add;
} else if (strlen($add) == 3) {
    $format = "INV0" . $add;
} else {
    $format = "INV" . $add;
}

// Logging invoice format
error_log("Invoice format: $format");

// Mengambil data dari keranjang untuk customer tertentu
$keranjang = mysqli_query($conn, "SELECT * FROM keranjang WHERE kode_customer = '$kd_cs'");
if (!$keranjang) {
    $error_message = mysqli_error($conn);
    echo "<script>console.error('Query gagal: " . addslashes($error_message) . "'); window.location='../keranjang.php';</script>";
    exit;
}

// Memasukkan data ke tabel produksi
$order_successful = true;
$order_error = '';
while ($row = mysqli_fetch_assoc($keranjang)) {
    $kd_produk = $row['kode_produk'];
    $nama_produk = $row['nama_produk'];
    $qty = $row['qty'];
    $harga = $row['harga'];
    $status = "Pesanan Baru";
    $terima = 0; // Atur nilai default untuk kolom 'terima'

    $order = mysqli_query($conn, "INSERT INTO produksi (invoice, kode_customer, kode_produk, nama_produk, qty, harga, status, tanggal, provinsi, kota, alamat, kode_pos, terima, pengiriman, bukti_pengiriman, grand_total) VALUES ('$format', '$kd_cs', '$kd_produk', '$nama_produk', '$qty', '$harga', '$status', '$tanggal', '$prov', '$kota', '$alamat', '$kopos', '$terima', '$pengiriman', '$bukti_pengiriman', '$grand_total')");
    if (!$order) {
        $order_successful = false;
        $order_error = mysqli_error($conn);
        break;
    }
}

// Menghapus data dari tabel keranjang jika order berhasil
if ($order_successful) {
    $del_keranjang = mysqli_query($conn, "DELETE FROM keranjang WHERE kode_customer = '$kd_cs'");
    if (!$del_keranjang) {
        $error_message = mysqli_error($conn);
        echo "<script>console.error('Gagal menghapus keranjang: " . addslashes($error_message) . "'); window.location='../keranjang.php';</script>";
        exit;
    } else {
        echo "
        <script>
        alert('Order berhasil dilakukan');
        window.location = '../selesai.php';
        </script>
        ";
    }
} else {
    echo "
    <script>
    console.error('Gagal melakukan order: " . addslashes($order_error) . "');
    alert('Gagal melakukan order');
    window.location = '../keranjang.php';
    </script>
    ";
}
?>
