<style>
/* Tabel rapi */
.table-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

/* HEADER TABEL - Warna abu-abu */
.table thead th {
    background-color: #6c757d !important; /* Abu-abu */
    border-bottom: 2px solid #333;
    font-weight: 600;
    padding: 12px 8px;
    white-space: nowrap;
    border: 1px solid #333 !important;
    color: white !important; /* Teks putih agar kontras */
}

.table td {
    padding: 10px 8px;
    vertical-align: middle;
    border: 1px solid #333 !important;
    color: #111;
}

/* Warna baris data - putih kekuningan */
.table tbody tr {
    background-color: #FFFDE7 !important; /* Putih kekuningan untuk semua baris data */
}

/* Hover effect untuk baris data */
.table tbody tr:hover {
    background-color: #e3f2fd !important; /* Biru muda saat hover */
    transition: background-color 0.2s ease;
}

/* Kolom aksi dengan spacing yang konsisten */
.btn-group {
    display: flex;
    justify-content: center;
    gap: 8px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
    border-radius: 4px;
}

/* Kolom deskripsi dengan ellipsis */
.table td:nth-child(4) {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Kolom nominal rata kanan */
.table td:nth-child(5) {
    text-align: right;
    font-weight: 500;
}

/* Total card styling */
.total-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.total-card h5 {
    margin: 0;
    font-size: 14px;
    opacity: 0.9;
}

.total-card h3 {
    margin: 5px 0 0 0;
    font-weight: 700;
}

/* Pagination styling */
.pagination {
    margin: 20px 0 0 0;
}

.page-link {
    border-radius: 4px;
    margin: 0 2px;
    border: 1px solid #dee2e6;
}

.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}

/* Loading state */
.loading {
    opacity: 0.6;
    pointer-events: none;
}
</style>