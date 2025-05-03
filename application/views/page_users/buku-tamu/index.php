<section class="content-header">
  <h1> <i class="fa fa-book"></i> <?= $title ?> <small></small>
  </h1>
  <ol class="breadcrumb">
    <li>
      <a href="dashboard"> <i class="fa fa-dashboard"></i> Dashboard </a>
    </li>
    <li class="active"> <i class="fa fa-book"></i> <?= $title ?></li>
  </ol>
</section>

<section class="content">

  <div class="row">
    <div class="col-xs-12">
      <div class="nav-tabs-custom border-radius">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab_1" onclick="showData(0)" data-toggle="tab"> <i class="fa fa-spin fa-spinner text-warning"></i> Belum kembalikan Tanda Masuk</a></li>
          <li><a href="#tab_2" onclick="showData(1)" data-toggle="tab"> <i class="fa fa-check text-success"></i> Sudah kembalikan Tanda Masuk</a></li>
          <?php if ($showAdd): ?>
            <li class="pull-right">
              <button type="button" id="createNew" class="btn btn-primary btn-sm border-radius pull-right" style="margin:10px;">
                <i class="fa fa-plus" ></i>&nbsp; Add <?= $title ?>
              </button>
            </li>
          <?php endif; ?>
        </ul>
        <div class="tab-content table-responsive">
          <table id="fileData" class="table table-bordered table-striped" cellspacing="0" width="100%">
            <tr><td><i class="fa fa-spin fa-spinner text-warning"></i> Loading . . .</td></tr>
          </table>
        </div>
      </div>
    </div>
  </div>

</section>

<?php
if ($showAdd || $showEdit) :
  view("page_users/buku-tamu/modal_form");
endif;

if ($showDetail) :
  view("page_users/buku-tamu/modal_detail");
endif;

view("includes/components/dataTables");
view("includes/components/dataTables");
view("includes/components/select2");
view("includes/components/datepicker");
view("includes/components/timepicker");
view("includes/components/iCheck");

view("page_users/buku-tamu/script");
?>
