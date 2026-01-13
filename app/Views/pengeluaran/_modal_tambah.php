<!-- Modal Tambah Data -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Pengeluaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light bg-opacity-10">
                <form id="formTambah">
                    <div class="mb-3">
                        <label class="form-label-premium">Tanggal Pengeluaran</label>
                        <input type="date" class="form-control form-control-premium" name="periode" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-premium">Kategori</label>
                        <select class="form-control form-control-premium" name="kategori_id" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($kategori as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= esc($k['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-premium">Deskripsi</label>
                        <textarea class="form-control form-control-premium" name="deskripsi" placeholder="Contoh: Beli Kertas Thermal" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-premium">Nominal (Rp)</label>
                        <input type="text" class="form-control form-control-premium input-rupiah" name="nominal" placeholder="0" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary-premium rounded-pill px-4" id="btnSimpan">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>