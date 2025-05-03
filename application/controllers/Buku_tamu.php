<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Buku_tamu extends CI_Controller {

  public $path;
  public $permissionName;

  public function __construct() {
      parent::__construct();
      $this->path = "buku-tamu";
      $this->permissionName = "buku-tamu";
  }

  public function index() {
      $permissionName = $this->permissionName;
      checkPermission($permissionName.'-show');
      $path = $this->path;
      $name = "Buku Tamu";
      $this->db->where("deleted_at is null");
      $getJenisIdentitas = $this->db->get_where('md_jenis_identitas', ['is_active'=>1])->result_array();
      $data = [
        'title' => $name,
        'name' => $name,
        'showAdd' => checkPermission($permissionName.'-add', true),
        'showEdit' => checkPermission($permissionName."-edit", true),
        'showDetail' => checkPermission($permissionName.'-detail', true),
        'showDelete' => checkPermission($permissionName.'-delete', true),
        'urlnya' => $path,
        'url_proses' => "$path/process/",
        'getJenisIdentitas' => $getJenisIdentitas,
      ];
      viewUsers("$path/index", $data);
  }

  public function process($action='', $id='') {
      return model('Buku_tamu_model', $action, $id);
  }

}
