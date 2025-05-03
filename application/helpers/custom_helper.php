<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function log_r($string = null, $var_dump = false)
{
  if ($var_dump) { var_dump($string); } else { echo "<pre>"; print_r($string); }
  exit;
}

function app(){
  return model('setup/Web_model', 'get');
}

function get3Initials($text="") {
    if ($text=="") return "";
    // Pisahkan teks berdasarkan spasi
    $words = explode(' ', $text);
    // Jika jumlah kata lebih dari atau sama dengan 3
    if (count($words) >= 3) {
        // Ambil huruf pertama dari setiap kata, maksimal 3 huruf
        $initials = strtoupper(substr($words[0], 0, 1)) .
                    strtoupper(substr($words[1], 0, 1)) .
                    strtoupper(substr($words[2], 0, 1));
    } else {
        // Jika kurang dari 3 kata, ambil 3 karakter pertama dari string
        $initials = strtoupper(substr($text, 0, 3));
    }
    return $initials;
}

function hasPermissionTo($roleId, $permissionName)
{
    $CI =& get_instance();

    // Cari ID permission berdasarkan nama permission
    $permission = $CI->db->get_where('permissions', ['name' => $permissionName])->row();
    if (!$permission) {
        return false; // Tidak ditemukan permission
    }

    // Cek apakah role memiliki permission
    $rolePermission = $CI->db->get_where('role_has_permissions', [
        'role_id' => $roleId,
        'permission_id' => $permission->id
    ])->row();

    return $rolePermission ? true : false;
}

function checkPermission($permissionName='', $return='')
{
  $isDataTables = (!empty($_GET['draw'])) ? true:false;
  $role_id = get_session('role_id');
  if (empty($role_id)) {
    if ($isDataTables) {
      echo "Session Expired!"; exit;
    }else if (isAjax()) {
      ResponseError('Session Expired!');
    }else {
      redirect('');
    }
  }
  $hasPermissionTo = hasPermissionTo($role_id, $permissionName);
  if ($hasPermissionTo) {
    return true;
  }else{
    if ($isDataTables) {
      echo "Permission Denied!"; exit;
    }else if (isAjax()) {
      ResponseError('Permission Denied!');
    }else {
      if ($return=='true') {
        return false;
      }else {
        redirect('404');
      }
    }
  }
}

function generateNumber($name='', $action='')
{
  if (empty($name)) { return ''; }
  $no_urut=1;
  if ($action=='x') {
    $value = $name;
  }else{
    $value = get_field('setup_running_numbers', ['name'=>$name]);
    if (empty($value)) { return ''; }
  }
  $random_allow = $value['random_allow'];
  if (empty($random_allow)) {
    $random_allow = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  }
  $type = $value['type'];
  $inisial = $value['inisial'];
  $length = $value['length'];
  $nomor = $inisial;
  if ($type==1) { // jika nomor urut
    if ($action!='x') {
      $tbl = str_replace('-', '_', $name);
      $CI =& get_instance();
      $CI->db->like('nomor', $nomor, 'after');
      $CI->db->order_by('nomor', 'DESC');
      $query = $CI->db->get($tbl, 1);
      $getNomor = $query->row_array();
      if (!empty($getNomor)) {
        $no_urut = (int) khususAngka($getNomor['nomor']) + 1;
      }
    }
    $nomor .= str_pad($no_urut, $length, '0', STR_PAD_LEFT);
  }else {
    $charactersLength = strlen($random_allow);
    for ($i = 0; $i < $length; $i++) {
        $nomor .= $random_allow[random_int(0, $charactersLength - 1)];
    }
  }
  return $nomor;
}

function getGenerateNumber($permissionName = "", $tbl = '', $field = '')
{
    $CI =& get_instance();
    // Cek parameter
    if (empty($permissionName)) { ResponseError("Permission invalid!"); }
    if (empty($tbl)) { ResponseError("Table invalid!"); }
    if (empty($field)) { ResponseError("Field invalid!"); }

    // Validasi permission dan CSRF
    checkPermission($permissionName);

    // Ambil parameter dari GET
    $name = $CI->input->get('name', TRUE);
    $nomor = generateNumber($name);

    $failed = false;

    for ($i = 0; $i < 10; $i++) {
        $CI->db->where($field, $nomor);
        $query = $CI->db->get($tbl);

        if ($query->num_rows() > 0) {
            $nomor = generateNumber($name);
            $failed = true;
        } else {
            $failed = false;
            break;
        }

        sleep(1);
    }

    if ($failed) {
        ResponseError(ucwords($field) . " sudah ada, dan tidak bisa digunakan lagi!");
    }

    ResponseSuccess($nomor);
}
