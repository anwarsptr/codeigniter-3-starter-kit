<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup extends CI_Controller {

	public function index()
	{
			$role_id = get_session('role_id');
			$data = [
				'title' => 'Setup',
        'result' => getMenuIndex($role_id, 'left', 9, 1),
			];
	    viewUsers('setup/index', $data);
	}

}
