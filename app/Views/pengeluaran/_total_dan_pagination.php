<div class="row align-items-center">
    <div class="col-md-6 mb-3 mb-md-0">
        <div class="d-flex align-items-center">
            <span class="text-muted small text-uppercase fw-bold me-2">Total:</span>
            <span class="h5 fw-bold text-dark mb-0" id="grandTotal">Rp 0</span>
        </div>
    </div>

    <div class="col-md-6">
        <div class="d-flex justify-content-md-end justify-content-center align-items-center gap-2">
             <!-- Rows Selector -->
             <select class="form-select form-select-sm" id="rowsPerPage" style="width: auto;">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
             </select>

             <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0" id="pagination">
                    <!-- Pagination populated by JS -->
                </ul>
            </nav>
        </div>
    </div>
</div>