<?php 
include 'header.php';
$sortage = mysqli_query($conn, "SELECT * FROM produksi WHERE cek = '1'");
$cek_sor = mysqli_num_rows($sortage);
$nama_material = array();  // Inisialisasi variabel sebagai array kosong
?>

<div class="container">
	<h2 style="width: 100%; border-bottom: 4px solid gray"><b>Daftar Pesanan</b></h2>
	<br>
	<h5 class="bg-success" style="padding: 7px; width: 710px; font-weight: bold;">
		<marquee>Lakukan Reload Setiap Masuk Halaman ini, untuk menghindari terjadinya kesalahan data dan informasi</marquee>
	</h5>
	<a href="produksi.php" class="btn btn-default"><i class="glyphicon glyphicon-refresh"></i> Reload</a>
	<br>
	<table class="table table-striped">
		<thead>
			<tr>
				<th scope="col">No</th>
				<th scope="col">Invoice</th>
				<th scope="col">Kode Customer</th>
				<th scope="col">Status</th>
				<th scope="col">Tanggal</th>
				<th scope="col">Catatan Penjual</th>
				<th scope="col">Action</th>
			</tr>
		</thead>
		<tbody>

			<?php 
			$result = mysqli_query($conn, "SELECT invoice, GROUP_CONCAT(DISTINCT kode_customer) AS kode_customer, GROUP_CONCAT(DISTINCT tanggal) AS tanggal, GROUP_CONCAT(DISTINCT status) AS status, GROUP_CONCAT(DISTINCT kode_produk) AS kode_produk, GROUP_CONCAT(DISTINCT qty) AS qty, GROUP_CONCAT(DISTINCT terima) AS terima, GROUP_CONCAT(DISTINCT tolak) AS tolak, GROUP_CONCAT(DISTINCT cek) AS cek, GROUP_CONCAT(DISTINCT catatan_penjual) AS catatan_penjual FROM produksi GROUP BY invoice ORDER BY invoice DESC");
			if (!$result) {
				die('Query Error: ' . mysqli_error($conn));
			}
			$no = 1;
			while($row = mysqli_fetch_assoc($result)){
				$kodep = explode(",", $row['kode_produk'])[0];
				$inv = $row['invoice'];
				?>

				<tr>
					<td><?= $no; ?></td>
					<td><?= $row['invoice']; ?></td>
					<td><?= explode(",", $row['kode_customer'])[0]; ?></td>
					<?php if(explode(",", $row['terima'])[0] == 1){ ?>
						<td style="color: green;font-weight: bold;">Pesanan Diterima (Siap Kirim)
							<?php
						}else if(explode(",", $row['tolak'])[0] == 1){
							?>
							<td style="color: red;font-weight: bold;">Pesanan Ditolak
								<?php 
							}
							if(explode(",", $row['terima'])[0] == 0 && explode(",", $row['tolak'])[0] == 0){
								?>
								<td style="color: orange;font-weight: bold;"><?= explode(",", $row['status'])[0]; ?>
								<?php 
							}
							$t_bom = mysqli_query($conn, "SELECT * FROM bom_produk WHERE kode_produk = '$kodep'");
							if (!$t_bom) {
								die('Query Error: ' . mysqli_error($conn));
							}

							while($row1 = mysqli_fetch_assoc($t_bom)){
								$kodebk = $row1['kode_bk'];

								$inventory = mysqli_query($conn, "SELECT * FROM inventory WHERE kode_bk = '$kodebk'");
								if (!$inventory) {
									die('Query Error: ' . mysqli_error($conn));
								}
								$r_inv = mysqli_fetch_assoc($inventory);

								$kebutuhan = $row1['kebutuhan'];	
								$qtyorder = explode(",", $row['qty'])[0];
								$inventory_qty = $r_inv['qty'];

								$bom = ($kebutuhan * $qtyorder);
								$hasil = $inventory_qty - $bom;
								if($hasil < 0 && explode(",", $row['tolak'])[0] == 0){
									$nama_material[] = $r_inv['nama'];
									mysqli_query($conn, "UPDATE produksi SET cek = '1' WHERE invoice = '$inv'");
									?>

									<?php 
								}
							}
							?>
						</td>
						<td><?= explode(",", $row['tanggal'])[0]; ?></td>
						<td>
							<button type="button" class="btn btn-info" data-toggle="modal" data-target="#catatanModal" data-invoice="<?= $row['invoice']; ?>" data-catatan="<?= explode(",", $row['catatan_penjual'])[0]; ?>">Catatan</button>
						</td>
						<td>
							<?php if( explode(",", $row['tolak'])[0]==0 && explode(",", $row['cek'])[0]==1 && explode(",", $row['terima'])[0]==0){ ?>
								<a href="inventory.php?cek=0" id="rq" class="btn btn-warning"><i class="glyphicon glyphicon-warning-sign"></i> Request Material Shortage</a> 
								<a href="proses/tolak.php?inv=<?= $row['invoice']; ?>" class="btn btn-danger" onclick="return confirm('Yakin Ingin Menolak ?')"><i class="glyphicon glyphicon-remove-sign"></i> Tolak</a> 
							<?php }else if(explode(",", $row['terima'])[0] == 0 && explode(",", $row['cek'])[0]==0){ ?>

								<a href="proses/terima.php?inv=<?= $row['invoice']; ?>&kdp=<?= $row['kode_produk']; ?>" class="btn btn-success"><i class="glyphicon glyphicon-ok-sign"></i> Terima</a> 
								<a href="proses/tolak.php?inv=<?= $row['invoice']; ?>" class="btn btn-danger" onclick="return confirm('Yakin Ingin Menolak ?')"><i class="glyphicon glyphicon-remove-sign"></i> Tolak</a> 
							<?php } ?>

							<a href="detailorder.php?inv=<?= $row['invoice']; ?>&cs=<?= explode(",", $row['kode_customer'])[0]; ?>" type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-eye-open"></i> Detail Pesanan</a>
						</td>
					</tr>
					<?php
					$no++; 
				}
				?>

			</tbody>
		</table>

		<?php 
if($cek_sor > 0){
 ?>
	<br>
	<br>
	<div class="row">
		<div class="col-md-4 bg-danger" style="padding:10px;">
			<h4>Kekurangan Material </h4>
			<h5 style="color: red;font-weight: bold;">Silahkan Tambah Stok Material dibawah ini : </h5>
			<table class="table table-striped">
				<tr>
					<th>No</th>
					<th>Material</th>
				</tr>
	<?php 
	$arr = array_values(array_unique($nama_material));
	for ($i=0; $i < count($arr); $i++) { 

	 ?>
				<tr>
					<td><?= $i+1 ?></td>
					<td><?= $arr[$i]; ?></td>
				</tr>
	<?php } ?>
			</table>
		</div>
	</div>
<?php 
}
 ?>

	</div>

	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>

<!-- Modal -->
<div class="modal fade" id="catatanModal" tabindex="-1" role="dialog" aria-labelledby="catatanModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="catatanModalLabel">Catatan Penjual</h4>
            </div>
            <div class="modal-body">
                <form id="catatanForm" method="POST" action="">
                    <input type="hidden" name="invoice" id="invoice">
                    <div class="form-group">
                        <label for="catatan" class="control-label">Catatan:</label>
                        <textarea class="form-control" id="catatan" name="catatan"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="catatan_sebelumnya" class="control-label">Catatan Sebelumnya:</label>
                        <p id="catatan_sebelumnya"></p>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Catatan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
include 'footer.php';
?>

<script>
$(document).ready(function() {
    $('#catatanModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) 
        var invoice = button.data('invoice') 
        var catatan = button.data('catatan') 

        var modal = $(this)
        modal.find('.modal-title').text('Catatan Penjual untuk Invoice ' + invoice)
        modal.find('#invoice').val(invoice)
        modal.find('#catatan').val(catatan)
        modal.find('#catatan_sebelumnya').text(catatan)
    })

    $('#catatanForm').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.post('proses/update_catatan.php', formData, function(response) {
            alert(response.message);
            if (response.success) {
                window.location.reload();
            }
        }, 'json');
    });
});
</script>
