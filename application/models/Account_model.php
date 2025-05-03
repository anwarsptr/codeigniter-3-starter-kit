<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Account_model extends CI_Model {

  var $tbl = 'users';
  var $primaryKey = 'id';

  function save_profile()
  {
    $name = post('name');
    $username = strtolower(post('username'));
    $foto = "";
    if (empty($name)) { ResponseError('<b>Name</b> is required'); }
    if (empty($username)) { ResponseError('<b>Username</b> is required'); }

    $id = get_session('id_user');
    if (empty($id)) { ResponseError('Session Expired!'); }

    $tbl = $this->tbl;
    $primaryKey = $this->primaryKey;
    $get = get_field($tbl, "username='$username' AND $primaryKey!=$id");
    if (!empty($get)) {
      ResponseError("Username '<b>$username</b>' already exists");
    }

    $get = get_field($tbl, "$primaryKey=$id");
    $foto_old = $get['foto'];

    $path = "uploads/$tbl/".date('Y/m');
    $maxUploadFile = maxUploadFile('profile');
    upload_config($path,$maxUploadFile,'jpeg|jpg|png|gif|bmp');
    $foto_old = $get['foto'];
    $foto = upload_file('avatar', $path, 'ajax', $foto_old);
    if (!empty($foto['msg'])) { ResponseError($foto['msg']); }
    if ($foto != $foto_old) {
      $cek_resize = resizeImage($foto,$path);
      if ($cek_resize!=1) { $stt=0; ResponseError('Sorry, Photo Upload Failed!'); }
    }

    $up = update_data($tbl, ["name"=>$name, "username"=>$username, "foto"=>$foto, "updated_at"=>tgl_now(), "updated_by"=>get_session('name'), "updated_by_id"=>$id], "$primaryKey='$id'");
    if ($up) {
      delete_file($foto_old);
      set_session('name', $name);
      set_session('username', $username);
      if (!empty($foto)) {
        set_session('foto', $foto);
      }
      ResponseSuccess('Saved successfully');
    }else{
      delete_file($foto);
      ResponseError("Failed, try again in a few minutes");
    }
  }

  function save_changePassword()
  {
    $password0 = post('password0');
    $password1 = post('password1');
    $password2 = post('password2');
    if (empty($password0)) { ResponseError('<b>Old password</b> is required'); }
    if (empty($password1)) { ResponseError('<b>New password</b> is required'); }
    if (empty($password2)) { ResponseError('<b>Confirm new password</b> is required'); }
    if ($password1 != $password2) { ResponseError('<b>Confirm new password</b> is incorrect'); }

    $id = get_session('id_user');
    if (empty($id)) { ResponseError('Session Expired!'); }

    $tbl = $this->tbl;
    $primaryKey = $this->primaryKey;
    $get = get_field($tbl, "$primaryKey=$id");
    if (empty($get)) { ResponseError('Session Expired!'); }

    if (decode($get['password']) != $password0) {
      ResponseError('The <b>Old password</b> is incorrect');
    }

    $up = update_data($tbl, ["password"=>encode($password1)], "$primaryKey='$id'");
    if ($up) {
      ResponseSuccess('Saved successfully');
    }else{
      ResponseError("Failed, try again in a few minutes");
    }
  }

}
