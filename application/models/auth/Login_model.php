<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model {

  var $tbl = 'users';
  var $primaryKey = 'id';

  function process()
  {
      $username = post('username');
      $password = post('password');
      if (empty($username)) { ResponseError('<b>Username</b> is required'); }
      if (empty($password)) { ResponseError('<b>Password</b> is required'); }

      $tbl = $this->tbl;
      $primaryKey = $this->primaryKey;
      $get = get_field($tbl, ['username'=>$username]);
      if (empty($get)) {
        ResponseError("Username '<b>$username</b>' not found!");
      }

      $isActive = $get['is_active'];
      if ($isActive==0) { ResponseError('Inactive Account!'); }
      if ($isActive==2) { ResponseError('Account is BANNED!'); }
      if ($password != decode($get['password'])) { ResponseError('Incorrect <b>Username</b> or <b>Password</b>'); }

      $id_user = $get['id'];
      update_data($tbl, ['last_login'=>tgl_now()], ["$primaryKey"=>$id_user]);
      set_session('id_user', $id_user);
      set_session('name', $get['name']);
      set_session('username', $username);
      set_session('foto', (!empty($get['foto']) && file_exists($get['foto'])) ? $get['foto']:'' );
      set_session('role_id', $get['role_id']);
      ResponseSuccess('Welcome and have a good activity :D');
  }

}
