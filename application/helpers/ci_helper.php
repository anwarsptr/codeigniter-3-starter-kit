<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

///START SESSION
function set_session($nama='',$key='') //add or update session
{ $CI = &get_instance();
  if ($nama!='') { $CI->session->set_userdata($nama, $key); }
}

function get_session($ket='') //get session
{ $CI = &get_instance();
  if ($ket!='') { return $CI->session->userdata($ket); }
}

function del_session($ket='') //remove session
{ $CI = &get_instance();
  if ($ket!='') { $CI->session->has_userdata($ket); }
}

function del_all_session() //remove all session
{ $CI = &get_instance();
  $CI->session->sess_destroy();
}

function logout($redirect='')
{
  del_all_session();
  if ($redirect!='') {
    redirect($redirect);
  }
}
//END SESSION


//START POST
function clear_tag($postnya='')
{
  if (empty($postnya)) { return $postnya; }
  // Mengonversi ampersand ke placeholder
  $postnya = str_replace('&', '[@AMPERSAND@]', $postnya);
  // Membersihkan input dari tag HTML dan mengonversi karakter khusus menjadi entitas HTML
  $postnya = htmlentities(strip_tags($postnya));
  // Mengembalikan placeholder ke ampersand
  $postnya = str_replace('[@AMPERSAND@]', '&', $postnya);
  return trim($postnya);
}

function post($ket,$stt='')
{
  if (empty($_POST[$ket])) { return ''; }
  $postnya = $_POST[$ket];
  if ($stt=='1') {
    return $postnya;
  }else {
    return clear_tag($postnya);
  }
}

function post_all($no_post='')
{ $CI = &get_instance();
  $post = array();
  if (!is_array($no_post)) { $no_post = array($no_post); }
  // log_r($no_post);
  foreach ( $_POST as $key => $value )
  {
    if(!in_array($key,$no_post)){
      $post[$key] = post($key);
    }
  }
  return $post;
}
//END POST

//START MODEL
function model($model,$func='',$data='', $data2='', $data3='', $data4='', $data5='', $data6='', $data7='', $data8='', $data9='', $data10='')
{ $CI = &get_instance();
  if ($CI->load->model($model)) {
    $model = basename($model);
    return $CI->$model->$func($data, $data2, $data3, $data4, $data5, $data6, $data7, $data8, $data9, $data10);
  }else {
    return 'Function not found!';
  }
}
//END MODEL

//START VIEW
function view($view='', $vars=[], $return=FALSE)
{
	if($view==''){ return ''; }
	$CI = &get_instance();
	return $CI->load->view($view,$vars,$return);
}

function templateData($data=[]) {
	$web_app = app();
	$data['web_app'] = $web_app;
	$data['base_url'] = base_url();
	$views = $data['views'];
	$data['content'] = $views['content'];
	if (empty($data['title'])) {
		$data['title'] = ucwords(basename($data['content']));
	}
	unset($data['views']);
	return view($views['layouts'], $data, $views['return']);
}

function viewGuests($view='', $vars=[], $return=FALSE) {
	if($view==''){ return ''; }
	$page = 'guest';
	$vars['views'] = [
		'layouts' => "layouts/$page",
		'content' => "page_$page"."s/$view",
		'return' => $return,
	];
	return templateData($vars);
}

function viewAuths($view='', $vars=[], $return=FALSE) {
	if($view==''){ return ''; }
	$page = 'auth';
	$vars['views'] = [
		'layouts' => "layouts/$page",
		'content' => "page_$page"."s/$view",
		'return' => $return,
	];
	return templateData($vars);
}

function viewUsers($view='', $vars=[], $return=FALSE) {
	if($view==''){ return ''; }
	$page = 'user';
	$vars['views'] = [
		'layouts' => "layouts/$page",
		'content' => "page_$page"."s/$view",
		'return' => $return,
	];
	return templateData($vars);
}

function viewCheckContent($page='', $content='')
{
  $path = APPPATH."/views/page_$page"."s/$content.php";
  if (file_exists($path)) {
    return true;
  }
  redirect('404');
}
//END VIEW

//START URI
function uri($data='')
{ $CI = &get_instance();
  return $CI->uri->segment(preg_replace("/[^0-9]/","",$data));
}
//END URI


//CEK
//==== TABEL ====//
function list_tables(){
  $CI = &get_instance();
  return $CI->db->list_tables();
}

function table_exists($tbl=''){
  $CI = &get_instance();
  return $CI->db->table_exists($tbl);
}
//==== FIELD ====//
function list_fields($tbl=''){
  $CI = &get_instance();
  return $CI->db->list_fields($tbl);
}

function field_exists($tbl='',$field=''){
  $CI = &get_instance();
  return $CI->db->field_exists($field, $tbl);
}

function field_data($tbl=''){
  $CI = &get_instance();
  return $CI->db->field_data($tbl);
}

function get_field($tbl,$where_arr='') {
  $CI = &get_instance();
  return $CI->db->get_where($tbl, $where_arr)->row_array();
}

// CRUD
function add_data($tbl,$data)
{ $CI = &get_instance();
  return $CI->db->insert($tbl,$data);
}
function update_data($tbl,$data,$d1='')
{ $CI = &get_instance();
  return $CI->db->update($tbl,$data,$d1);
}
function delete_data($tbl,$where)
{ $CI = &get_instance();
  return $CI->db->delete($tbl,$where);
}

// BATCH
function add_batch($tbl,$data)
{ $CI = &get_instance();
  return $CI->db->insert_batch($tbl,$data);
}
function update_batch($tbl,$data,$id='')
{ $CI = &get_instance();
  return $CI->db->update_batch($tbl,$data,$id);
}
// BATCH


// Encrypt
function encode($string = '')
{
    try {
      $CI = &get_instance();
      $key = $CI->config->item('encryption_key');
      $key = hash('sha256', $key, true); // pastikan 256-bit key

      $iv = openssl_random_pseudo_bytes(16); // AES-256-CBC IV size = 16
      $encrypted = openssl_encrypt($string, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

      // gabungkan iv + encrypted, lalu base64
      $output = base64_encode($iv . $encrypted);

      // ganti karakter biar URL-safe
      $output = str_replace("/", "==11==", $output);
      $output = str_replace("+", "==22==", $output);
      return $output;
    } catch (\Exception $e) {
      return '';
    }

}

// Decrypt
function decode($string = '')
{
    try {
      $CI = &get_instance();
      $key = $CI->config->item('encryption_key');
      $key = hash('sha256', $key, true); // pastikan 256-bit key

      // balikkan karakter pengganti
      $string = str_replace("==11==", "/", $string);
      $string = str_replace("==22==", "+", $string);

      $data = base64_decode($string);
      $iv = substr($data, 0, 16);
      $encrypted = substr($data, 16);

      if (strlen($encrypted) < 16) { return ''; }

      $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
      return $decrypted;
    } catch (\Exception $e) {
      return '';
    }
}

// CSRF
function setCSRF()
{
  $CI = &get_instance();
  $name = $CI->security->get_csrf_token_name();
  $hash = $CI->security->get_csrf_hash();
  $string = json_encode([$name, $hash]);
  return encode($string);
}
