<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jenis_identitas extends CI_Controller {

  public $path;
  public $permissionName;

  public function __construct() {
      parent::__construct();
      $this->path = "master-data/jenis-identitas";
      $this->permissionName = "master-data-jenis-identitas";
  }

  public function index() {
      $permissionName = $this->permissionName;
      checkPermission($permissionName.'-show');
      $path = $this->path;
      $name = "Jenis Identitas";
      $data = [
        'title' => "Master Data $name",
        'name' => $name,
        'showAdd' => checkPermission($permissionName.'-add', true),
        'showEdit' => checkPermission($permissionName."-edit", true),
        'showDetail' => checkPermission($permissionName.'-detail', true),
        'showActive' => checkPermission($permissionName.'-active', true),
        'showDelete' => checkPermission($permissionName.'-delete', true),
        'urlnya' => $path,
        'url_proses' => "$path/process/",
      ];
      viewUsers("$path/index", $data);
  }

  public function process($action='', $id='') {
      return model('master_data/Jenis_identitas_model', $action, $id);
  }

}
