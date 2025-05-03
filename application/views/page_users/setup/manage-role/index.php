<section class="content-header">
  <h1> <i class="fa fa-lock"></i> <?= $title ?> <small></small>
  </h1>
  <ol class="breadcrumb">
    <li>
      <a href="dashboard"> <i class="fa fa-dashboard"></i> Dashboard </a>
    </li>
    <li><a href="setup"> <i class="fa fa-gears"></i> Setup </a></li>
    <li class="active"> <i class="fa fa-lock"></i> Manage Role</li>
  </ol>
</section>
<section class="content">

  <div class="box box-primary border-radius">
    <div class="box-body">
      <table id="fileData" class="table table-bordered table-striped" cellspacing="0" width="100%">
        <tr><td><i class="fa fa-spin fa-spinner text-warning"></i> Loading . . .</td></tr>
      </table>
    </div>
  </div>

</section>

<?php
view("page_users/setup/manage-role/modal_form");

view("includes/components/dataTables");
?>

<script type="text/javascript">
$(document).ready(function () {
  showData();
});

async function showData()
{
  columnWidths = ['30'];
  columnHeads = ['Name'];
  columns = ['name'];
  await prosesDatatable({
    url: '<?= $url_proses ?>get',
    columnHeads: columnHeads,
    columnWidths: columnWidths, //Persen
    columns: columns,
    actions: function(data, type, row) {
      id=data.id; btnAksi='';
        btnAksi += `<a href="<?= "$urlnya/role-access" ?>/${id}" data-placement="top" data-toggle="tooltip"
              class="btn btn-success btn-xs m3px"
              data-id="${id}" title="Edit Role Access">
              <i class="fa fa-edit"></i> Role Access
          </a>`;
      <?php if ($showEdit) : ?>
        btnAksi += `<a href="javascript:void(0)" data-placement="top" data-toggle="tooltip"
              class="btn btn-warning btn-xs editButton m3px"
              data-id="${id}" data-name="${data.name}" title="Edit">
              <i class="fa fa-edit"></i> Edit
          </a>`;
      <?php endif; ?>
      return btnAksi;
    }
  });
}

$(function () {

  <?php if ($showEdit) : ?>
  /* Modal Edit */
  $('body').on('click', '.editButton', async function () {
      var id = $(this).data('id');
      var name = $(this).data('name');
      $('#modelHeading').html("Edit Role");
      $('#form_id').val(id);
      $('#name').val(name);
      $('#dataForm').parsley().reset();
      $('#ajaxModal').modal('show');
  });

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
          setTimeout(function () { showData(); }, 2500);
        }
      });
  });
  <?php endif; ?>

});
</script>
