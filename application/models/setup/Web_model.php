<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Web_model extends CI_Model {

    var $tbl = 'setup_webs';
    var $primaryKey = 'id';

    public function get($field='', $aksi='') {
        $tbl=$this->tbl; $primaryKey=$this->primaryKey;
        if (in_array($field, ['','ALL'])) {
          return get_field($tbl, ["$primaryKey"=>1]);
        }else {
          if (field_exists($field, $tbl))
          {
            $get = get_field($tbl, ["$primaryKey"=>1])[$field];
            if (!empty($get)) { return $get; }
          }
          return $get;
        }
    }

    public function save()
    {
      $app_name = post('app_name');
      $title = post('title');
      $description = post('descriptions');
      $website = post('website');

      if (empty($app_name)) { ResponseError('<b>App Name</b> is required'); }
      if (empty($title)) { ResponseError('<b>Title</b> is required'); }

      $id = get_session('id_user');
      if (empty($id)) { ResponseError('Session Expired!'); }

      $tbl=$this->tbl; $primaryKey=$this->primaryKey;
      $get = get_field($tbl, "$primaryKey=1");
      $favicon_old = $get['favicon'];
      $logo_old = $get['logo'];

      $path = 'uploads/webs';
      $maxUploadFile = maxUploadFile('setup-webs');
      upload_config($path, $maxUploadFile, 'jpeg|jpg|png|gif|bmp');

      $favicon_old = $get['favicon'];
      $favicon = upload_file('favicon', $path, 'ajax', $favicon_old);

      $logo_old = $get['logo'];
      $logo = upload_file('logo', $path, 'ajax', $logo_old);

      $up = update_data($tbl, [
        "app_name"=>$app_name, "title"=>$title, "description"=>$description, "website"=>$website,
        "logo"=>$logo, "favicon"=>$favicon,
        "updated_at"=>tgl_now(), "updated_by"=>get_session('name'), "updated_by_id"=>$id
      ], "$primaryKey=1");

      if ($up) {
        if (!empty($favicon) && $favicon_old!=$favicon) {
          delete_file($favicon_old);
        }
        if (!empty($logo) && $logo_old!=$logo) {
          delete_file($logo_old);
        }
        ResponseSuccess('Saved successfully');
      }else{
        delete_file($favicon);
        delete_file($logo);
        ResponseError("Failed, try again in a few minutes");
      }
    }
}
