<?php
include 'header.php';
include 'koneksi/koneksi.php';

// Ambil kode customer dari sesi
$kd_cs = $_SESSION['kd_cs'];

// Ambil data dari tabel produksi berdasarkan kode customer
$query = "SELECT * FROM produksi WHERE kode_customer = '$kd_cs'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die('Query Error: ' . mysqli_error($conn));
}

// Proses update catatan jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_order = mysqli_real_escape_string($conn, $_POST['id_order']);
    $catatan_pembeli = mysqli_real_escape_string($conn, $_POST['catatan_pembeli']);

    $update_query = "UPDATE produksi SET catatan_pembeli = '$catatan_pembeli' WHERE id_order = '$id_order'";
    $update_result = mysqli_query($conn, $update_query);

    if (!$update_result) {
        die('Update Error: ' . mysqli_error($conn));
    } else {
        echo "<script>alert('Catatan berhasil diperbarui'); window.location.href = 'riwayat.php';</script>";
    }
}
?>

<div class="container" style="padding-bottom: 300px;">
    <h2 style="width: 100%; border-bottom: 4px solid #ff8680"><b>Riwayat Pesanan Saya</b></h2>
    <table class="table table-striped">
        <tr>
            <th>No</th>
            <th>Invoice</th>
            <th>Kode Produk</th>
            <th>Nama Produk</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Total</th>
            <th>Catatan Pembeli</th>
            <th>Catatan Penjual</th>
        </tr>
        <?php 
        $no = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td><?= $no; ?></td>
                <td><?= htmlspecialchars($row['invoice']); ?></td>
                <td><?= htmlspecialchars($row['kode_produk']); ?></td>
                <td><?= htmlspecialchars($row['nama_produk']); ?></td>
                <td><?= htmlspecialchars($row['qty']); ?></td>
                <td>Rp.<?= number_format($row['harga']); ?></td>
                <td><?= htmlspecialchars($row['status']); ?></td>
                <td><?= htmlspecialchars($row['tanggal']); ?></td>
                <td>Rp. <?= htmlspecialchars($row['grand_total']); ?></td>
                <td>
                    <button class="btn btn-primary btn-edit" data-id="<?= $row['id_order']; ?>" data-catatan="<?= htmlspecialchars($row['catatan_pembeli']); ?>">
                       Catatan
                    </button>
                </td>
                <td><?= htmlspecialchars($row['catatan_penjual']); ?></td>
                
            </tr>
            <?php 
            $no++;
        }
        ?>
    </table>
</div>

<!-- Modal untuk mengedit catatan pembeli -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editForm" method="POST" action="riwayat.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Catatan Pembeli</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_order" id="id_order">
                    <div class="form-group">
                        <label for="catatan_sebelumnya">Catatan Sebelumnya</label>
                        <textarea class="form-control" id="catatan_sebelumnya" rows="3" readonly></textarea>
                    </div>
                    <div class="form-group">
                        <label for="catatan_pembeli">Catatan Baru</label>
                        <textarea class="form-control" id="catatan_pembeli" name="catatan_pembeli" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var editButtons = document.querySelectorAll('.btn-edit');
    var editModal = document.getElementById('editModal');
    var idOrderInput = document.getElementById('id_order');
    var catatanSebelumnyaTextarea = document.getElementById('catatan_sebelumnya');
    var catatanPembeliTextarea = document.getElementById('catatan_pembeli');

    editButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var idOrder = this.getAttribute('data-id');
            var catatanSebelumnya = this.getAttribute('data-catatan');

            idOrderInput.value = idOrder;
            catatanSebelumnyaTextarea.value = catatanSebelumnya;

            $(editModal).modal('show');
        });
    });
});
</script>

<?php
include 'footer.php';
?>
