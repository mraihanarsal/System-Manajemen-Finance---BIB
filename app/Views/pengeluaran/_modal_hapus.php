<div class="modal fade" id="modalHapus" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formHapus">
        <input type="hidden" id="hapusId" name="id">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Hapus Data</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="fw-bold">Apakah Anda yakin ingin menghapus data pengeluaran ini?</p>
          <p class="text-secondary small">Data yang sudah dihapus tidak dapat dikembalikan lagi.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger" id="btnKonfirmasiHapus">
            <i class="fas fa-trash"></i> Ya, Hapus
          </button>
        </div>
      </form>
    </div>
  </div>
</div>