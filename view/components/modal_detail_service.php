<div class="modal fade" id="modalDetailServis" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa fa-ship me-2"></i> Detail Servis Kapal</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <strong>No WO:</strong>
                    <span id="det_no_wo" class="ms-2 text-dark fw-bold"></span>
                </div>

                <div class="row mb-2">
                    <div class="col-md-6"><strong>Vendor:</strong> <span id="det_vendor"></span></div>
                    <div class="col-md-6"><strong>Nama Kapal:</strong> <span id="det_kapal"></span></div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-6"><strong>Tgl Masuk:</strong> <span id="det_masuk"></span></div>
                    <div class="col-md-6"><strong>Tgl Keluar:</strong> <span id="det_keluar"></span></div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-6"><strong>Teknisi:</strong> <span id="det_teknisi"></span></div>
                    <div class="col-md-6"><strong>Status:</strong>
                        <span id="det_status" class="badge px-3 py-2"></span>
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Keterangan:</strong>
                    <div id="det_keterangan" class="border rounded p-2 mt-1 bg-light"></div>
                </div>

                <hr>
                <div class="mb-2"><strong>Layanan:</strong></div>
                <div id="det_layanan" class="mb-3"></div>

                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Total Harga</span>
                    <span id="det_total" class="text-success">Rp 0</span>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ===================== STYLE ===================== -->
<style>
    #modalDetailServis .modal-body strong {
        color: #222;
    }

    #modalDetailServis .modal-body span {
        color: #333;
    }

    #det_layanan .layanan-item {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px dashed #ddd;
        padding: 4px 0;
    }

    #det_layanan .layanan-item:last-child {
        border-bottom: none;
    }

    #det_total {
        font-size: 1.1rem;
    }
</style>

<!-- ===================== SCRIPT ===================== -->
<script>
    function showServisDetail(id) {
        fetch(`controller/master/service_controller.php?action=get&id=${id}`)
            .then(r => r.json())
            .then(d => {
                if (!d || !d.svs_id) {
                    alert('Data tidak ditemukan!');
                    return;
                }

                // Isi data utama
                $('#det_no_wo').text(d.no_wo || '-');
                $('#det_vendor').text(d.vendor_name || '-');
                $('#det_kapal').text(d.nama_kapal || '-');
                $('#det_masuk').text(d.tgl_masuk || '-');
                $('#det_keluar').text(d.tgl_keluar || '-');
                $('#det_teknisi').text(d.pekerja_nama || '-');
                $('#det_keterangan')
                    .html(d.keterangan && d.keterangan.trim() !== ''
                        ? `<div style="text-align:left; white-space:pre-line; font-weight:400; color:#333;">${d.keterangan}</div>`
                        : '<div class="text-secondary" style="text-align:left;">Tidak ada keterangan</div>');

                // Status badge
                const badgeClass = {
                    'Done': 'bg-success',
                    'On Progress': 'bg-warning text-dark',
                    'On Hold': 'bg-secondary',
                    'Canceled': 'bg-danger'
                }[d.status] || 'bg-dark';
                $('#det_status').attr('class', `badge px-3 py-2 ${badgeClass}`).text(d.status || '-');

                // Layanan & harga
                let html = '';
                let total = 0;
                if (d.layanan_detail && d.layanan_detail.length > 0) {
                    d.layanan_detail.forEach(l => {
                        const harga = parseFloat(l.harga || 0);
                        total += harga;
                        html += `<div class="layanan-item">
                      <span>${l.nama}</span>
                      <span>Rp ${harga.toLocaleString('id-ID')}</span>
                    </div>`;
                    });
                } else {
                    html = '<div class="text-muted">Tidak ada layanan</div>';
                }
                $('#det_layanan').html(html);
                $('#det_total').text('Rp ' + total.toLocaleString('id-ID'));

                $('#modalDetailServis').modal('show');
            })
            .catch(err => {
                console.error(err);
                alert('Gagal memuat detail servis.');
            });
    }
</script>