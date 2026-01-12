<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">

    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Keuangan</h1>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4 border-left-primary">
        <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-2"></i>Filter Laporan Keuangan</h6>
        </div>
        <div class="card-body">
            <form id="filterForm">
                <div class="row">
                    <!-- 1. Jenis Filter -->
                    <div class="col-md-6 mb-3">
                        <label for="filter_type" class="small font-weight-bold text-gray-700">Jenis Filter</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-list text-gray-500"></i></span>
                            </div>
                            <select class="form-control border-left-0 bg-light" id="filter_type" name="filter_type" style="height: 45px;">
                                <option value="">Semua Periode</option>
                                <option value="year">Per Tahun</option>
                                <option value="date_range">Rentang Tanggal</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- 2. Dynamic Inputs -->
                    <div class="col-md-6 mb-3">
                         <!-- Placeholder -->
                        <div id="empty_input" class="form-control border-0 bg-transparent" style="height: 45px;"></div>

                        <!-- Year Input -->
                        <div id="year_input" class="d-none transition-fade">
                            <label for="year" class="small font-weight-bold text-gray-700">Pilih Tahun</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-calendar-year text-gray-500"></i></span>
                                </div>
                                <select class="form-control border-left-0 bg-light" id="year" name="year" style="height: 45px;">
                                    <?php 
                                    $currYear = date('Y');
                                    for($i = $currYear; $i >= 2020; $i--) {
                                        echo "<option value='$i'>$i</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Date Range Input -->
                        <div class="d-none range-input transition-fade">
                            <label class="small font-weight-bold text-gray-700">Rentang Tanggal</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="date" class="form-control bg-light" id="start_date" name="start_date" style="height: 45px;">
                                </div>
                                <div class="col-6">
                                    <input type="date" class="form-control bg-light" id="end_date" name="end_date" style="height: 45px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- 3. Actions -->
                    <div class="col-12 text-right">
                         <button type="button" id="btnFilter" class="btn btn-primary shadow-sm mr-2" style="height: 45px; min-width: 150px;">
                            <i class="fas fa-search mr-2"></i> Tampilkan
                        </button>
                        <a href="/laporan_pdf" id="btnDownloadPdf" target="_blank" class="btn btn-danger shadow-sm" style="height: 45px; min-width: 150px;">
                            <i class="fas fa-file-pdf mr-2"></i> Download PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <style>
        .transition-fade {
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #4e73df;
            background-color: #fff !important;
        }
        .input-group-text {
            color: #6e707e;
        }
    </style>

    <!-- DataTables -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Rekapitulasi Pemasukan & Pengeluaran</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tblLaporan" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center align-middle" width="5%">No.</th>
                            <th class="text-center align-middle" width="15%">Bulan</th>
                            <th class="text-center align-middle" width="10%">Tahun</th>
                            <th class="text-center align-middle">Pemasukan (Rp)</th>
                            <th class="text-center align-middle">Pengeluaran (Rp)</th>
                            <th class="text-center align-middle">Bersih (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data populated via AJAX -->
                    </tbody>
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <th colspan="3" class="text-end">TOTAL</th>
                            <th class="text-end text-success" id="totalMasuk">0</th>
                            <th class="text-end text-danger" id="totalKeluar">0</th>
                            <th class="text-end text-primary" id="totalBersih">0</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- CSS DataTables -->
<link href="<?= base_url('vendor/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- JS DataTables -->
<script src="<?= base_url('vendor/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('vendor/datatables/dataTables.bootstrap4.min.js') ?>"></script>

<script>
$(document).ready(function() {
    function formatRupiah(angka) {
        if (!angka) return "0";
        return parseInt(angka).toLocaleString("id-ID");
    }

    // Handle Filter Type Change
    $('#filter_type').change(function() {
        var type = $(this).val();
        
        // Reset visibility
        $('#year_input').addClass('d-none');
        $('.range-input').addClass('d-none');
        $('#empty_input').addClass('d-none');
        
        if (type === 'year') {
            $('#year_input').removeClass('d-none');
        } else if (type === 'date_range') {
            $('.range-input').removeClass('d-none');
        } else {
            $('#empty_input').removeClass('d-none');
        }
        updatePdfLink();
    });

    // Initialize with default PDF link
    updatePdfLink();

    // Trigger Update on Input Change
    $('#year, #start_date, #end_date').change(function() {
        updatePdfLink();
    });

    function updatePdfLink() {
        var params = $('#filterForm').serialize();
        var url = "<?= site_url('laporan/download_pdf') ?>?" + params;
        $('#btnDownloadPdf').attr('href', url);
    } // Changed download_pdf to generate_pdf based on Controller method

    // Initialize DataTable
    var table = $('#tblLaporan').DataTable({
        "processing": true,
        "ajax": {
            "url": "<?= site_url('laporan/getData') ?>",
            "type": "GET",
            "data": function(d) {
                // Add filter params to AJAX request
                d.filter_type = $('#filter_type').val();
                d.year = $('#year').val();
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            },
            "dataSrc": function(json) {
                return json.data;
            }
        },
        "columns": [
            { 
                "data": null,
                "render": function (data, type, row, meta) {
                    return meta.row + 1;
                },
                "className": "text-center"
            },
            { "data": "nama_bulan", "className": "text-center" },
            { "data": "tahun", "className": "text-center" },
            { 
                "data": "pemasukan",
                "render": function(data) {
                    return '<div class="text-right fw-bold text-success">' + formatRupiah(data) + '</div>';
                }
            },
            { 
                "data": "pengeluaran",
                "render": function(data) {
                    return '<div class="text-right fw-bold text-danger">' + formatRupiah(data) + '</div>';
                }
            },
            { 
                "data": "bersih",
                 "render": function(data) {
                    var color = data >= 0 ? 'text-primary' : 'text-danger';
                    return '<div class="text-right fw-bold ' + color + '">' + formatRupiah(data) + '</div>';
                }
            }
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api();
 
            // Remove formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total Pemasukan
            var totalMasuk = api
                .column( 3 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                
            // Total Pengeluaran
            var totalKeluar = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

             // Total Bersih
            var totalBersih = api
                .column( 5 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( '#totalMasuk' ).html(formatRupiah(totalMasuk));
            $( '#totalKeluar' ).html(formatRupiah(totalKeluar));
            $( '#totalBersih' ).html(formatRupiah(totalBersih));
        }
    });

    // Filter Button Click
    $('#btnFilter').click(function() {
        table.ajax.reload();
    });
});
</script>
<?= $this->endSection() ?>
