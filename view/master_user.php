<div class="cc" id="frm-cus" style="background-color: #E3DEDE;">
  <div class="posisi">
    <ul class="nav nav-tabs">
      <li role="presentation" class="active">
        <input type="button" name="usr_btn_input" class="btn btn-danger" id="usr_btn_input" value="Input User">
      </li>
      <li role="presentation">
        <input type="button" name="usr_btn_select" class="btn btn-primary" id="usr_btn_select" value="Data User">
      </li>
    </ul>

    <div id="usr_divisi" style="margin-left: 1%;margin-bottom: 1%;">
      <h1><p class="text-center"><b>Tambah User</b></p></h1><hr />
      <form id="usr_form">
        <!-- Status user -->
        <div class="form-group row">
          <label class="col-sm-3 control-label"><p class="text-left font-weight-normal">Status User</p></label>
          <div class="col-sm-7">
            <select name="usr_status" id="usr_status" class="form-control">
              <option value="">Pilih Status User</option>
              <option value="Admin">Admin</option>
              <option value="Customer">Customer</option>
            </select>
          </div>
        </div>

        <!-- Nama user -->
        <div class="form-group row">
          <label class="col-sm-3 control-label"><p class="text-left font-weight-normal">Nama User</p></label>
          <div class="col-sm-7">
            <input type="text" name="usr_nama" class="form-control" id="usr_nama" placeholder="Nama User" maxlength="30" required>
          </div>
        </div>

        <!-- Alamat -->
        <div class="form-group row">
          <label class="col-sm-3 control-label"><p class="text-left font-weight-normal">Alamat</p></label>
          <div class="col-sm-7">
            <textarea name="usr_alamat" class="form-control" id="usr_alamat" placeholder="Alamat User" cols="30" rows="4" maxlength="100"></textarea>
          </div>
        </div>

        <!-- Email -->
        <div class="form-group row">
          <label class="col-sm-3 control-label"><p class="text-left font-weight-normal">Email</p></label>
          <div class="col-sm-7">
            <input type="email" name="usr_email" id="usr_email" class="form-control" placeholder="example@gmail.com" maxlength="40" required autocomplete="off">
          </div>
        </div>

        <!-- Telepon -->
        <div class="form-group row">
          <label class="col-sm-3 control-label"><p class="text-left font-weight-normal">No Telepon</p></label>
          <div class="col-sm-7">
            <input type="text" name="usr_tlp" id="usr_tlp" class="form-control" maxlength="13" placeholder="08999385257" required>
          </div>
        </div>

        <!-- Password -->
        <div class="form-group row">
          <label class="col-sm-3 control-label"><p class="text-left font-weight-normal">Password</p></label>
          <div class="col-sm-7 input-group mb-3">
            <input type="password" name="usr_pass" id="usr_pass" class="form-control" maxlength="64" placeholder="Minimal 8 karakter" required>
            <div class="input-group-append">
              <button class="btn btn-outline-secondary toggle-pw" type="button" data-target="#usr_pass"><i class="fa fa-eye"></i></button>
              <span class="true_pass input-group-text fa fa-lg fa-check" style="color:green;display:none;"></span>
              <span class="false_pass input-group-text fa fa-lg fa-times" style="color:red;display:none;"></span>
            </div>
          </div>
        </div>

        <!-- Konfirmasi Password -->
        <div class="form-group row">
          <label class="col-sm-3 control-label"><p class="text-left font-weight-normal">Konfirmasi Password</p></label>
          <div class="col-sm-7 input-group mb-3">
            <input type="password" name="usr_konfir_pass" id="usr_konfir_pass" class="form-control" maxlength="64" placeholder="Ketik ulang password" required>
            <div class="input-group-append">
              <button class="btn btn-outline-secondary toggle-pw" type="button" data-target="#usr_konfir_pass"><i class="fa fa-eye"></i></button>
              <span class="true_konfir input-group-text fa fa-lg fa-check" style="color:green;display:none;"></span>
              <span class="false_konfir input-group-text fa fa-lg fa-times" style="color:red;display:none;"></span>
            </div>
          </div>
          <span class="warning_max" style="display:none;color:red;">Password minimal 8 karakter</span>
        </div>

        <!-- Tombol Save -->
        <div class="row">
          <div class="col-md-7 offset-md-3">
            <button type="button" id="usr_simpan" class="btn btn-md btn-primary btn-block" disabled>
              <span class="fa fa-fw fa-save"></span> Save
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  const $pw = $('#usr_pass');
  const $pw2 = $('#usr_konfir_pass');
  const $saveBtn = $('#usr_simpan');
  const $warning = $('.warning_max');

  // Toggle show/hide password
  $(document).on('click', '.toggle-pw', function(){
    const target = $(this).data('target');
    const $input = $(target);
    const $icon = $(this).find('i');

    if($input.attr('type') === 'password'){
      $input.attr('type', 'text');
      $icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
      $input.attr('type', 'password');
      $icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
  });

  // Validasi password real-time
  function validatePasswords(){
    const v1 = $pw.val();
    const v2 = $pw2.val();
    const enoughLen = v1.length >= 8;

    // panjang minimal
    if(!enoughLen && v1.length>0){
      $warning.show();
    } else {
      $warning.hide();
    }

    // indikator password valid
    if(enoughLen){
      $('.true_pass').show(); $('.false_pass').hide();
    } else if(v1.length>0){
      $('.true_pass').hide(); $('.false_pass').show();
    } else {
      $('.true_pass, .false_pass').hide();
    }

    // indikator cocok / tidak
    if(v2.length>0 && v1 === v2 && enoughLen){
      $('.true_konfir').show(); $('.false_konfir').hide();
    } else if(v2.length>0){
      $('.true_konfir').hide(); $('.false_konfir').show();
    } else {
      $('.true_konfir, .false_konfir').hide();
    }

    // tombol Save aktif hanya kalau valid
    if(enoughLen && v1 === v2){
      $saveBtn.prop('disabled', false);
    } else {
      $saveBtn.prop('disabled', true);
    }
  }

  $pw.on('input', validatePasswords);
  $pw2.on('input', validatePasswords);

  // Simpan user
  $('#usr_simpan').on('click', function(e){
    e.preventDefault();
    const data = $('#usr_form').serialize();
    $(this).prop('disabled', true).text('Saving...');

    $.post('pro_master_user.php', data, function(resp){
      alert(resp);
      $('#usr_simpan').prop('disabled', false).text('Save');
      $('#usr_form')[0].reset();
      validatePasswords();
    }).fail(function(){
      alert('Terjadi kesalahan koneksi.');
      $('#usr_simpan').prop('disabled', false).text('Save');
    });
  });
});
</script>
