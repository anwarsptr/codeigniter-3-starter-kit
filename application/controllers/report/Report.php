<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends CI_Controller {

	public function index()
	{
			$role_id = get_session('role_id');
			$data = [
				'title' => 'Report',
        'result' => getMenuIndex($role_id, 'left', 5, 1),
			];
	    viewUsers('report/index', $data);
	}

}
