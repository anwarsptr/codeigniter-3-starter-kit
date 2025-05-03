<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {

	public function logout()
	{
			logout('auth/login');
	}

	public function index($path='')
	{
			$id_user = get_session('id_user');
			if (empty($id_user)) {
				redirect('auth/login');
			}

			$content = "account/$path";
			viewCheckContent("user", $content);

			$query=[];
			if ($path=='profile') {
				$query = get_field('users', ['id'=>$id_user]);
			}

			$data = [
				'title' => ucwords(preg_replace('/[_-]/',' ',$path)),
				'showPermission' => checkPermission("account-$path-edit", true),
				'query' => $query
			];
			viewUsers($content, $data);
	}


	public function save($value='')
	{
			return model('Account_model', "save_$value");
	}

}
