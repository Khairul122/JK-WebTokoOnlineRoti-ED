<?php 
include '../../koneksi/koneksi.php';
$inv = $_GET['inv'];

$tolak = mysqli_query($conn, "UPDATE produksi SET tolak = '1', terima = '2', status = 'Pesanan Ditolak' WHERE invoice = '$inv'");

if($tolak){
    echo "
    <script>
    alert('PESANAN DITOLAK');
    window.location = '../produksi.php';
    </script>
    ";
} else {
    echo "
    <script>
    alert('Gagal menolak pesanan.');
    window.location = '../produksi.php';
    </script>
    ";
}
?>
