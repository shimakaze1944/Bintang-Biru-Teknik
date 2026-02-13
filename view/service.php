<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE)
  session_start();
include_once(__DIR__ . '/../controller/auth/db_connection.php');

// role check
if (!in_array($_SESSION['sess_usr_status'], ['Admin', 'BBTeknik'])) {
  echo "<div class='alert alert-danger'>Akses ditolak! Hanya Admin & BB Teknik.</div>";
  exit;
}
?>

<div class="container-fluid px-4 mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">⚓ Manajemen Servis Kapal</h4>
    <button class="btn btn-primary" id="btnAddService">
      <i class="fa fa-plus"></i> Tambah Servis
    </button>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle text-center">
          <thead class="table-light">
            <tr>
              <th>No</th>
              <th>No WO</th>
              <th>Nama Kapal</th>
              <th>Vendor</th>
              <th>Layanan</th>
              <th>Teknisi</th>
              <th>Tanggal Masuk</th>
              <th>Tanggal Keluar</th>
              <th>Status</th>
              <th>Keterangan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            $q = $conn->query("
              SELECT 
                s.*, 
                v.vendor_name,
                GROUP_CONCAT(DISTINCT l.layanan_nama SEPARATOR ', ') AS layanan_list,
                GROUP_CONCAT(DISTINCT p.pekerja_nama SEPARATOR ', ') AS pekerja_list
              FROM tbl_servis s
              LEFT JOIN tbl_vendor v ON s.vendor_id = v.vendor_id
              LEFT JOIN tbl_servis_layanan sl ON s.svs_id = sl.svs_id
              LEFT JOIN tbl_layanan l ON sl.layanan_id = l.layanan_id
              LEFT JOIN tbl_servis_pekerja sp ON s.svs_id = sp.svs_id
              LEFT JOIN tbl_pekerja p ON sp.pekerja_id = p.pekerja_id
              GROUP BY s.svs_id
              ORDER BY s.tgl_masuk DESC
            ");
            if ($q && $q->num_rows > 0) {
              while ($row = $q->fetch_assoc()) {
                $badge = match ($row['status']) {
                  'Done' => 'success',
                  'On Progress' => 'warning',
                  'On Hold' => 'secondary',
                  'Canceled' => 'danger',
                  default => 'dark'
                };
                echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['no_wo']}</td>
                        <td>{$row['nama_kapal']}</td>
                        <td>" . ($row['vendor_name'] ?? '-') . "</td>
                        <td>{$row['layanan_list']}</td>
                        <td>{$row['pekerja_list']}</td>
                        <td>{$row['tgl_masuk']}</td>
                        <td>{$row['tgl_keluar']}</td>
                        <td><span class='badge bg-{$badge}' style='color:white; padding:5px;'>{$row['status']}</span></td>
                        <td>{$row['keterangan']}</td>
                        <td>
                          <button class='btn btn-sm btn-info me-1 btnDetailService' data-id='{$row['svs_id']}'>
                            <i class='fa fa-eye'></i> Detail
                          </button>
                          <button class='btn btn-sm btn-warning me-1 btnEditService' data-id='{$row['svs_id']}'>
                            <i class='fa fa-edit'></i> Ubah
                          </button>
                          <button class='btn btn-sm btn-danger btnDeleteService' data-id='{$row['svs_id']}'>
                            <i class='fa fa-trash'></i> Hapus
                          </button>
                        </td>
                      </tr>";
                $no++;
              }
            } else {
              echo "<tr><td colspan='11' class='text-muted'>Belum ada data servis</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- MODAL TAMBAH/EDIT SERVIS -->
<div class="modal fade" id="serviceModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fa fa-wrench me-2"></i><span id="modalTitle">Tambah Servis</span></h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>

      <form id="serviceForm" method="POST" action="controller/master/service_controller.php">
        <div class="modal-body">
          <input type="hidden" name="svs_id" id="svs_id">
          <input type="hidden" name="action" id="form_action" value="create">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">No WO</label>
              <input type="text" name="no_wo" id="no_wo" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Vendor</label>
              <select name="vendor_id" id="vendor_id" class="form-control" required>
                <option value="">-- Pilih Vendor --</option>
                <?php
                $v = $conn->query("SELECT * FROM tbl_vendor ORDER BY vendor_name ASC");
                while ($ven = $v->fetch_assoc()) {
                  echo "<option value='{$ven['vendor_id']}'>{$ven['vendor_name']}</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Nama Kapal</label>
              <select name="nama_kapal" id="nama_kapal" class="form-control" disabled required>
                <option value="">-- Pilih Vendor Dulu --</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Tanggal Masuk</label>
              <input type="date" name="tgl_masuk" id="tgl_masuk" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Tanggal Keluar</label>
              <input type="date" name="tgl_keluar" id="tgl_keluar" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select name="status" id="status" class="form-control">
                <option value="On Progress">On Progress</option>
                <option value="On Hold">On Hold</option>
                <option value="Done">Done</option>
                <option value="Canceled">Canceled</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Layanan</label>
              <select name="layanan[]" id="layanan" class="form-control select2" multiple>
                <?php
                $lay = $conn->query("SELECT * FROM tbl_layanan ORDER BY layanan_nama ASC");
                while ($l = $lay->fetch_assoc()) {
                  echo "<option value='{$l['layanan_id']}'>{$l['layanan_nama']}</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Teknisi</label>
              <select name="pekerja[]" id="pekerja" class="form-control select2" multiple>
                <?php
                $p = $conn->query("SELECT * FROM tbl_pekerja ORDER BY pekerja_nama ASC");
                while ($pe = $p->fetch_assoc()) {
                  echo "<option value='{$pe['pekerja_id']}'>{$pe['pekerja_nama']}</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-12">
              <label class="form-label">Keterangan</label>
              <textarea name="keterangan" id="keterangan" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ================= LIBRARIES ================= -->
<link rel="stylesheet" href="assets/select2/select2.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- ===================== STYLE ===================== -->
<style>
  /* ======== BASE FORM STYLING ======== */
  .modal-body .form-label {
    font-weight: 600;
  }

  .modal-body .col-md-6,
  .modal-body .col-md-12 {
    margin-bottom: 12px;
  }

  /* ======== TABLE STYLING ======== */
  .table td {
    vertical-align: middle !important;
    padding: 10px 12px;
  }

  .table td:nth-child(1),
  .table td:nth-child(2),
  .table td:nth-child(3),
  .table td:nth-child(4),
  .table td:nth-child(5),
  .table td:nth-child(6),
  .table td:nth-child(7),
  .table td:nth-child(8),
  .table td:nth-child(9),
  .table td:nth-child(10) {
    white-space: normal !important;
    word-break: break-word;
    text-align: left !important;
  }

  .table td:last-child {
    white-space: nowrap !important;
    width: 150px;
    text-align: center !important;
    vertical-align: middle !important;
  }

  .table td:last-child .btn {
    vertical-align: middle;
  }

  .table tbody tr:hover {
    background-color: #f8f9fa;
  }

  @media (max-width: 768px) {
    .table td:last-child {
      width: 120px;
    }
  }

  /* ======== SELECT2 CLEAN STYLE ======== */
  .select2-container--default.select2-bar .select2-selection--multiple {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    min-height: 40px;
    padding: 4px 6px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    background: #fff;
    position: relative;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    padding-right: 36px !important;
    /* ruang clear-all */
  }

  .select2-container--default.select2-bar .select2-selection--multiple:focus-within {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, 0.15);
  }

  .select2-container--default.select2-bar .select2-selection__choice {
    background-color: #e9ecef !important;
    color: #212529 !important;
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    padding: 3px 6px 3px 4px !important;
    margin: 3px 6px 3px 0 !important;
    display: inline-flex;
    align-items: center;
    font-size: 13px;
  }

  .select2-container--default.select2-bar .select2-selection__choice__remove {
    color: #6c757d !important;
    margin-right: 6px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    opacity: 0.8;
    background: transparent !important;
    border: none !important;
    line-height: 1;
    position: relative;
  }

  .select2-container--default.select2-bar .select2-selection__choice__remove:hover {
    color: #343a40 !important;
    opacity: 1;
  }

  /* tombol X clear-all */

  .select2-container--default.select2-bar .select2-selection--multiple.show-clear .select2-selection__clear {
    display: block !important;
  }

  .select2-container--default.select2-bar .select2-selection--multiple .select2-selection__clear:hover {
    color: #555;
  }

  /* input pencarian */
  .select2-container--default .select2-search--inline .select2-search__field {
    margin-top: 2px;
    border: none !important;
    outline: none !important;
    min-width: 100px;
  }

  /* disabled style */
  .select2-container--default.select2-bar.select2-disabled .select2-selection {
    background-color: #f8f9fa;
    cursor: default;
    pointer-events: none;
    opacity: 1;
  }
</style>


<!-- ===================== SCRIPT ===================== -->
<script>
  $(window).on('load', function () {
    const modal = $('#serviceModal');

    // Load Select2
    $.getScript('assets/select2/select2.full.min.js')
      .done(function () {
        console.log('Select2 JS loaded');

        // === Inisialisasi Select2 ===
        $('.select2').select2({
          placeholder: "Klik untuk pilih",
          width: '100%',
          closeOnSelect: false,
          allowClear: true,
          dropdownParent: $('#serviceModal')
        }).each(function () {
          $(this).next('.select2-container').addClass('select2-bar');
        });

        // ======== FIX: Hanya bisa hapus via X item atau clear-all, bukan klik ulang ========

        let allowUnselect = false;

        // Saat klik X di item → izinkan unselect
        $(document).on('mousedown.select2', '.select2-selection__choice__remove', function () {
          allowUnselect = true;
        });

        // Cegah unselect via klik ulang di dropdown
        $(document).on('select2:unselecting', '.select2', function (e) {
          if (!allowUnselect) {
            e.preventDefault();
          }
          setTimeout(() => { allowUnselect = false; }, 50);
        });

        // ======== FUNGSI TAMBAHAN (CLEAR BUTTON, ENABLE/DISABLE, DLL) ========

        // Update tampilan tombol clear-all
        function updateClearVisibility($select) {
          const $container = $select.next('.select2-container');
          const hasValue = ($select.val() && $select.val().length > 0);
          const disabled = $select.prop('disabled');
          if (hasValue && !disabled) {
            $container.find('.select2-selection--multiple').addClass('show-clear');
          } else {
            $container.find('.select2-selection--multiple').removeClass('show-clear');
          }
        }

        // Klik tombol clear-all (X kanan)
        $(document).on('click', '.select2-selection__clear', function (e) {
          e.preventDefault();
          e.stopPropagation();
          const $select = $(this).closest('.select2-container').prev('select');
          $select.val(null).trigger('change');
          if ($select.data('select2')) {
            $select.select2('close');
          }
          updateClearVisibility($select);
        });

        // Update clear-all saat ada perubahan
        $('.select2').each(function () {
          updateClearVisibility($(this));
        });
        $(document).on('change', '.select2', function () {
          updateClearVisibility($(this));
        });

        // Enable/disable Select2 secara penuh
        function setSelect2Disabled($select, disabled) {
          $select.prop('disabled', disabled);
          $select.trigger('change.select2');
          const $container = $select.next('.select2-container');
          if (disabled) {
            $container.addClass('select2-disabled');
            $container.find('.select2-selection--multiple').removeClass('show-clear');
            if ($select.data('select2')) {
              $select.select2('close');
            }
          } else {
            $container.removeClass('select2-disabled');
            updateClearVisibility($select);
          }
        }

        // =============== EVENT HANDLER ===============

        // Tambah Servis
        $('#btnAddService').on('click', function () {
          $('#serviceForm')[0].reset();
          $('#svs_id').val('');
          $('#form_action').val('create');
          $('#modalTitle').text('Tambah Servis');
          $('#layanan').val(null).trigger('change');
          $('#pekerja').val(null).trigger('change');
          $('#serviceForm :input').prop('disabled', false);
          $('.select2').each(function () { setSelect2Disabled($(this), false); });
          $('.btn-success').show();
          $('.btn-secondary').text('Batal');
          modal.modal('show');
        });

        // Edit Servis
        $(document).on('click', '.btnEditService', function () {
          const id = $(this).data('id');
          $('#serviceForm :input').prop('disabled', false);
          $('.btn-success').show();
          $('.btn-secondary').text('Batal');

          fetch(`controller/master/service_controller.php?action=get&id=${id}`)
            .then(r => r.json())
            .then(async d => {
              $('#svs_id').val(d.svs_id);
              $('#no_wo').val(d.no_wo);
              $('#vendor_id').val(d.vendor_id).trigger('change');
              $('#tgl_masuk').val(d.tgl_masuk);
              $('#tgl_keluar').val(d.tgl_keluar);
              $('#status').val(d.status);
              $('#keterangan').val(d.keterangan);
              await new Promise(r => setTimeout(r, 400));
              $('#nama_kapal').val(d.nama_kapal);
              $('#layanan').val(d.layanan).trigger('change');
              $('#pekerja').val(d.pekerja).trigger('change');
              $('#form_action').val('edit');
              $('#modalTitle').text('Edit Servis');
              $('.select2').each(function () { setSelect2Disabled($(this), false); });
              modal.modal('show');
            });
        });

        // Detail Servis
        $(document).on('click', '.btnDetailService', function () {
          const id = $(this).data('id');
          fetch(`controller/master/service_controller.php?action=get&id=${id}`)
            .then(res => res.json())
            .then(async data => {
              $('#svs_id').val(data.svs_id);
              $('#no_wo').val(data.no_wo);
              $('#vendor_id').val(data.vendor_id).trigger('change');
              $('#tgl_masuk').val(data.tgl_masuk);
              $('#tgl_keluar').val(data.tgl_keluar);
              $('#status').val(data.status);
              $('#keterangan').val(data.keterangan);
              await new Promise(resolve => setTimeout(resolve, 400));
              $('#nama_kapal').val(data.nama_kapal);
              $('#layanan').val(data.layanan).trigger('change');
              $('#pekerja').val(data.pekerja).trigger('change');
              $('#modalTitle').text('Detail Servis');
              $('#form_action').val('view');
              $('#serviceForm :input').prop('disabled', true);
              $('.select2').each(function () { setSelect2Disabled($(this), true); });
              $('.btn-success').hide();
              $('.btn-secondary').text('Tutup');
              modal.modal('show');
            });
        });

        // Vendor → Kapal
        $('#vendor_id').on('change', function () {
          const id = $(this).val();
          const kapal = $('#nama_kapal');
          kapal.prop('disabled', true).html('<option>Loading...</option>');
          if (!id) { kapal.html('<option value="">-- Pilih Vendor Dulu --</option>'); return; }
          fetch(`controller/master/get_kapal.php?vendor_id=${id}`)
            .then(r => r.text())
            .then(d => {
              kapal.html('<option value="">-- Pilih Kapal --</option>' + d);
              kapal.prop('disabled', false);
            });
        });

        // Simpan data
        $('#serviceForm').on('submit', function (e) {
          e.preventDefault();
          $.post('controller/master/service_controller.php', $(this).serialize(), function (res) {
            if (res.trim() === 'OK') {
              alert('Data servis berhasil disimpan!');
              modal.modal('hide');
              location.reload();
            } else {
              alert('Gagal menyimpan: ' + res);
            }
          });
        });

        // Hapus data
        $(document).on('click', '.btnDeleteService', function () {
          const id = $(this).data('id');
          if (confirm('Yakin ingin menghapus data ini?')) {
            location.href = `controller/master/service_controller.php?action=delete&id=${id}`;
          }
        });
      })
      .fail(function (_, __, err) {
        console.error('Gagal load Select2:', err);
      });
  });
</script>
