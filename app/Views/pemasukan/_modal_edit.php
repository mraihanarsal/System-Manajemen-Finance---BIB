<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog modal-lg"> 
    <div class="modal-content">
      <form id="formEdit" method="post">
        <div class="modal-header">
          <h5 class="modal-title">Edit Data Pemasukan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="edit_id" id="edit_id">
          
          <!-- Data Lama -->
          <div class="card mb-3 border-warning">
            <div class="card-header bg-warning text-dark py-2">
              <h6 class="mb-0"><i class="fas fa-history"></i> Data Sebelumnya</h6>
            </div>
            <div class="card-body py-2">
              <div class="row small">
                <div class="col-md-3">
                  <strong>Nama Toko:</strong><br>
                  <span id="old_nama_toko" class="text-muted">-</span>
                </div>
                <div class="col-md-2">
                  <strong>Bulan:</strong><br>
                  <span id="old_bulan" class="text-muted">-</span>
                </div>
                <div class="col-md-2">
                  <strong>Tahun:</strong><br>
                  <span id="old_tahun" class="text-muted">-</span>
                </div>
                <div class="col-md-5">
                  <strong>Omset:</strong><br>
                  <span id="old_omset" class="text-muted">-</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Form Edit -->
          <div class="row">
            <div class="col-md-6 mb-2">
              <label class="form-label">Nama Toko</label>
              <input type="text" name="edit_nama_toko" id="edit_nama_toko" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-3 mb-2">
              <label class="form-label">Bulan</label>
              <select name="edit_bulan" id="edit_bulan" class="form-control form-control-sm" required>
                <option value="">Pilih Bulan</option>
                <option value="1">Januari</option>
                <option value="2">Februari</option>
                <option value="3">Maret</option>
                <option value="4">April</option>
                <option value="5">Mei</option>
                <option value="6">Juni</option>
                <option value="7">Juli</option>
                <option value="8">Agustus</option>
                <option value="9">September</option>
                <option value="10">Oktober</option>
                <option value="11">November</option>
                <option value="12">Desember</option>
              </select>
            </div>
            <div class="col-md-3 mb-2">
              <label class="form-label">Tahun</label>
              <input type="number" name="edit_tahun" id="edit_tahun" class="form-control form-control-sm" min="2000" max="2030" required>
            </div>
          </div>
          <div class="mb-2">
            <label class="form-label">Omset Baru (Rp)</label>
            <input type="text" name="edit_omset" id="edit_omset" class="form-control form-control-sm omset-input" placeholder="Contoh: 2.500.000.000" required>
            <small class="text-muted">Data lama: <span id="current_omset" class="text-warning fw-bold">-</span></small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning btn-sm">
            <i class="fas fa-save"></i> Update Data
          </button>
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times"></i> Batal
          </button>
        </div>
      </form>
    </div>
  </div>
</div>