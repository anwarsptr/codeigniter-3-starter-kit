<section class="content-header">
  <h1> <i class="fa fa-circle-o"></i> <?= $title ?> <small></small> </h1>
  <ol class="breadcrumb">
    <li>
      <a href="dashboard"> <i class="fa fa-dashboard"></i> Dashboard </a>
    </li>
    <li><a href="master-data"> <i class="fa fa-puzzle-piece"></i> Master Data </a></li>
    <li class="active"> <i class="fa fa-circle-o"></i> <?= $name ?></li>
  </ol>
</section>

<section class="content">

  <div class="row">
    <div class="col-xs-12">
      <div class="nav-tabs-custom border-radius">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab_1" onclick="showData(1)" data-toggle="tab"> <i class="fa fa-check text-success"></i> Active</a></li>
          <li><a href="#tab_2" onclick="showData(0)" data-toggle="tab"> <i class="fa fa-ban text-danger"></i> Not Active</a></li>
          <?php if ($showAdd): ?>
            <li class="pull-right">
              <button type="button" id="createNew" class="btn btn-primary btn-sm border-radius">
                <i class="fa fa-plus" ></i>&nbsp; Add <?= $name ?>
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
  view("page_users/master-data/jenis-identitas/modal_form");
endif;

if ($showDetail) :
  view("page_users/master-data/jenis-identitas/modal_detail");
endif;

view("includes/components/dataTables");
view("includes/components/iCheck");
view("includes/components/select2");
view("page_users/master-data/jenis-identitas/script");
?>
