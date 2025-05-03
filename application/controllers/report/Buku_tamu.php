<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Buku_tamu extends CI_Controller {

	public function index()
	{
			$role_id = get_session('role_id');
			$data = [
				'title' => 'Report Buku Tamu',
				'url_proses' => 'report/buku-tamu/process/',
				'showPrintPreview' => checkPermission('report-buku-tamu-show', true),
				'showExportExcel' => checkPermission('report-buku-tamu-export-excel', true),
				'showExportPDF' => checkPermission('report-buku-tamu-export-pdf', true),
			];
	    viewUsers('report/buku-tamu/index', $data);
	}

	public function process($value='')
	{
		return model('Buku_tamu_model', $value);
	}

}
