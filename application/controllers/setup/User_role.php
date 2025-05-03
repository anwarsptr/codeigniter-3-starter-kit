<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_role extends CI_Controller {

    public $path;

    public function __construct() {
        parent::__construct();
        $this->path = "setup/user-role";
    }

    public function index() {
        checkPermission('setup-user-role-show');
        $path = $this->path;
        $this->db->where('is_active=1 AND deleted_at is null');
        $this->db->order_by('order_by', 'ASC');
        $roles = $this->db->get('roles')->result_array();
        $data = [
  				'title' => "Setup User Role",
          'showAdd' => checkPermission('setup-user-role-add', true),
  				'showEdit' => checkPermission("setup-user-role-edit", true),
          'showActive' => checkPermission('setup-user-role-active', true),
          'showDelete' => checkPermission('setup-user-role-delete', true),
          'roles' => $roles,
          'urlnya' => $path,
  				'url_proses' => "$path/process/",
  			];
        viewUsers("$path/index", $data);
    }

    public function edit($id='')
    {
        return model('setup/User_role_model', 'edit', $id);
    }

    public function process($action='', $id='') {
  		  return model('setup/User_role_model', $action, $id);
  	}

}
