<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webs extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $query = get_field('setup_webs', ["id"=>1]);
        $data = [
  				'title' => "Setup Webs",
  				'showPermission' => checkPermission("setup-website-edit", true),
  				'query' => $query
  			];
        viewUsers('setup/webs', $data);
    }

  	public function save() {
  		return model('setup/Web_model', 'save');
  	}

}
