<?php 
include '../../koneksi/koneksi.php';

$response = array('success' => false, 'message' => 'Gagal memperbarui catatan.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoice = mysqli_real_escape_string($conn, $_POST['invoice']);
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);

    $update = mysqli_query($conn, "UPDATE produksi SET catatan_penjual = '$catatan' WHERE invoice = '$invoice'");

    if ($update) {
        $response['success'] = true;
        $response['message'] = 'Catatan berhasil diperbarui';
    } else {
        $response['message'] = 'Gagal memperbarui catatan: ' . mysqli_error($conn);
    }
}

echo json_encode($response);
?>
