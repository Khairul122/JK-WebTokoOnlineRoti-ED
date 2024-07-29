<?php 
include '../koneksi/koneksi.php';

// Mengambil parameter GET dan membersihkannya untuk menghindari SQL Injection
$hal = mysqli_real_escape_string($conn, $_GET['hal']);
$kode_cs = mysqli_real_escape_string($conn, $_GET['kd_cs']);
$kode_produk = mysqli_real_escape_string($conn, $_GET['produk']);
$qty = isset($_GET['jml']) ? mysqli_real_escape_string($conn, $_GET['jml']) : 1;

// Mengambil data produk dari database
$result = mysqli_query($conn, "SELECT * FROM produk WHERE kode_produk = '$kode_produk'");
if (!$result || mysqli_num_rows($result) == 0) {
    die('Produk tidak ditemukan');
}
$row = mysqli_fetch_assoc($result);

$nama_produk = $row['nama'];
$kd = $row['kode_produk'];
$harga = $row['harga'];

// Memproses permintaan berdasarkan nilai $hal
if($hal == 1){
    $cek = mysqli_query($conn, "SELECT * from keranjang where kode_produk = '$kode_produk' and kode_customer = '$kode_cs'");
    if (!$cek) {
        die('Query gagal: ' . mysqli_error($conn));
    }
    $jml = mysqli_num_rows($cek);
    $row1 = mysqli_fetch_assoc($cek);
    if($jml > 0){
        $set = $row1['qty'] + 1;
        $update = mysqli_query($conn, "UPDATE keranjang SET qty = '$set' WHERE kode_produk = '$kode_produk' and kode_customer = '$kode_cs'");
        if($update){
            echo "
            <script>
            alert('BERHASIL DITAMBAHKAN KE KERANJANG');
            window.location = '../keranjang.php';
            </script>
            ";
            die;
        } else {
            die('Gagal mengupdate keranjang: ' . mysqli_error($conn));
        }
    } else {
        $insert = mysqli_query($conn, "INSERT INTO keranjang (kode_customer, kode_produk, nama_produk, qty, harga) VALUES ('$kode_cs', '$kd', '$nama_produk', '1', '$harga')");
        if($insert){
            echo "
            <script>
            alert('BERHASIL DITAMBAHKAN KE KERANJANG');
            window.location = '../keranjang.php';
            </script>
            ";
            die;
        } else {
            die('Gagal menambahkan ke keranjang: ' . mysqli_error($conn));
        }
    }
} else {
    $cek = mysqli_query($conn, "SELECT * from keranjang where kode_produk = '$kode_produk' and kode_customer = '$kode_cs'");
    if (!$cek) {
        die('Query gagal: ' . mysqli_error($conn));
    }
    $jml = mysqli_num_rows($cek);
    $row1 = mysqli_fetch_assoc($cek);
    if($jml > 0){
        $set = $row1['qty'] + $qty;
        $update = mysqli_query($conn, "UPDATE keranjang SET qty = '$set' WHERE kode_produk = '$kode_produk' and kode_customer = '$kode_cs'");
        if($update){
            echo "
            <script>
            alert('BERHASIL DITAMBAHKAN KE KERANJANG');
            window.location = '../detail_produk.php?produk=".$kode_produk."';
            </script>
            ";
            die;
        } else {
            die('Gagal mengupdate keranjang: ' . mysqli_error($conn));
        }
    } else {
        $insert = mysqli_query($conn, "INSERT INTO keranjang (kode_customer, kode_produk, nama_produk, qty, harga) VALUES ('$kode_cs', '$kd', '$nama_produk', '$qty', '$harga')");
        if($insert){
            echo "
            <script>
            alert('BERHASIL DITAMBAHKAN KE KERANJANG');
            window.location = '../detail_produk.php?produk=".$kode_produk."';
            </script>
            ";
            die;
        } else {
            die('Gagal menambahkan ke keranjang: ' . mysqli_error($conn));
        }
    }
}
?>
