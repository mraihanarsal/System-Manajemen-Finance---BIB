<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-exclamation-triangle me-2"></i> Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form id="formHapus">
                <div class="modal-body p-4 text-center">
                    <input type="hidden" id="hapusId" name="id">
                    
                    <div class="mb-3">
                         <i class="fas fa-trash-alt fa-4x text-danger opacity-25"></i>
                    </div>
                    
                    <h5 class="fw-bold text-dark mb-2">Hapus Data Ini?</h5>
                    <p class="text-muted mb-0">
                        Apakah Anda yakin ingin menghapus data pengeluaran ini? 
                        <br>Data yang dihapus <strong>tidak dapat dikembalikan</strong>.
                    </p>
                </div>
                
                <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4" id="btnKonfirmasiHapus">
                        <i class="fas fa-trash me-1"></i> Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>