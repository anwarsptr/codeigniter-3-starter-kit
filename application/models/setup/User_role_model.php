<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_role_model extends CI_Model {

    var $tbl = 'users';
    var $tbl2 = 'roles';
    var $primaryKey = 'id';
    var $andDeleted = "users.deleted_at is null";

    public function get() {
        checkPermission('setup-user-role-show');
        $id = get_session('id_user');
        $status = (!empty($_GET['status'])) ? $_GET['status']:0;
        $tbl = $this->tbl;
        $tbl2 = $this->tbl2;
        $join[] = ["$tbl2", "$tbl.role_id=$tbl2.id"];
        $select = "$tbl.id, $tbl.username, $tbl.name, $tbl.last_login, $tbl2.name as role_name";
        $where = "$tbl.is_active=$status AND $tbl.id!=$id AND $this->andDeleted";
        $data = [
          'tbl' => $tbl,
          'select' => $select,
          'join' => $join,
          'where' => $where,
          'encrypt_id' => true,
        ];
        return json_datatables($data);
    }

    public function edit($getID='')
    {
        checkPermission('setup-user-role-edit');
        $id = decode($getID);
        $this->db->where("$this->andDeleted");
        $get = $this->db->get_where($this->tbl, ["$this->primaryKey"=>$id])->row_array();
        if (empty($get)) { ResponseError('Data not found'); }
        $get['id'] = $getID;
        ResponseSuccess($get);
    }

    public function save($value='')
    {
      $form_id = post('form_id');
      $ifEdit = (!empty($form_id)) ? true:false;
      checkPermission($ifEdit ? 'setup-user-role-edit':'setup-user-role-add');

      $id = ($ifEdit) ? decode($form_id):'';
      $name = post('name');
      $username = strtolower(post('username'));
      $password = post('password');
      $role_id = post('role_id');
      $is_active = post('is_active');
      $is_active = (empty($is_active)) ? 0:$is_active;

      if (empty($name)) { ResponseError('<b>Name</b> is required'); }
      if (empty($username)) { ResponseError('<b>Username</b> is required'); }
      if (!$ifEdit && empty($password)) { ResponseError('<b>Password</b> is required'); }
      if (empty($role_id)) { ResponseError('<b>Role</b> is required'); }

      $tbl = $this->tbl; $primaryKey = $this->primaryKey;
      $where_username_old='';
      if ($ifEdit) {
        if ($id==1) { ResponseError("Permission Denied!"); }
        $get = get_field($tbl, ["id"=>$id]);
        if (empty($get)) {
          ResponseError("Users not found");
        }
        $where_username_old = "id!=$id";
        if (empty($password)) {
          $password = decode($get['password']);
        }
      }

      if (!empty($where_username_old)) {
        $this->db->where($where_username_old);
      }
      $get = get_field($tbl, ["username"=>$username]);
      if (!empty($get)) {
        ResponseError("Username '<b>$username</b>' already exists");
      }

      $tgl_now=tgl_now(); $input_by=get_session('name'); $input_by_id=get_session('id_user');
      $post = ["name"=>$name, "username"=>$username, "password"=>encode($password), "role_id"=>$role_id, "is_active"=>$is_active];
      if ($ifEdit) {
        $post = array_merge(["updated_at"=>$tgl_now, "updated_by"=>$input_by, "updated_by_id"=>$input_by_id], $post);
        $save = update_data('users', $post, "id='$id'");
      }else {
        $post = array_merge(["created_at"=>$tgl_now, "created_by"=>$input_by, "created_by_id"=>$input_by_id], $post);
        $save = add_data('users', $post);
      }

      if ($save) {
        ResponseSuccess('Saved successfully');
      }else{
        ResponseError("Failed, try again in a few minutes");
      }
    }

    public function delete($getID='')
    {
        checkPermission('setup-user-role-delete');
        $id = decode($getID);
        if ($id==1) { ResponseError("Permission Denied!"); }

        $tbl = $this->tbl; $primaryKey = $this->primaryKey;
        $get = get_field($tbl, "$primaryKey=$id");
        if (empty($get)) { ResponseError("User not found"); }

        $foto = $get['foto'];
        $this->db->trans_begin();
        $delete = delete_data($tbl, "$primaryKey='$id'");
        if ($delete) {
          $this->db->trans_commit();
          delete_file($foto);
          ResponseSuccess('Deleted successfully');
        }else{
          $this->db->trans_rollback();
          ResponseError("Failed, try again in a few minutes");
        }
    }

}
