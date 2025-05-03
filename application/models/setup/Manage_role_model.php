<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_role_model extends CI_Model {

    var $tbl = 'roles';
    var $primaryKey = 'id';

    public function get() {
        checkPermission('setup-manage-role-show');
        $data = [
          'tbl' => $this->tbl,
          'select' => "$this->primaryKey, name",
          'search' => ["name"],
          'where' => "is_active=1",
          'orderBy' => "order_by ASC",
          'encrypt_id' => true,
        ];
        return json_datatables($data);
    }

    public function save()
    {
      $form_id = post('form_id');
      $id = decode($form_id);
      $name = post('name');
      if (empty($name)) { ResponseError('<b>Name</b> is required'); }
      $tbl = $this->tbl; $primaryKey = $this->primaryKey;
      $get = get_field("$tbl", "$primaryKey=$id");
      if (empty($get)) { ResponseError("Role not found"); }

      $getUnique = get_field("$tbl", "$primaryKey!=$id AND name='$name'");
      if (!empty($getUnique)) { ResponseError("Role '<b>$name</b>' already exists!"); }

      $tgl_now=tgl_now(); $input_by=get_session('name'); $input_by_id=get_session('id_user');
      $post = ["name"=>$name, "updated_at"=>$tgl_now, "updated_by"=>$input_by, "updated_by_id"=>$input_by_id];
      $save = update_data($tbl, $post, "$primaryKey='$id'");
      if ($save) {
        ResponseSuccess('Saved successfully');
      }else{
        ResponseError("Failed, try again in a few minutes");
      }
    }

    public function saveRole()
    {
      $id = decode(post('id'));
      $tbl = $this->tbl; $primaryKey = $this->primaryKey;
      $get = get_field($tbl, "$primaryKey=$id");
      if (empty($get)) { ResponseError('Role not found'); }
      $up=false;
      $this->db->trans_begin();
      $up = delete_data('role_has_permissions', "role_id=$id");
      if ($up) {
        $i=1;
        foreach ($_POST as $key => $value) {
          if ($key !== 'id') {
            $akses = explode('_', $key);
            if (isset($akses[1])) {
              // Menambahkan permission ke role
              $permission_id = $akses[1];
              $data = ['role_id'=>$id, 'permission_id'=>$permission_id, '`order_by`'=>$i];
              $up = add_data('role_has_permissions', $data);
              if (!$up) {
                $this->db->trans_rollback();
                ResponseError("Failed to insert role has permissions");
              }
              $i++;
            }
          }
        }
      }
      if ($up) {
        $this->db->trans_commit();
        ResponseSuccess('Saved successfully');
      }else {
        rollback();
        ResponseError("Failed, try again in a few minutes");
      }
    }

    public function saveSort()
    {
      $id = decode(post('id'));
      $tbl = $this->tbl; $primaryKey = $this->primaryKey;
      $get = get_field($tbl, "$primaryKey=$id");
      if (empty($get)) { ResponseError('Role not found'); }
      $i=1; $up=false;
      foreach ($_POST['item'] as $val) {
        $menu = $this->db->get_where('menus', ['id' => $val, 'is_active' => 1])->row_array();
        if (!empty($menu)) {
          $permissions = $this->db->get_where('permissions', ['id_menu' => $val])->result_array();
          if (!empty($permissions)) {
            $showPermission = firstWhere($permissions, "short_name", "Show");
            if ($showPermission) {
              $permission_id = $showPermission['id'];
              $up = update_data('role_has_permissions', ['`order_by`'=>$i], "permission_id=$permission_id AND role_id=$id");
            }
          }
          $i++;
        }
      }
      if ($up) {
        ResponseSuccess('Saved successfully');
      }else {
        ResponseError("Failed, try again in a few minutes");
      }
    }

}
