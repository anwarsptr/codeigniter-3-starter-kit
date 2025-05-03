<section class="content-header">
  <h1> <i class="fa fa-file"></i> <?= $title ?> <small></small>
  </h1>
  <ol class="breadcrumb">
    <li>
      <a href="dashboard"> <i class="fa fa-dashboard"></i> Dashboard </a>
    </li>
    <li> <a href="report"><i class="fa fa-print"></i> Report</a> </li>
    <li class="active"><i class="fa fa-file"></i> <?= $title ?></li>
  </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
          <form class="form-vertical" action="javascript:showDataFilter()" method="POST" id="form-submit" enctype="multipart/form-data" data-parsley-validate="true">
          <div class="box box-primary border-radius" style="padding:15px;">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-xs-6">
                    <div class="form-group has-feedback">
                        <label for="from_date" class="control-label">Dari Tanggal : <span class="text-danger">*</span></label>
                        <input type="text" class="form-control border-radius" name="from_date" id="from_date" value="" placeholder="dd-mm-yyyy" maxlength="10" readonly required style="background:white;">
                        <span class="glyphicon glyphicon-calendar form-control-feedback"></span>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-xs-6">
                    <div class="form-group has-feedback">
                        <label for="from_date" class="control-label">Sampai Tanggal : <span class="text-danger">*</span></label>
                        <input type="text" class="form-control border-radius" name="to_date" id="to_date" value="" placeholder="dd-mm-yyyy" maxlength="10" readonly required style="background:white;">
                        <span class="glyphicon glyphicon-calendar form-control-feedback"></span>
                    </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group has-feedback">
                      <label for="nama_pengunjung" class="control-label">Nama Pengunjung : </label>
                      <input type="text" class="form-control border-radius" id="nama_pengunjung" name="nama_pengunjung" placeholder="Enter Nama Pengunjung" value="" maxlength="100">
                      <span class="glyphicon glyphicon-user form-control-feedback"></span>
                  </div>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-sm btn-block border-radius"><i class="fa fa-search"></i> Search</button>
                </div>
            </div>
          </div>
          </form>
        </div>
    </div>

    <?php
    view("includes/components/select2");
    view("includes/components/datepicker");
    if ($showPrintPreview) {
      view("includes/components/report/printThis");
    }
    if ($showExportExcel) {
      view("includes/components/report/exportExcel");
    }
    if ($showExportPDF) {
      view("includes/components/report/exportPDF");
    }

    view('page_users/report/buku-tamu/table');

    view("page_users/report/buku-tamu/script");
    ?>

</section>
