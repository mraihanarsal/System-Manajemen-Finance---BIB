<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Kelola TikTok
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- HEADER + BREADCRUMB -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-body">

                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="/tiktok" class="text-decoration-none">
                                <i class="fas fa-music me-1"></i> TikTok
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">List Toko Saya pada Platform Tiktok</li>
                    </ol>
                </nav>

                <button type="button"
                    class="btn btn-sm btn-primary"
                    data-toggle="modal"
                    data-target="#createTokoModal">
                    <i class="fas fa-plus me-1"></i> Tambah Toko
                </button>

            </div>
        </div>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="card shadow">
    <div class="card-body">

        <?php if (session()->has('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session('success') ?>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session()->has('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session('error') ?>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (empty($toko)) : ?>

            <div class="text-center py-5">
                <i class="fas fa-store-slash fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada toko TikTok</h5>
            </div>

        <?php else : ?>

            <div class="table-responsive">
                <table class="table table-bordered" width="100%">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Nama Toko</th>
                            <th>Alamat</th>
                            <th width="150">Status</th>
                            <th width="200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        if (!isset($pager)) {
                            $no = 1;
                        } else {
                            $page  = $pager->getCurrentPage('toko_tiktok') ?? 1;
                            $limit = $pager->getPerPage('toko_tiktok') ?? 5;
                            $no    = 1 + (($page - 1) * $limit);
                        }
                        ?>

                        <?php foreach ($toko as $t): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= esc($t['nama_toko']) ?></td>
                                <td><?= esc($t['alamat']) ?></td>

                                <td class="text-center">
                                    <?php if ($t['is_active'] == 1): ?>
                                        <span class="badge badge-success px-3 py-2">AKTIF</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger px-3 py-2">TIDAK AKTIF</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">

                                    <!-- BUTTON EDIT -->
                                    <button class="btn btn-warning btn-sm"
                                        data-toggle="modal"
                                        data-target="#editTokoModal<?= $t['id_toko'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- BUTTON HAPUS -->
                                    <button class="btn btn-danger btn-sm"
                                        data-toggle="modal"
                                        data-target="#hapusTokoModal<?= $t['id_toko'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <!-- BUTTON TOGGLE STATUS -->
                                    <?php if ($t['is_active'] == 1): ?>
                                        <a href="<?= base_url('tiktok/toko/deactivate/' . $t['id_toko']) ?>"
                                            class="btn btn-success btn-sm" title="Nonaktifkan">
                                            <i class="fa fa-toggle-on"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= base_url('tiktok/toko/activate/' . $t['id_toko']) ?>"
                                            class="btn btn-danger btn-sm" title="Aktifkan">
                                            <i class="fa fa-toggle-off"></i>
                                        </a>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

            <?php if (isset($pager)) : ?>
                <div class="mt-3">
                    <?= $pager->links('toko_tiktok', 'bootstrap_pagination') ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</div>

<!-- MODAL CREATE -->
<div class="modal fade" id="createTokoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tambah Toko TikTok</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="/tiktok/toko/store" method="post">
                <?= csrf_field() ?>

                <div class="modal-body">

                    <div class="form-group">
                        <label>ID Toko(Otomatis)</label>
                        <input type="text" class="form-control"
                            value="<?= $id_tiktok_auto ?>" readonly style="background:#f8f9fa">
                        <input type="hidden" name="id_toko" value="<?= $id_tiktok_auto ?>">
                    </div>

                    <div class="form-group">
                        <label>Nama Toko *</label>
                        <input type="text" class="form-control" name="nama_toko" required>
                    </div>

                    <div class="form-group">
                        <label>Alamat *</label>
                        <textarea class="form-control" name="alamat" rows="3" required></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- MODAL EDIT -->
<?php foreach ($toko as $t): ?>
    <div class="modal fade" id="editTokoModal<?= $t['id_toko'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Toko - <?= esc($t['nama_toko']) ?></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <form action="/tiktok/toko/update/<?= $t['id_toko'] ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>ID Toko</label>
                            <input type="text" class="form-control"
                                value="<?= $t['id_toko'] ?>" readonly style="background:#f8f9fa">
                        </div>

                        <div class="form-group">
                            <label>Nama Toko *</label>
                            <input type="text" class="form-control" name="nama_toko"
                                value="<?= esc($t['nama_toko']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Alamat *</label>
                            <textarea class="form-control" name="alamat" rows="3" required><?= esc($t['alamat']) ?></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- MODAL HAPUS -->
<?php foreach ($toko as $t): ?>
    <div class="modal fade" id="hapusTokoModal<?= $t['id_toko'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Hapus Toko</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    Apakah anda yakin ingin menghapus toko <strong><?= esc($t['nama_toko']) ?></strong>?
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <a href="/tiktok/toko/delete/<?= $t['id_toko'] ?>" class="btn btn-danger">Hapus</a>
                </div>

            </div>
        </div>
    </div>
<?php endforeach; ?>

<?= $this->endSection() ?>