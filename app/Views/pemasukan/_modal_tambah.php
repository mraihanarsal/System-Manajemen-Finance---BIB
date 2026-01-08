<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <form id="formTambah" method="post">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Data Pemasukan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label">Nama Toko</label>
            <input type="text" name="nama_toko" class="form-control form-control-sm" required placeholder="Masukkan nama toko">
          </div>
          <div class="mb-2">
            <label class="form-label">Bulan</label>
            <select name="bulan" class="form-control form-control-sm" required>
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
          <div class="mb-2">
            <label class="form-label">Tahun</label>
            <input type="number" name="tahun" class="form-control form-control-sm" min="2000" max="2030" required placeholder="Masukkan tahun">
          </div>
          <div class="mb-2">
            <label class="form-label">Omset</label>
            <input type="text" name="omset" class="form-control form-control-sm" placeholder="Masukkan nilai omset" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success btn-sm">
            <i class="fas fa-save"></i> Simpan
          </button>
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times"></i> Batal
          </button>
        </div>
      </form>
    </div>
  </div>
</div>