<?php 
include 'header.php';
include 'koneksi/koneksi.php';
$kd = mysqli_real_escape_string($conn, $_GET['kode_cs']);
$cs = mysqli_query($conn, "SELECT * FROM customer WHERE kode_customer = '$kd'");
$rows = mysqli_fetch_assoc($cs);
?>

<div class="container" style="padding-bottom: 200px">
    <h2 style=" width: 100%; border-bottom: 4px solid #ff8680"><b>Checkout</b></h2>
    <div class="row">
        <div class="col-md-6">
            <h4>Daftar Pesanan</h4>
            <table class="table table-striped">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Sub Total</th>
                </tr>
                <?php 
                $result = mysqli_query($conn, "SELECT * FROM keranjang WHERE kode_customer = '$kd'");
                $no = 1;
                $hasil = 0;
                while($row = mysqli_fetch_assoc($result)){
                    ?>
                    <tr>
                        <td><?= $no; ?></td>
                        <td><?= $row['nama_produk']; ?></td>
                        <td>Rp.<?= number_format($row['harga']); ?></td>
                        <td><?= $row['qty']; ?></td>
                        <td>Rp.<?= number_format($row['harga'] * $row['qty']);  ?></td>
                    </tr>
                    <?php 
                    $total = $row['harga'] * $row['qty'];
                    $hasil += $total;
                    $no++;
                }
                ?>
                <tr id="ongkir-row" style="display: none;">
                    <td colspan="4" style="text-align: right; font-weight: bold;">Ongkir</td>
                    <td id="ongkir-amount">Rp.0</td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align: right; font-weight: bold;">Grand Total = <span id="grand-total"><?= number_format($hasil); ?></span></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 bg-success">
            <h5>Pastikan Pesanan Anda Sudah Benar</h5>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6 bg-warning">
            <h5>Isi Form di bawah ini</h5>
        </div>
    </div>
    <br>
    <form action="proses/order.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="kode_cs" value="<?= $kd; ?>">
        <input type="hidden" id="total-hidden" name="total" value="<?= $hasil; ?>">
        <input type="hidden" id="ongkir-hidden" name="ongkir" value="0">
        <input type="hidden" id="grand-total-hidden" name="grand_total" value="<?= $hasil; ?>">
        <div class="form-group">
            <label for="exampleInputEmail1">Nama</label>
            <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Nama" name="nama" style="width: 557px;" value="<?= $rows['nama']; ?>" readonly>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="exampleInputPassword1">Provinsi</label>
                    <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Provinsi" name="prov" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="exampleInputPassword1">Kota</label>
                    <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Kota" name="kota" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="exampleInputPassword1">Alamat</label>
                    <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Alamat" name="almt" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="exampleInputPassword1">Kode Pos</label>
                    <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Kode Pos" name="kopos" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="pengiriman">Metode Pengiriman</label>
            <select class="form-control" id="pengiriman" name="pengiriman" required onchange="toggleOngkir()">
                <option value="Dijemput">Dijemput</option>
                <option value="Diantar">Diantar</option>
            </select>
        </div>
        <div class="form-group" id="ongkir-form" style="display: none;">
            <label for="ongkir">Ongkir</label>
            <select class="form-control" id="ongkir" name="ongkir_select" onchange="updateOngkir()">
                <option value="10000">Dalam Kota - Rp 10.000</option>
                <option value="20000">Luar Kota - Rp 20.000</option>
            </select>
        </div>
        <div class="form-group">
            <label for="bukti_pengiriman">Upload Bukti Pengiriman</label>
            <input type="file" class="form-control-file" id="bukti_pengiriman" name="bukti_pengiriman" required>
        </div>
        <button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-shopping-cart"></i> Order Sekarang</button>
        <a href="keranjang.php" class="btn btn-danger">Cancel</a>
    </form>
</div>

<script>
function toggleOngkir() {
    var pengiriman = document.getElementById('pengiriman').value;
    var ongkirForm = document.getElementById('ongkir-form');
    var ongkirRow = document.getElementById('ongkir-row');
    var ongkirAmount = document.getElementById('ongkir-amount');
    var totalHidden = document.getElementById('total-hidden').value;
    var grandTotalElement = document.getElementById('grand-total');
    var grandTotalHidden = document.getElementById('grand-total-hidden');

    if (pengiriman === 'Diantar') {
        ongkirForm.style.display = 'block';
        ongkirRow.style.display = 'table-row';

        var ongkir = document.getElementById('ongkir').value;
        ongkirAmount.textContent = 'Rp.' + new Intl.NumberFormat().format(ongkir);
        var grandTotal = parseInt(totalHidden) + parseInt(ongkir);
        grandTotalElement.textContent = new Intl.NumberFormat().format(grandTotal);
        grandTotalHidden.value = grandTotal;
        document.getElementById('ongkir-hidden').value = ongkir;
    } else {
        ongkirForm.style.display = 'none';
        ongkirRow.style.display = 'none';

        var grandTotal = parseInt(totalHidden);
        grandTotalElement.textContent = new Intl.NumberFormat().format(grandTotal);
        grandTotalHidden.value = grandTotal;
        ongkirAmount.textContent = 'Rp.0';
        document.getElementById('ongkir-hidden').value = 0;
    }
}

function updateOngkir() {
    var ongkir = document.getElementById('ongkir').value;
    var ongkirAmount = document.getElementById('ongkir-amount');
    var totalHidden = document.getElementById('total-hidden').value;
    var grandTotalElement = document.getElementById('grand-total');
    var grandTotalHidden = document.getElementById('grand-total-hidden');

    var grandTotal = parseInt(totalHidden) + parseInt(ongkir);
    grandTotalElement.textContent = new Intl.NumberFormat().format(grandTotal);
    grandTotalHidden.value = grandTotal;

    ongkirAmount.textContent = 'Rp.' + new Intl.NumberFormat().format(ongkir);
    document.getElementById('ongkir-hidden').value = ongkir;
}
</script>

<?php 
include 'footer.php';
?>
