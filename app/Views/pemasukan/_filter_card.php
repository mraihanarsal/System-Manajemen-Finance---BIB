<!-- FILTER CARD -->
<div class="card mb-3 shadow-sm border-0">
  <div class="card-body">
    <div class="row g-3 align-items-end">
      <!-- Pilih Bulan -->
      <div class="col-md-4 mb-4 mt-4 mt-md-0">
        <label for="filterBulan" class="form-label fw-bold">Pilih Bulan</label>
        <select id="filterBulan" class="form-select">
          <option value=""> Semua Bulan </option>
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

      <!-- Pilih Tahun -->
<div class="col-md-4 mb-4 mt-4 mt-md-0">
  <div class="d-flex align-items-center justify-content-center gap-3">
    <label for="currentYear" class="form-label fw-bold mb-0 mr-1">Pilih Tahun</label>
    
    <button id="prevYear" 
            class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center" 
            style="width: 36px; height: 36px;">
      <i class="fas fa-chevron-left"></i>
    </button>
    
    <h5 id="currentYear" class="mb-0 fw-bold mx-3"></h5>
    
    <button id="nextYear" 
            class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center" 
            style="width: 36px; height: 36px;">
      <i class="fas fa-chevron-right"></i>
    </button>
  </div>
</div>

<!-- Tombol Aksi -->
<div class="col-md-4 mb-4 mt-4 mt-md-0">
  <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 5px;">

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
      <i class="fas fa-plus"></i> Tambah
    </button>

    <button id="btnFilter" class="btn btn-success">
      <i class="fas fa-filter"></i> Filter
    </button>

    <button id="btnReset" class="btn btn-secondary">
      <i class="fas fa-undo"></i> Reset
    </button>
  </div>
</div>