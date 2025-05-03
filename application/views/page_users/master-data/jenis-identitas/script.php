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
  columnWidths = ['20', '35', '15'];
  columnHeads = ['Kode', 'Keterangan', 'Tanggal&nbsp;diinput'];
  columns = ['kode', 'keterangan', 'created_at'];
  await prosesDatatable({
    url: '<?= $url_proses ?>get?status='+stt,
    columnHeads: columnHeads,
    columnWidths: columnWidths, //Persen
    columns: columns,
    order:[[0, 'asc'],[1, 'asc']],
    actions: function(data, type, row) {
      id=data.id; btnAksi='';
      <?php if ($showDetail) : ?>
        btnAksi += `<a href="javascript:void(0)" data-placement="top" data-toggle="tooltip"
              class="btn btn-info btn-xs detailButton m3px"
              data-id="${id}" title="Detail">
              <i class="fa fa-list"></i>
          </a>`;
      <?php endif; ?>
      <?php if ($showEdit) : ?>
        btnAksi += `<a href="javascript:void(0)" data-placement="top" data-toggle="tooltip"
              class="btn btn-success btn-xs editButton m3px"
              data-id="${id}" title="Edit">
              <i class="fa fa-edit"></i>
          </a>`;
      <?php endif; ?>
      <?php if ($showDelete) : ?>
          btnAksi += `<a href="javascript:void(0)" data-placement="top" data-toggle="tooltip"
            class="btn btn-danger btn-xs deleteButton m3px"
            data-id="${id}" title="Delete">
            <i class="fa fa-trash"></i>
          </a>`;
      <?php endif; ?>
      return btnAksi;
    }
  });
}


$(function () {

  <?php if ($showAdd) : ?>
    /* Modal Create */
   $('#createNew').click(async function () {
       $('#modelHeading').html("Add <?= $name ?>");
       $('#dataForm').trigger("reset");
       $('#form_id').val('');
       $('#vActive').hide();
       $('#is_active').val(1).iCheck('update');
       $('#is_active').prop('checked', true).iCheck('update');
       $('.form-control.select2').select2();
       $('#dataForm').parsley().reset();
       await getNewNomor();
       $('#ajaxModal').modal('show');
   });

   async function getNewNomor() {
     await prosesSubmit({
       csrf: true, method: "GET",
       url: "<?= $url_proses ?>generateNumber?name=md-jenis-identitas",
       callbackSuccess: function(response) {
         $('#kode').val(response.message);
       }
     });
   }
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
          $('#modelHeading').html("Edit <?= $name ?>");
          $('#form_id').val(data.id);
          $('#kode').val(data.kode);
          $('#keterangan').val(data.keterangan);
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

  <?php if ($showDetail) : ?>
  /* Modal Detail */
  $('body').on('click', '.detailButton', async function () {
      var id = $(this).data('id');
      await prosesSubmit({
        csrf: true, method: "GET",
        url: "<?= $url_proses ?>detail/" + id,
        callbackSuccess: function(response) {
          data = response.message;
          $('#modelHeadingDetail').html("Detail <?= $name ?>");
          $('#d_kode').html(data.kode);
          $('#d_keterangan').html(data.keterangan);
          $('#d_active').html(data.is_active==1 ? 'Yes':'No');
          $('#d_created_at').html(data.created_at ? format_tglnya(data.created_at, 'waktu') : '-');
          $('#d_created_by').html(data.created_by ? data.created_by : '-');
          $('#d_updated_at').html(data.updated_at ? format_tglnya(data.updated_at, 'waktu') : '-');
          $('#d_updated_by').html(data.updated_by ? data.updated_by : '-');
          $('#ajaxModalDetail').modal('show');
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
        html: "Permanently delete '<b><?= $name ?></b>'!",
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
