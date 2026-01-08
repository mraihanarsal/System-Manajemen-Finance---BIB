<!-- Modal Edit Data -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Edit Pengeluaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEdit">
                    <input type="hidden" name="id" id="editId">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Periode Tanggal</label>
                        <input type="date" class="form-control" name="periode" id="editPeriode" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori</label>
                        <select class="form-control" name="kategori_id" id="editKategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($kategori as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= esc($k['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" id="editDeskripsi" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nominal (Rp)</label>
                        <input type="text" class="form-control input-rupiah" name="nominal" id="editNominal" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnUpdate">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>