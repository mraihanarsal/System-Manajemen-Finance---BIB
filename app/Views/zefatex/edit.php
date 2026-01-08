<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Transaksi Zefatex
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center mb-4">
    <a href="<?= base_url('zefatex') ?>" class="btn btn-secondary btn-sm me-3">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    <h1 class="h3 mb-0 text-gray-800 fw-bold ml-3">Edit Transaksi</h1>
</div>

<form action="<?= base_url('zefatex/update/' . $trx['id']) ?>" method="post" enctype="multipart/form-data">
    
    <!-- HEADER -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Informasi Invoice</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>No. Invoice <span class="text-danger">*</span></label>
                        <input type="text" name="invoice_number" class="form-control" value="<?= esc($trx['invoice_number']) ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Bill To (Customer) <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" class="form-control" value="<?= esc($trx['customer_name']) ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Invoice <span class="text-danger">*</span></label>
                        <input type="date" name="transaction_date" class="form-control" value="<?= $trx['transaction_date'] ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6">
                     <div class="form-group">
                        <label>Ganti Foto Invoice (Opsional)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto.</small>
                    </div>
                </div>
                 <div class="col-md-6">
                    <?php if(!empty($trx['image_path'])): ?>
                        <label>Foto Saat Ini:</label><br>
                        <a href="<?= base_url('uploads/zefatex/' . $trx['image_path']) ?>" target="_blank">
                            <img src="<?= base_url('uploads/zefatex/' . $trx['image_path']) ?>" class="img-thumbnail" style="height: 100px;">
                        </a>
                    <?php else: ?>
                        <span class="text-muted fst-italic">Tidak ada foto.</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ITEMS -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-dark">Item Transaksi</h6>
            <button type="button" class="btn btn-success btn-sm" id="btnAddItem">
                <i class="fas fa-plus"></i> Tambah Item
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tblItems">
                    <thead class="bg-light">
                        <tr>
                            <th>Description</th>
                            <th width="100">Qty</th>
                            <th width="200">Price (IDR)</th>
                            <th width="200">Amount (IDR)</th>
                            <th width="50">#</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyItems">
                        <?php if(empty($items)): ?>
                             <tr class="item-row">
                                <td><input type="text" name="item_desc[]" class="form-control" required></td>
                                <td><input type="number" name="item_qty[]" class="form-control qty" value="1" step="0.01" required></td>
                                <td><input type="text" name="item_price[]" class="form-control price" value="Rp 0" required></td>
                                <td><input type="text" class="form-control amount" value="0" readonly></td>
                                <td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($items as $item): ?>
                            <tr class="item-row">
                                <td><input type="text" name="item_desc[]" class="form-control" value="<?= esc($item['description']) ?>" required></td>
                                <td><input type="number" name="item_qty[]" class="form-control qty" value="<?= $item['qty'] ?>" step="0.01" required></td>
                                <td><input type="text" name="item_price[]" class="form-control price" value="Rp <?= number_format($item['price'], 0, ',', '.') ?>" required></td>
                                <td><input type="text" class="form-control amount" value="Rp <?= number_format($item['amount'], 0, ',', '.') ?>" readonly></td>
                                <td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                         <tr>
                            <td colspan="3" class="text-end fw-bold">TOTAL PEMASUKAN</td>
                            <td colspan="2">
                                <input type="text" id="grandTotal" class="form-control fw-bold text-success" value="Rp <?= number_format($trx['total_amount'], 0, ',', '.') ?>" readonly>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

             <div class="form-group mt-3">
                <label>Catatan Tambahan (Opsional)</label>
                <textarea name="note" class="form-control" rows="2"><?= esc($trx['description']) ?></textarea>
            </div>
        </div>
        <div class="card-footer bg-white text-end">
             <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                 <i class="fas fa-save me-2"></i> Update Transaksi
             </button>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        
        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
        }

        function addRow() {
             let row = `
                <tr class="item-row">
                    <td><input type="text" name="item_desc[]" class="form-control" placeholder="Deskripsi Item" required></td>
                    <td><input type="number" name="item_qty[]" class="form-control qty" value="1" step="0.01" min="0" required></td>
                    <td><input type="text" name="item_price[]" class="form-control price" value="Rp 0" required></td>
                    <td><input type="text" class="form-control amount" value="0" readonly></td>
                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove"><i class="fas fa-trash"></i></button></td>
                </tr>
            `;
            $('#tbodyItems').append(row);
        }

        $('#btnAddItem').click(function() { addRow(); });

        $(document).on('click', '.btn-remove', function() {
            if ($('.item-row').length > 1) { 
                $(this).closest('tr').remove(); 
                calcTotal(); 
            } else { 
                alert('Minimal satu item harus ada.'); 
            }
        });

        $(document).on('input', '.qty, .price', function() {
            let row = $(this).closest('tr');
            
            // Format Price if updated
            if($(this).hasClass('price')) {
                 $(this).val(formatRupiah($(this).val(), 'Rp'));
            }

            let qty = parseFloat(row.find('.qty').val()) || 0;
            
            // Parse Price
            let priceRaw = row.find('.price').val();
            let price = parseInt(priceRaw.replace(/[^0-9]/g, '')) || 0;
            
            let amount = qty * price;
            
            row.find('.amount').val("Rp " + amount.toLocaleString('id-ID'));
            calcTotal();
        });

        // Add cleaning on Post, but standard form submit sends the value as is.
        // We need to clean comma/dots before submit OR clean it in Controller.
        // User requested UI change. Controller might expect float.
        // Let's hook form submit to clean inputs? Or better, let Controller handle "Rp 50.000" string?
        // ZefatexController::update method likely expects 'item_price' array.
        // If I change 'item_price' to "Rp 50.000", validation might fail if it expects numeric.
        // It's safer to strip formatting before submit.
        
        $('form').submit(function() {
            $('.price').each(function() {
                let clean = $(this).val().replace(/[^0-9]/g, ''); // 50000
                $(this).val(clean);
            });
            // also amount? amount is not submitted usually, or ignored.
        });

        function calcTotal() {
            let total = 0;
            $('.item-row').each(function() {
                let qty = parseFloat($(this).find('.qty').val()) || 0;
                let priceRaw = $(this).find('.price').val();
                let price = parseInt(priceRaw.replace(/[^0-9]/g, '')) || 0;
                total += (qty * price);
            });
            $('#grandTotal').val("Rp " + total.toLocaleString('id-ID'));
        }
    });
</script>
<?= $this->endSection() ?>
