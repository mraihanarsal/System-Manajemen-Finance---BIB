<script>
document.addEventListener('DOMContentLoaded', function() {
    const rowsPerPage = 10;
    let allData = [];
    let currentPage = 1;
    let totalRows = 0; 

    // Format Rupiah untuk display
    function formatRupiah(angka) {
        if (!angka) return "Rp 0";
        return "Rp " + parseInt(angka).toLocaleString("id-ID");
    }

    // Format display nominal (dengan titik)
    function formatDisplayNominal(angka) {
        return parseInt(angka).toLocaleString('id-ID');
    }

    // Format Tanggal Indo (YYYY-MM-DD -> DD MMMM YYYY)
    function formatTanggal(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    }

    // Auto format input nominal
    $(document).on('input', '.input-rupiah', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value) {
            $(this).val(formatDisplayNominal(value));
        }
    });

    // Load Data dari server
    function loadData(page = 1) {
        const start = $('#filterStart').val();
        const end = $('#filterEnd').val();
        const cat = $('#filterKategori').val();
        currentPage = page;

        $('#tabelPengeluaran tbody').html('<tr><td colspan="6" class="text-center">Loading data...</td></tr>');

        $.ajax({
            url: "<?= site_url('pengeluaran/getAll') ?>",
            type: 'GET',
            data: { 
                page: currentPage, 
                limit: rowsPerPage, 
                start: start, 
                end: end,
                kategori_id: cat
            },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    allData = response.data || [];
                    totalRows = response.total || 0;
                    renderTable();
                    renderPagination();
                    loadTotal(); // Update total saat data berubah
                } else {
                    alert('Error: ' + (response.error || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                $('#tabelPengeluaran tbody').html('<tr><td colspan="6" class="text-center text-danger">Gagal memuat data</td></tr>');
            }
        });
    }

    // Load Total Pengeluaran
    function loadTotal() {
        const start = $('#filterStart').val();
        const end = $('#filterEnd').val();

        $.ajax({
            url: "<?= site_url('pengeluaran/getTotal') ?>",
            type: 'GET',
            data: { 
                start: start, 
                end: end 
            },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('#grandTotal').text(formatRupiah(response.total));
                } else {
                    $('#grandTotal').text('Rp 0');
                }
            },
            error: function() {
                $('#grandTotal').text('Rp 0');
            }
        });
    }

    // Render tabel
    function renderTable() {
        let html = '';
        let no = (currentPage - 1) * rowsPerPage + 1;

        if (allData.length === 0) {
            $('#tabelPengeluaran tbody').html('<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data pengeluaran pada periode ini.</td></tr>');
            return;
        }

        allData.forEach(row => {
            const deskripsi = row.deskripsi && row.deskripsi.length > 50 
                ? row.deskripsi.substring(0, 50) + '...' 
                : row.deskripsi;
            
            const namaKategori = row.nama_kategori || 'Lainnya'; 

            html += `
              <tr>
                <td class="text-center">${no++}</td>
                <td>${formatTanggal(row.periode)}</td>
                <td><span class="badge bg-secondary">${namaKategori}</span></td>
                <td title="${row.deskripsi || ''}">${deskripsi || '-'}</td>
                <td class="text-end font-monospace fw-bold text-dark">${formatRupiah(row.jumlah)}</td>
                <td class="text-center">
                  <div class="btn-group" role="group">
                    <button class="btn btn-warning btn-sm btn-edit"
                            data-id="${row.id}"
                            data-kategori="${row.kategori_id}"
                            data-deskripsi="${row.deskripsi}"
                            data-periode="${row.periode}"
                            data-nominal="${row.jumlah}">
                            <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm btn-hapus"
                            data-id="${row.id}">
                            <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            `;
        });

        $('#tabelPengeluaran tbody').html(html);
    }

    // Render pagination (Simple version)
    function renderPagination() {
        let totalPages = Math.ceil(totalRows / rowsPerPage);
        let pagination = '';
        
        if (totalPages <= 1) {
            $('#pagination').html('');
            return;
        }

        // Prev
        pagination += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${currentPage - 1}">&laquo;</a>
                       </li>`;

        // Logic simple: Show all pages if < 7, else show window
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);

        if (startPage > 1) pagination += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
        if (startPage > 2) pagination += `<li class="page-item disabled"><a class="page-link">...</a></li>`;

        for (let i = startPage; i <= endPage; i++) {
            pagination += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                           </li>`;
        }

        if (endPage < totalPages - 1) pagination += `<li class="page-item disabled"><a class="page-link">...</a></li>`;
        if (endPage < totalPages) pagination += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;

        // Next
        pagination += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${currentPage + 1}">&raquo;</a>
                       </li>`;

        $('#pagination').html(pagination);
    }

    // Event handlers
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (!page || page < 1) return;
        loadData(page);
    });

    $('#btnFilter').click(function() {
        currentPage = 1;
        loadData();
    });

    // === CRUD OPERATIONS ===

    // Tambah Data
    $('#btnSimpan').click(function() {
        $('#formTambah').submit();
    });

    $('#formTambah').submit(function(e){
        e.preventDefault();
        const submitBtn = $('#btnSimpan');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: "<?= site_url('pengeluaran/tambah') ?>",
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('#modalTambah').modal('hide');
                    $('#formTambah')[0].reset();
                    loadData();
                    $('input[name="nominal"]').val('');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.pesan,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    let msg = response.pesan;
                    if (response.messages) {
                        msg = JSON.stringify(response.messages);
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        html: msg // Use html for potential list
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error tambah:', xhr.responseText);
                let msg = 'Gagal menghubungi server.';
                if (xhr.responseJSON && xhr.responseJSON.messages) {
                     msg = Object.values(xhr.responseJSON.messages).join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                     msg = xhr.responseJSON.error;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    html: msg
                });
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('Simpan');
            }
        });
    });

    // Edit Data - Buka Modal
    $(document).on('click', '.btn-edit', function(){
        const id = $(this).data('id');
        const kategori = $(this).data('kategori');
        const deskripsi = $(this).data('deskripsi');
        const periode = $(this).data('periode'); // YYYY-MM-DD
        const nominal = $(this).data('nominal');
        
        // Format nominal
        const formattedNominal = formatDisplayNominal(nominal);
        
        // Isi form edit
        $('#editId').val(id);
        $('#editKategori').val(kategori);
        $('#editDeskripsi').val(deskripsi);
        $('#editPeriode').val(periode);
        $('#editNominal').val(formattedNominal);
        
        $('#modalEdit').modal('show');
    });

    // Edit Data - Submit
    $('#btnUpdate').click(function() {
        $('#formEdit').submit();
    });

    $('#formEdit').submit(function(e){
        e.preventDefault();
        const id = $('#editId').val();
        const submitBtn = $('#btnUpdate');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: "<?= site_url('pengeluaran/ubah') ?>/" + id,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('#modalEdit').modal('hide');
                    loadData(currentPage);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.pesan,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                     Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.pesan
                    });
                }
            },
            error: function(xhr, status, error) {
                let msg = 'Gagal menghubungi server.';
                 if (xhr.responseJSON && xhr.responseJSON.messages) {
                     msg = Object.values(xhr.responseJSON.messages).join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                     msg = xhr.responseJSON.error;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    html: msg
                });
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('Simpan Perubahan');
            }
        });
    });

    // Hapus Data
    $(document).on('click', '.btn-hapus', function(){
        const id = $(this).data('id');
        $('#hapusId').val(id);
        $('#modalHapus').modal('show');
    });

    $('#btnKonfirmasiHapus').click(function() {
        $('#formHapus').submit();
    });

    $('#formHapus').submit(function(e){
        e.preventDefault();
        const id = $('#hapusId').val();
        const submitBtn = $('#btnKonfirmasiHapus');
        submitBtn.prop('disabled', true).text('Menghapus...');
        
        $.ajax({
            url: "<?= site_url('pengeluaran/hapus') ?>/" + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('#modalHapus').modal('hide');
                    loadData();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.pesan,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.pesan
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal menghapus data'
                });
            },
            complete: function() {
                submitBtn.prop('disabled', false).text('Ya, Hapus');
            }
        });
    });

    // Initial Load
    loadData();
});
</script>