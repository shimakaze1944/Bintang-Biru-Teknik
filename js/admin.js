// BBTehnik/js/custom_admin.js
document.addEventListener('DOMContentLoaded', function(){
  const usersBody = document.getElementById('users-body');
  const modalUser = new bootstrap.Modal(document.getElementById('modalUser'));
  const formUser = document.getElementById('formUser');

  // load users
  function loadUsers(){
    fetch('/controller/user/pro_master_user.php?action=list')
      .then(r=>r.json())
      .then(data=>{
        usersBody.innerHTML = '';
        let no = 1;
        data.forEach(u=>{
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${no++}</td>
            <td>${escapeHtml(u.usr_username)}</td>
            <td>${escapeHtml(u.usr_nama)}</td>
            <td>${escapeHtml(u.usr_email)}</td>
            <td>${escapeHtml(u.usr_tlp)}</td>
            <td>${escapeHtml(u.usr_status)}</td>
            <td>${escapeHtml(u.vendor_name ?? '')}</td>
            <td>
              <button class="btn btn-sm btn-primary btn-edit-user" data-id="${u.usr_id}">Edit</button>
              <button class="btn btn-sm btn-danger btn-delete-user" data-id="${u.usr_id}">X</button>
            </td>
          `;
          usersBody.appendChild(tr);
        });
      }).catch(e=>console.error(e));
  }

  loadUsers();

  // helper escape
  function escapeHtml(str){ if(!str) return ''; return String(str).replace(/[&<>"']/g, function(m){return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m];}); }

  // add user button
  const btnAdd = document.getElementById('btn-add-user');
  if (btnAdd) btnAdd.addEventListener('click', function(){
    formUser.reset(); document.getElementById('usr_id').value = '';
    document.getElementById('usr_username').disabled = false;
    modalUser.show();
  });

  // edit user click (delegate)
  document.addEventListener('click', function(e){
    if (e.target.matches('.btn-edit-user')) {
      const id = e.target.dataset.id;
      fetch('/controller/user/pro_master_user.php?action=get&id=' + id)
        .then(r=>r.json())
        .then(u=>{
          if (!u) { alert('User tidak ditemukan'); return; }
          document.getElementById('usr_id').value = u.usr_id;
          document.getElementById('usr_username').value = u.usr_username;
          document.getElementById('usr_username').disabled = true; // don't change username on edit
          document.getElementById('usr_nama').value = u.usr_nama;
          document.getElementById('usr_email').value = u.usr_email;
          document.getElementById('usr_tlp').value = u.usr_tlp;
          document.getElementById('usr_status').value = u.usr_status;
          document.getElementById('vendor_id').value = u.vendor_id ?? '';
          modalUser.show();
        });
    }

    if (e.target.matches('.btn-delete-user')) {
      if (!confirm('Hapus user ini?')) return;
      const id = e.target.dataset.id;
      fetch('/controller/user/pro_master_user.php?action=delete', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'usr_id='+encodeURIComponent(id)
      }).then(r=>r.text()).then(txt=>{
        if (txt.trim()==='OK') {
          alert('User dihapus');
          loadUsers();
        } else alert('Gagal: '+txt);
      });
    }

    // --- service buttons (edit/delete) ---
    if (e.target.matches('.btn-edit-service')) {
      e.stopPropagation();
      const id = e.target.dataset.id;
      window.location = '/view/service_edit.php?id=' + id;
    }
    if (e.target.matches('.btn-delete-service')) {
      e.stopPropagation();
      if (!confirm('Hapus servis ini?')) return;
      const id = e.target.dataset.id;
      fetch('/controller/service/pro_service.php?action=delete', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'service_id='+encodeURIComponent(id)
      }).then(r=>r.text()).then(txt=>{
        if (txt.trim()==='OK') { alert('Dihapus'); location.reload(); }
        else alert('Gagal: '+txt);
      });
    }
  });

  // form submit (create or edit)
  formUser.addEventListener('submit', function(ev){
    ev.preventDefault();
    const form = new FormData(formUser);
    const id = form.get('usr_id');
    const action = id ? 'edit' : 'create';
    fetch('/controller/user/pro_master_user.php?action=' + action, {
      method: 'POST',
      body: form
    }).then(r=>r.text()).then(txt=>{
      if (txt.trim() === 'OK') {
        modalUser.hide();
        loadUsers();
      } else {
        alert('Gagal: ' + txt);
      }
    });
  });

  // prevent double-click removing card: do not bind remove on card click anywhere else
});
