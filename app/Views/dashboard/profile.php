<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Foto Profile</h6>
                </div>
                <div class="card-body text-center">
                    <img id="previewFoto" src="<?= base_url($user['foto'] ? 'uploads/profiles/' . $user['foto'] : 'img/undraw_profile_2.svg') ?>" 
                         class="rounded-circle img-fluid" style="width: 200px; height: 200px; object-fit: cover;">
                    <div class="mt-3">
                        <form id="formUploadFoto" enctype="multipart/form-data">
                            <input type="file" id="foto" name="foto" accept="image/*" style="display: none;">
                            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('foto').click()">
                                Ubah Foto
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Profile</h6>
                </div>
                <div class="card-body">
                    <form id="formUpdateProfile">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" value="<?= $user['username'] ?? $user['nama'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="<?= $user['nama'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <input type="text" class="form-control" value="<?= ucfirst($user['role']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <input type="text" class="form-control" value="<?= ucfirst(isset($user['status']) ? $user['status'] : 'active') ?>" readonly>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>

                    <!-- Form Ganti Password -->
                    <hr>
                    <h6 class="m-0 font-weight-bold text-primary mt-4">Ganti Password</h6>
                    <form id="formGantiPassword" class="mt-3">
                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" name="password_baru" class="form-control" placeholder="Masukkan password baru">
                        </div>
                        <div class="form-group">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" name="konfirmasi_password" class="form-control" placeholder="Konfirmasi password baru">
                        </div>
                        <button type="submit" class="btn btn-warning">Ganti Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Preview foto sebelum upload
    $('#foto').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewFoto').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
            
            // Auto upload foto
            uploadFoto();
        }
    });

    // Upload foto
    function uploadFoto() {
        const formData = new FormData($('#formUploadFoto')[0]);
        
        $.ajax({
            url: '<?= base_url('dashboard/upload_foto') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showAlert('success', 'Foto berhasil diupload!');
                } else {
                    showAlert('error', response.message || 'Gagal upload foto');
                }
            },
            error: function() {
                showAlert('error', 'Terjadi kesalahan saat upload foto');
            }
        });
    }

    // Update profile
    $('#formUpdateProfile').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '<?= base_url('dashboard/update_profile') ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    showAlert('success', 'Profile berhasil diupdate!');
                } else {
                    showAlert('error', response.message || 'Gagal update profile');
                }
            },
            error: function() {
                showAlert('error', 'Terjadi kesalahan saat update profile');
            }
        });
    });

    // Ganti password
    $('#formGantiPassword').on('submit', function(e) {
        e.preventDefault();
        
        const passwordBaru = $('input[name="password_baru"]').val();
        const konfirmasi = $('input[name="konfirmasi_password"]').val();
        
        if (passwordBaru !== konfirmasi) {
            showAlert('error', 'Password dan konfirmasi password tidak sama');
            return;
        }
        
        if (passwordBaru.length < 6) {
            showAlert('error', 'Password minimal 6 karakter');
            return;
        }
        
        $.ajax({
            url: '<?= base_url('dashboard/ganti_password') ?>',
            type: 'POST',
            data: { password: passwordBaru },
            success: function(response) {
                if (response.success) {
                    showAlert('success', 'Password berhasil diubah!');
                    $('#formGantiPassword')[0].reset();
                } else {
                    showAlert('error', response.message || 'Gagal mengubah password');
                }
            },
            error: function() {
                showAlert('error', 'Terjadi kesalahan saat mengubah password');
            }
        });
    });

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>`;
        
        $('.container-fluid').prepend(alertHtml);
        
        // Auto remove alert after 5 seconds
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
    }
});
</script>
<?= $this->endSection() ?>