<script type="text/javascript">
$('input[type="checkbox"].flat-checkbox').iCheck({
  checkboxClass: 'icheckbox_flat-green'
});
$('.flat-checkbox').on('ifChanged', function(event){
    $(this).val($(this).prop('checked') ? 1:0);
});

$(document).ready(function () {
  showData(1);
});

var tab=1;
async function showData(stt=1)
{
  tab=stt;
  columnWidths = ['25', '30', '15', '20'];
  columnHeads = ['Username', 'Full Name', 'Role', 'Last&nbsp;Login'];
  columns = ['username', 'name', 'role_name', 'last_login'];
  await prosesDatatable({
    url: '<?= $url_proses ?>get?status='+stt,
    columnHeads: columnHeads,
    columnWidths: columnWidths, //Persen
    columns: columns,
    order:[[0, 'asc'],[1, 'asc']],
    actions: function(data, type, row) {
      id=data.id; btnAksi='';
      <?php if ($showEdit) : ?>
        btnAksi += `<a href="javascript:void(0)" data-placement="top" data-toggle="tooltip"
              class="btn btn-success btn-xs editButton m3px"
              data-id="${id}" title="Edit">
              <i class="fa fa-edit"></i>
          </a>`;
      <?php endif; ?>
      <?php if ($showDelete) : ?>
        var myArray = ['<?= get_session('id_user') ?>', 1];
        if(jQuery.inArray(id, myArray) === -1) {
          btnAksi += `<a href="javascript:void(0)" data-placement="top" data-toggle="tooltip"
            class="btn btn-danger btn-xs deleteButton m3px"
            data-id="${id}" title="Delete">
            <i class="fa fa-trash"></i>
          </a>`;
        }
      <?php endif; ?>
      return btnAksi;
    }
  });
}


$(function () {

  <?php if ($showAdd) : ?>
    /* Modal Create */
   $('#createNew').click(function () {
       $('#modelHeading').html("Add User");
       $('#dataForm').trigger("reset");
       $('#form_id').val('');
       $('#vActive').hide();
       $('#is_active').val(1).iCheck('update');
       $('#is_active').prop('checked', true).iCheck('update');
       $('.form-control.select2').select2();
       $('#dataForm').parsley().reset();
       $('#role_id').removeAttr('disabled');
       $('#pwdRequired').show();
       $('#password').attr('required', true);
       $('#ajaxModal').modal('show');
   });
  <?php endif; ?>

  <?php if ($showEdit) : ?>
  /* Modal Edit */
  $('body').on('click', '.editButton', async function () {
      var id = $(this).data('id');
      await prosesSubmit({
        csrf: true, method: "GET",
        url: "<?= $url_proses ?>edit/" + id,
        callbackSuccess: function(response) {
          data = response.message;
          $('#modelHeading').html("Edit User");
          $('#pwdRequired').hide();
          $('#password').removeAttr('required');
          $('#form_id').val(data.id);
          $('#username').val(data.username);
          $('#password').val('');
          $('#name').val(data.name);
          $('#role_id').val(data.role_id);
          <?php if ($showActive) { ?>
            $('#vActive').show();
          <?php }else{ ?>
            $('#vActive').hide();
          <?php } ?>
          if(data.is_active == 1) {
            $('#is_active').val(1).iCheck('update');
            $('#is_active').prop('checked', true).iCheck('update');
          } else {
            $('#is_active').val(0).iCheck('update');
            $('#is_active').prop('checked', false).iCheck('update');
          }
          $('.form-control.select2').select2();
          $('#dataForm').parsley().reset();
          $('#ajaxModal').modal('show');
        }
      });
  });
  <?php endif; ?>

  <?php if ($showAdd || $showEdit) : ?>
  $('#dataForm').submit(async function (e) {
      e.preventDefault();
      form='dataForm';
      var fd = new FormData();
      isSave = true;
      $('#'+form+' *').each(function(key, field) {
        var field_name = field.name;
        if ($('[name="'+field_name+'"]').length!=0) {
          if ($('[name="'+field_name+'"]').attr('required')=='required' && $('[name="'+field_name+'"]').val() == '') {
            isSave = false;
            return false;
          }
          fd.append(field_name, $('[name="'+field_name+'"]').val());
        }
      });

      if (!isSave) { return false; }

      url = "<?= $url_proses ?>save";
      await prosesSubmit({
        csrf: true, method:"POST", form:form, url:url, fd:fd,
        callbackSuccess: function(data) {
          swalResponse('success', data.message, false, 'x');
          $('#ajaxModal').modal('hide');
          setTimeout(function () { showData(tab); }, 2500);
        }
      });
  });
  <?php endif; ?>

  <?php if ($showDelete) : ?>
  /* Hapus */
  $('body').on('click', '.deleteButton', function () {
      var id = $(this).data("id");
      prosesSubmitConfirm({
        title: 'Are you sure ?',
        html: 'Permanently delete the user and all data associated with this user!',
        send: {
          csrf: true, method: "DELETE",
          url: "<?= $url_proses ?>delete/" + id,
          callbackSuccess: function(data) {
            swalResponse('success', data.message, false, 'x');
            setTimeout(function() { showData(tab); }, 2500);
          }
        }
      });
  });
  <?php endif; ?>

});
</script>
