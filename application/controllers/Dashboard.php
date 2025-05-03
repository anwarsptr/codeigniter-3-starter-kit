<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function index()
	{
			if (empty(get_session('id_user'))) {
				redirect('auth/login');
			}

			$role_id = get_session('role_id');
			$warna_bg = ['danger','warning','success','info'];
			$data = [
				'result' => getMenuIndex($role_id, 'left', NULL, 1),
				'user_name' => get_session('name'),
				'random_bg' => $warna_bg[rand(0,3)]
			];
	    viewUsers('dashboard', $data);
	}

}
