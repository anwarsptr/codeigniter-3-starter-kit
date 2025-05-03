<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_role extends CI_Controller {

    public $path;

    public function __construct() {
        parent::__construct();
        $this->path = "setup/manage-role";
    }

    public function index() {
        $path = $this->path;
        $data = [
  				'title' => "Setup Manage Role",
  				'showEdit' => checkPermission("setup-manage-role-edit", true),
          'urlnya' => $path,
  				'url_proses' => "$path/process/",
  			];
        viewUsers("$path/index", $data);
    }

    public function role_access($id_ori='') {
        $id = decode($id_ori);
        $roles = get_field('roles', ["id"=>$id]);
        if (empty($roles)) { redirect('404'); }

        $showAccess = checkPermission("setup-manage-role-edit-access", true);
        $isSave = ($id==1) ? false:$showAccess;

        $path = $this->path;
        $data = [
          'id_ori' => $id_ori,
  				'title' => "Setup Role Access",
          'showAccess' => $showAccess,
  				'showSort' => checkPermission("setup-manage-role-edit-sort", true),
          'list_menu_role_left' => getMenuWithPermissionsByRoleName($id, 'left', 1),
          'roles' => $roles,
          'isSave' => $isSave,
          'urlnya' => $path,
  				'url_proses' => "$path/process/",
  			];
        viewUsers("$path/role-access", $data);
    }

  	public function process($action='', $id='') {
  		return model('setup/Manage_role_model', $action, $id);
  	}

}
