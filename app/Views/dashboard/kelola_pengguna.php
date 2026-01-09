<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- HEADER + BREADCRUMB -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-body">

                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="/dashboard" class="text-decoration-none">
                                <i class="fas fa-dashboard me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Kelola Pengguna</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Kelola Pengguna</h6>
            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#tambahUserModal">
                <i class="fas fa-plus"></i> Tambah User
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="text-center">
                                <img src="<?= base_url($user['foto'] ? 'uploads/profiles/' . $user['foto'] : 'img/undraw_profile_1.svg') ?>" 
                                     class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                            </td>
                            <td><?= esc($user['nama']) ?></td>
                            <td><?= esc($user['username']) ?></td>
                                <td>
                                    <?php 
                                        $role = !empty($user['role']) ? $user['role'] : 'user';
                                        $badgeClass = $role === 'admin' ? 'warning' : 'info';
                                    ?>
                                    <span class="badge badge-<?= $badgeClass ?>">
                                        <?= ucfirst($role) ?>
                                    </span>
                                </td>
                            <td>
                                <span class="badge badge-<?= $user['status'] === 'active' ? 'success' : 'danger' ?>">
                                    <?= ucfirst($user['status']) ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-edit" 
                                    data-id="<?= $user['id'] ?>"
                                    data-nama="<?= esc($user['nama']) ?>"
                                    data-username="<?= esc($user['username']) ?>"
                                    data-role="<?= $user['role'] ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <?php if (!$user['is_master']): ?>
                                <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $user['id'] ?>">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="tambahUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formTambahUser">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User Baru</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Foto Profil (Opsional)</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#tambahUserModal').modal('hide')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formEditUser">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" onclick="$('#editUserModal').modal('hide')">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" id="edit_username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" name="password" class="form-control" placeholder="Isi hanya jika ingin mengganti password">
                    </div>
                    <div class="form-group">
                        <label>Foto Profil (Biarkan kosong jika tidak ingin mengubah)</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" id="edit_role" class="form-control" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#editUserModal').modal('hide')">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Handle form tambah user
    $('#formTambahUser').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: '<?= base_url('api/users') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#tambahUserModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                let msg = 'Terjadi kesalahan';
                if(xhr.responseJSON && xhr.responseJSON.messages) {
                    msg = Object.values(xhr.responseJSON.messages).join('<br>');
                }
                Swal.fire('Gagal', msg, 'error');
            }
        });
    });

    // Handle open edit modal
    $('#dataTable').on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const username = $(this).data('username');
        const role = $(this).data('role');

        $('#edit_id').val(id);
        $('#edit_nama').val(nama);
        $('#edit_username').val(username);
        $('#edit_role').val(role);
        
        $('#editUserModal').modal('show');
    });

    // Handle form edit user
    $('#formEditUser').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_id').val();
        
        // Use FormData for file upload
        const formData = new FormData(this);
        formData.append('_method', 'PUT'); // Method spoofing for CI4

        $.ajax({
            url: '<?= base_url('api/users') ?>/' + id,
            type: 'POST', // Must be POST for FormData
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#editUserModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                let msg = 'Terjadi kesalahan';
                if(xhr.responseJSON && xhr.responseJSON.messages) {
                    msg = Object.values(xhr.responseJSON.messages).join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire('Gagal', msg, 'error');
            }
        });
    });

    // Handle delete user
    $('#dataTable').on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data user akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('api/users') ?>/' + id,
                    type: 'DELETE',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal', 'Gagal menghapus data', 'error');
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>