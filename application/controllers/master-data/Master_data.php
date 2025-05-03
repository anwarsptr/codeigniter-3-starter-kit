<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_data extends CI_Controller {

	public function index()
	{
			$role_id = get_session('role_id');
			$data = [
				'title' => 'Master Data',
        'result' => getMenuIndex($role_id, 'left', 7, 1),
			];
	    viewUsers('master-data/index', $data);
	}

}
