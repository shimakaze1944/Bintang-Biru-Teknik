<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE)
    session_start();
include_once(__DIR__ . '/../controller/auth/db_connection.php');

// hanya admin & bbteknik
if (!in_array($_SESSION['sess_usr_status'], ['Admin', 'BBTeknik'])) {
    echo "<div class='alert alert-danger'>Akses ditolak! Halaman ini hanya untuk Admin & BB Teknik.</div>";
    exit;
}
?>

<div class="container-fluid px-4 mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">🏢 Manajemen Vendor</h4>
        <button class="btn btn-primary" id="btnAddVendor">
            <i class="fa fa-plus"></i> Tambah Vendor
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th>No</th>
                            <th>Nama Vendor</th>
                            <th>Alamat</th>
                            <th>Kontak</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $q = $conn->query("SELECT * FROM tbl_vendor ORDER BY vendor_id DESC");

                        if ($q && $q->num_rows > 0) {
                            while ($row = $q->fetch_assoc()) {
                                echo "<tr>
                        <td class='text-center'>$no</td>
                        <td>{$row['vendor_name']}</td>
                        <td>{$row['vendor_alamat']}</td>
                        <td>{$row['vendor_kontak']}</td>
                        <td>" . ($row['vendor_keterangan'] ?: '-') . "</td>
                        <td class='text-center'>
                          <button class='btn btn-sm btn-warning me-1 btnEditVendor' data-id='{$row['vendor_id']}'>
                            <i class='fa fa-edit'></i> Ubah
                          </button>
                          <button class='btn btn-sm btn-danger btnDeleteVendor' data-id='{$row['vendor_id']}'>
                            <i class='fa fa-trash'></i> Hapus
                          </button>
                        </td>
                      </tr>";
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center text-muted'>Belum ada data vendor</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH / EDIT VENDOR -->
<div class="modal fade" id="vendorModal" tabindex="-1" role="dialog" aria-labelledby="vendorModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa fa-building me-2"></i><span id="modalTitle">Tambah Vendor</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="vendorForm" method="POST" action="controller/master/vendor_controller.php">
                <div class="modal-body">
                    <input type="hidden" name="vendor_id" id="vendor_id">
                    <input type="hidden" name="action" id="form_action" value="create">

                    <div class="form-group">
                        <label>Nama Vendor</label>
                        <input type="text" name="vendor_name" id="vendor_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="vendor_alamat" id="vendor_alamat" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Kontak</label>
                        <input type="text" name="vendor_kontak" id="vendor_kontak" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="vendor_keterangan" id="vendor_keterangan" class="form-control"
                            rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function waitForJQuery() {
        if (typeof $ === 'undefined') return setTimeout(waitForJQuery, 200);

        $(document).ready(function () {
            const vendorModal = $('#vendorModal');

            // Tambah Vendor
            $(document).on('click', '#btnAddVendor', function () {
                $('#vendorForm')[0].reset();
                $('#vendor_id').val('');
                $('#form_action').val('create');
                $('#modalTitle').text('Tambah Vendor');
                vendorModal.modal('show');
            });

            // Edit Vendor
            $(document).on('click', '.btnEditVendor', function () {
                const id = $(this).data('id');
                fetch(`controller/master/vendor_controller.php?action=get&id=${id}`)
                    .then(res => res.json())
                    .then(data => {
                        if (!data || !data.vendor_id) {
                            alert('Data vendor tidak ditemukan');
                            return;
                        }
                        $('#vendor_id').val(data.vendor_id);
                        $('#vendor_name').val(data.vendor_name);
                        $('#vendor_alamat').val(data.vendor_alamat);
                        $('#vendor_kontak').val(data.vendor_kontak);
                        $('#vendor_keterangan').val(data.vendor_keterangan || '');
                        $('#form_action').val('edit');
                        $('#modalTitle').text('Edit Vendor');
                        vendorModal.modal('show');
                    })
                    .catch(err => {
                        alert('Gagal mengambil data vendor.');
                        console.error(err);
                    });
            });

            // Simpan
            $(document).on('submit', '#vendorForm', function (e) {
                e.preventDefault();
                $.ajax({
                    url: 'controller/master/vendor_controller.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (res) {
                        if (res.trim() === 'OK') {
                            alert('Data vendor berhasil disimpan!');
                            vendorModal.modal('hide');
                            location.reload();
                        } else {
                            alert('Gagal menyimpan: ' + res);
                        }
                    },
                    error: function (xhr, status, err) {
                        alert('Terjadi kesalahan: ' + err);
                    }
                });
            });

            // Delete
            $(document).on('click', '.btnDeleteVendor', function () {
                const id = $(this).data('id');
                if (confirm('Yakin ingin menghapus vendor ini?')) {
                    window.location.href = `controller/master/vendor_controller.php?action=delete&id=${id}`;
                }
            });
        });
    })();
</script>