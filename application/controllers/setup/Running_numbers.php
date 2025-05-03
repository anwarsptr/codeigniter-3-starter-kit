<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Running_numbers extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $result = $this->db->get('setup_running_numbers')->result_array();
        $data = [
  				'title' => "Setup Running Numbers",
  				'showPermission' => checkPermission("setup-running-numbers-edit", true),
  				'result' => $result
  			];
        viewUsers('setup/running-numbers', $data);
    }

  	public function save($id='') {
  		return model('setup/Running_numbers_model', 'save', $id);
  	}

}
