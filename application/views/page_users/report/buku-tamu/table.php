<?php $app_name = @$web_app['app_name']; ?>
<div class="row" id="vTable" style="display: none">
    <div class="col-md-12">
      <div class="box box-primary border-radius" style="padding:15px;">
        <div class="row">
            <div class="col-md-6">
                <?php if ($showPrintPreview) : ?>
                <button type="button" class="btn bg-navy btn-sm border-radius" id="btnPrint" style="display:none"><i class="fa fa-print"></i> Print</button>
                <?php endif ?>
                <?php if ($showExportExcel) : ?>
                <button type="button" class="btn btn-success btn-sm border-radius" id="btnExportExcel" style="display:none"><i class="fa fa-file-excel-o"></i> Export Excel</button>
                <?php endif ?>
                <?php if ($showExportPDF) : ?>
                <button type="button" class="btn btn-danger btn-sm border-radius" id="btnExportPDF" style="display:none"><i class="fa fa-file-pdf-o"></i> Export PDF</button>
                <?php endif ?>
            </div>
            <div class="col-md-6 text-right" style="padding-top:5px"><span id="select_tgl"></span></div>
        </div>
        <div style="max-height: 500px; overflow-y: auto;margin-top:10px">
            <div class="printArea" id="printArea">
                <div id="printHeader" style="display:none">
                    <b id="printHeaderTitle"><?= $app_name ?></b><br>
                    <span id="printHeaderSubtitle"><?= $title ?></span><br>
                    <span id="printHeaderTgl"></span>
                    <br>
                </div>
                <table id="dataTable" class="table table-bordered table-striped table-hover" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center bg-primary text-white" width="5%">No</th>
                            <th class="text-center bg-primary text-white" width="10%">No&nbsp;Buku&nbsp;Tamu</th>
                            <th class="text-center bg-primary text-white" width="20%">Tanggal&nbsp;Kunjungan</th>
                            <th class="text-center bg-primary text-white" width="25%">Nama&nbsp;Tamu</th>
                            <th class="text-center bg-primary text-white" width="10%">No&nbsp;Telp</th>
                            <th class="text-center bg-primary text-white" width="20%">Jenis&nbsp;Identitas</th>
                            <th class="text-center bg-primary text-white" width="10%">No&nbsp;Kendaraan</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
      </div>
    </div>
</div>
