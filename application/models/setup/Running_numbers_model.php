<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Running_numbers_model extends CI_Model {

    var $tbl = 'setup_running_numbers';
    var $primaryKey = 'id';

    public function save($id='')
    {
      $tbl = $this->tbl; $primaryKey = $this->primaryKey;
      $idnya = decode($id);
      $get = get_field($tbl, "$primaryKey='$idnya'");
      if (empty($get)) {
        ResponseError("The <b>running number</b> to be saved was not found");
      }
      $inisial = strtoupper(post('inisial'));
      $length = khususAngka(post('length'));
      $type = khususAngka(post('type'));
      $type = (empty($type)) ? 0:$type;
      $random_allow = strtoupper(post('random_allow'));

      if (empty($inisial)) { ResponseError('<b>Inisial</b> is required'); }
      if (empty($length)) { ResponseError('<b>Length</b> is required'); }
      if (empty($random_allow)) { ResponseError('<b>Random Allow</b> is required'); }

      $up = update_data($tbl, [
        "inisial"=>$inisial, "length"=>$length, "type"=>$type, "random_allow"=>$random_allow,
        "updated_at"=>tgl_now(), "updated_by"=>get_session('name'), "updated_by_id"=>get_session('id_user')
      ], "id=$idnya");

      if ($up) {
        ResponseSuccess('Saved successfully');
      }else{
        ResponseError("Failed, try again in a few minutes");
      }
    }
}
