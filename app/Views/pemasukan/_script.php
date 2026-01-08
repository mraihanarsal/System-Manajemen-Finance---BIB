<script>
$(document).ready(function() {
    const rowsPerPage = 10;
    let allData = [];
    let currentPage = 1;
    let totalRows = 0;
    let tahunSekarang = new Date().getFullYear();
    let tahunAktif = tahunSekarang;

        
    $('#currentYear').text(tahunAktif);

    
    $('#prevYear').click(function() {
        tahunAktif--;
        $('#currentYear').text(tahunAktif);
        loadData();
        loadTotal();
    });

    $('#nextYear').click(function() {
        tahunAktif++;
        $('#currentYear').text(tahunAktif);
        loadData();
        loadTotal();
    });
 
    
    function formatRupiah(angka) {
        if (angka === null || angka === undefined || angka === '' || angka === 0) return "Rp 0";

        try {
            
            let numberStr = angka.toString().replace(',', '.'); 
            let numValue = parseFloat(numberStr);

            if (isNaN(numValue)) return "Rp 0";
            return "Rp " + numValue.toLocaleString("id-ID", {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });
        } catch (e) {
            console.error('Error formatting number:', e);
            return "Rp 0";
        }
    }

    
    function formatInputRupiah(angka) {
        if (!angka || angka === '0') return '';
        let clean = angka.toString().replace(/\D/g, '');
        if (clean === '' || clean === '0') return '';
        return clean.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    
    function parseRupiah(rupiah) {
        if (!rupiah) return '0';
        let clean = rupiah.toString().replace(/[^\d.,]/g, '').replace(/\./g, '').replace(',', '.');
        let num = parseFloat(clean);
        return isNaN(num) ? '0' : num.toString();
    }

    
    $(document).on('input', '.omset-input', function() {
        let value = $(this).val();
        $(this).val(formatInputRupiah(value));
    });

    
    function loadData(page = 1) {
        const bulan = $('#filterBulan').val();
        const tahun = tahunAktif;
        currentPage = page;

        $('#tabelPemasukan tbody').html('<tr><td colspan="6" class="text-center">Loading data...</td></tr>');

        $.ajax({
            url: "<?= site_url('pemasukan/getAll') ?>",
            type: 'GET',
            data: { 
                page: page, 
                limit: rowsPerPage, 
                bulan: bulan, 
                tahun: tahun 
            },
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data) {
                    allData = response.data;
                    totalRows = response.total || 0;
                    renderTable();
                    renderPagination();
                } else {
                    allData = [];
                    totalRows = 0;
                    renderTable();
                    renderPagination();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                $('#tabelPemasukan tbody').html('<tr><td colspan="6" class="text-center text-danger">Gagal memuat data</td></tr>');
                allData = [];
                totalRows = 0;
                renderPagination();
            }
        });
    }

    
    function loadTotal() {
        const bulan = $('#filterBulan').val();
        const tahun = tahunAktif;

        $.ajax({
            url: "<?= site_url('pemasukan/getTotal') ?>",
            type: 'GET',
            data: { 
                bulan: bulan, 
                tahun: tahun 
            },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('#totalPemasukan').text(formatRupiah(response.total));
                } else {
                    $('#totalPemasukan').text('Rp 0');
                }
            },
            error: function() {
                $('#totalPemasukan').text('Rp 0');
            }
        });
    }

    
    function renderTable() {
        let html = '';
        let no = (currentPage - 1) * rowsPerPage + 1;

        if (!allData || allData.length === 0) {
            $('#pesanKosong').show();
            $('#tabelPemasukan tbody').html('');
            return;
        }

        $('#pesanKosong').hide();
        
        allData.forEach(row => {
            html += `
              <tr>
                <td class="text-center">${no++}</td>
                <td>${row.nama_toko || '-'}</td>
                <td class="text-center">${getNamaBulan(row.bulan) || '-'}</td>
                <td class="text-center">${row.tahun || '-'}</td>
                <td class="text-end">${formatRupiah(row.omset)}</td>
                <td class="text-center">
                  <div class="d-flex justify-content-center" style="gap: 10px;">
                    <button class="btn btn-warning btn-sm btn-edit"
                            data-id="${row.id_pemasukan}"
                            data-nama="${row.nama_toko}"
                            data-bulan="${row.bulan}"
                            data-tahun="${row.tahun}"
                            data-omset="${row.omset}"
                            style="min-width: 80px; height: 32px;">
                            <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-sm btn-hapus"
                            data-id="${row.id_pemasukan}"
                            style="min-width: 80px; height: 32px;">
                            <i class="fas fa-trash"></i> Hapus
                    </button>
                  </div>
                </td>
              </tr>
            `;
        });

        $('#tabelPemasukan tbody').html(html);
    }

    
    function getNamaBulan(bulan) {
        const bulanList = [
            '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        return bulanList[bulan] || bulan;
    }

    
    function renderPagination() {
        let totalPages = Math.ceil(totalRows / rowsPerPage);
        let pagination = '';
        
        if (totalPages <= 1) {
            $('#pagination').html('');
            return;
        }

        pagination += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">&laquo;</a>
            </li>
        `;

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                pagination += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                pagination += `<li class="page-item disabled"><a class="page-link">...</a></li>`;
            }
        }

        pagination += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">&raquo;</a>
            </li>
        `;

        $('#pagination').html(pagination);
    }

    
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page && page >= 1) {
            loadData(page);
        }
    });

    $('#btnFilter').click(function() {
        currentPage = 1;
        loadData();
        loadTotal();
    });

    $('#btnReset').click(function() {
        $('#filterBulan').val('');
        tahunAktif = tahunSekarang;
        $('#currentYear').text(tahunAktif);
        currentPage = 1;
        loadData();
        loadTotal();
    });

    

    
    $('#formTambah').on('submit', function(e){
        e.preventDefault();
        
        const originalOmset = $('input[name="omset"]').val();
        const cleanOmset = parseRupiah(originalOmset);
        
        
        console.log('Tambah - Original:', originalOmset);
        console.log('Tambah - Parsed:', cleanOmset);
        
        if (cleanOmset === '0') {
            showAlert('error', 'Omset harus lebih dari 0');
            return;
        }
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        const formData = new FormData();
        formData.append('nama_toko', $('input[name="nama_toko"]').val());
        formData.append('bulan', $('select[name="bulan"]').val());
        formData.append('tahun', $('input[name="tahun"]').val());
        formData.append('omset', cleanOmset);
        
        $.ajax({
            url: "<?= site_url('pemasukan/tambah') ?>",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('#modalTambah').modal('hide');
                    $('#formTambah')[0].reset();
                    loadData(currentPage);
                    loadTotal();
                    showAlert('success', 'Data berhasil ditambahkan');
                } else {
                    showAlert('error', 'Gagal: ' + response.pesan);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error tambah:', error);
                showAlert('error', 'Error menyimpan data');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    
    $(document).on('click', '.btn-edit', function(){
        const id = $(this).data('id');
        const nama_toko = $(this).data('nama');
        const bulan = $(this).data('bulan');
        const tahun = $(this).data('tahun');
        const omset = $(this).data('omset');
        
        $('#old_nama_toko').text(nama_toko || '-');
        $('#old_bulan').text(getNamaBulan(bulan) || '-');
        $('#old_tahun').text(tahun || '-');
        $('#old_omset').text(formatRupiah(omset));
        $('#current_omset').text(formatRupiah(omset));
        
        $('#edit_id').val(id);
        $('#edit_nama_toko').val(nama_toko);
        $('#edit_bulan').val(bulan);
        $('#edit_tahun').val(tahun);
        let cleanOmsetValue = parseFloat(omset);
        if (isNaN(cleanOmsetValue)) cleanOmsetValue = 0;
        $('#edit_omset').val(formatInputRupiah(Math.round(cleanOmsetValue).toString()));
        
        $('#modalEdit').modal('show');
    });

    
    $('#formEdit').on('submit', function(e){
        e.preventDefault();
        
        const originalOmset = $('#edit_omset').val();
        const cleanOmset = parseRupiah(originalOmset);
        const id = $('#edit_id').val();
        
        
        console.log('Edit - Original:', originalOmset);
        console.log('Edit - Parsed:', cleanOmset);
        
        if (cleanOmset === '0') {
            showAlert('error', 'Omset harus lebih dari 0');
            return;
        }
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        const formData = new FormData();
        formData.append('nama_toko', $('#edit_nama_toko').val());
        formData.append('bulan', $('#edit_bulan').val());
        formData.append('tahun', $('#edit_tahun').val());
        formData.append('omset', cleanOmset);
        
        $.ajax({
            url: "<?= site_url('pemasukan/ubah') ?>/" + id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('#modalEdit').modal('hide');
                    loadData(currentPage);
                    loadTotal();
                    showAlert('success', 'Data berhasil diupdate');
                } else {
                    showAlert('error', 'Gagal: ' + (response.pesan || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error edit:', error);
                showAlert('error', 'Error mengubah data');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    
    $(document).on('click', '.btn-hapus', function(){
        const id = $(this).data('id');
        $('#hapus_id').val(id);
        $('#modalHapus').modal('show');
    });

    
    $('#formHapus').on('submit', function(e){
        e.preventDefault();
        
        const id = $('#hapus_id').val();
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
        
        $.ajax({
            url: "<?= site_url('pemasukan/hapus') ?>/" + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('#modalHapus').modal('hide');
                    loadData(currentPage);
                    loadTotal();
                    showAlert('success', 'Data berhasil dihapus');
                } else {
                    showAlert('error', 'Gagal: ' + response.pesan);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error hapus:', error);
                showAlert('error', 'Error menghapus data');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    function showAlert(type, message) {
        
        $('.alert').remove();
        
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show mx-3 mt-3" role="alert">
                <i class="fas ${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        
        $('#content').prepend(alertHtml);
        
        
        setTimeout(() => {
            $('.alert').alert('close');
        }, 4000);
    }

    
    loadData();
    loadTotal();
});
</script>