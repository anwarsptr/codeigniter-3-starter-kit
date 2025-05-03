<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Jenis_identitas_model extends CI_Model {

    var $tbl = 'md_jenis_identitas';
    var $primaryKey = 'id';
    var $permissionName = 'master-data-jenis-identitas';
    var $andDeleted = "deleted_at is null";

    public function generateNumber() {
      getGenerateNumber($this->permissionName.'-add', $this->tbl, 'kode');
    }

    public function get() {
        checkPermission($this->permissionName.'-show');
        $status = (!empty($_GET['status'])) ? $_GET['status']:0;
        $select = "id, kode, keterangan, created_at";
        $where = "is_active=$status AND $this->andDeleted";
        $data = [
          'tbl' => $this->tbl,
          'select' => $select,
          'where' => $where,
          'encrypt_id' => true,
        ];
        return json_datatables($data);
    }

    public function edit($getID='')
    {
        checkPermission($this->permissionName.'-edit');
        $id = decode($getID);
        $this->db->where("$this->andDeleted");
        $get = $this->db->get_where($this->tbl, ["$this->primaryKey"=>$id])->row_array();
        if (empty($get)) { ResponseError('Data not found'); }
        $get['id'] = $getID;
        ResponseSuccess($get);
    }

    public function detail($getID='')
    {
        checkPermission($this->permissionName.'-edit');
        $id = decode($getID);
        $this->db->where("$this->andDeleted");
        $get = $this->db->get_where($this->tbl, ["$this->primaryKey"=>$id])->row_array();
        if (empty($get)) { ResponseError('Data not found'); }
        $get['id'] = $getID;
        ResponseSuccess($get);
    }

    public function save()
    {
        $tbl = $this->tbl;
        $permissionName = $this->permissionName;
        $andDeleted = " AND $this->andDeleted";

        $form_id = post('form_id');
        $ifEdit = (!empty($form_id)) ? true : false;

        checkPermission($ifEdit ? $permissionName . '-edit' : $permissionName . '-add');

        $id = ($ifEdit) ? decode($form_id) : '';
        $isActive = checkPermission($permissionName . '-active', true);

        $kode = strtoupper(post('kode'));
        $keterangan = post('keterangan');

        if (empty($kode)) {
            ResponseError('<b>Kode</b> is required');
        }
        if (empty($keterangan)) {
            ResponseError('<b>Keterangan</b> is required');
        }

        if ($isActive) {
            if (post('is_active') === "") {
                ResponseError('<b>Active</b> is required');
            }
            $is_active = post('is_active');
            $is_active = (empty($is_active)) ? 0 : $is_active;
        } else {
            $is_active = 0;
        }

        $where_kode_old = '';
        if ($ifEdit) {
            $this->db->where('id', $id);
            $get = $this->db->get($tbl)->row_array();
            if (empty($get)) {
                ResponseError("Data not found");
            }
            $where_kode_old = " AND id != '$id'";
        }

        // Cek apakah kode sudah ada
        $this->db->where("kode", $kode);
        if ($where_kode_old != '') {
            $this->db->where("id !=", $id);
        }
        if (!empty($andDeleted)) {
            $this->db->where("deleted_at IS NULL", null, false);
        }
        $cekKode = $this->db->get($tbl)->row_array();

        if (!empty($cekKode)) {
            ResponseError("Kode '<b>$kode</b>' already exists");
        }

        $tgl_now = tgl_now();
        $input_by = get_session('name');
        $input_by_id = get_session('id_user');

        $post = [
            "kode" => $kode,
            "keterangan" => $keterangan,
            "is_active" => $is_active
        ];

        if ($ifEdit) {
            unset($post['kode']);
            $post = array_merge([
                "updated_at" => $tgl_now,
                "updated_by" => $input_by,
                "updated_by_id" => $input_by_id
            ], $post);

            $this->db->where('id', $id);
            $save = $this->db->update($tbl, $post);
        } else {
            $post = array_merge([
                "created_at" => $tgl_now,
                "created_by" => $input_by,
                "created_by_id" => $input_by_id
            ], $post);
            $save = $this->db->insert($tbl, $post);
        }

        if ($save) {
            ResponseSuccess('Saved successfully');
        } else {
            ResponseError("Failed, try again in a few minutes");
        }
    }

    public function delete($getID='')
    {
        checkPermission($this->permissionName.'-delete');
        $id = decode($getID);

        $tbl = $this->tbl; $primaryKey = $this->primaryKey;
        $get = get_field($tbl, "$primaryKey=$id");
        if (empty($get)) { ResponseError("Jenis Identitas not found"); }

        $this->db->trans_begin();
        $delete = delete_data($tbl, "$primaryKey='$id'");
        if ($delete) {
          $this->db->trans_commit();
          ResponseSuccess('Deleted successfully');
        }else{
          $this->db->trans_rollback();
          ResponseError("Failed, try again in a few minutes");
        }
    }

}
