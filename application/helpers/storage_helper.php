<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function createPath($path, $mode=0775, $aksi='return')
{
  if (!is_dir($path)) {
    mkdir("$path", $mode, true);
    return true;
  }else {
    if ($aksi=='return') {
      return false;
    }else {
      return true;
    }
  }
}

function hapus_multi_foto($foto=[])
{
  if (!empty($foto)) {
    if (is_array($foto)) {
      foreach ($foto as $key => $value) {
        if (file_exists($value)) { unlink($value); }
      }
    }else {
      if (file_exists($foto)) { unlink($foto); }
    }
  }
}

function delete_file($foto='', $dir='')
{
  if (!empty($foto)) {
    if (file_exists("$dir$foto")) { unlink("$dir$foto"); }
  }
  return true;
}

function checkImg($path='', $default='') {
	$imgDefault = 'img/none.jpeg';
	if ($default!='') {
		$default = "img/$default-default.png";
		if (file_exists($default)) {
			$imgDefault = $default;
		}
	}

	if (!empty($path) && file_exists($path)) {
		return $path;
	}else{
		return $imgDefault;
	}
}

function upload_config($path='',$size='',$tipe='')
{ $CI = &get_instance();
  if($path==''){ $path='uploads'; }
	createPath($path);
	if($tipe==''){$tipe='*';} if($size==''){$size=1;}
  $file_size = 1024 * $size;
  $CI->upload->initialize(array(
    "upload_path"   => "./$path",
    "allowed_types" => "$tipe",
    "max_size" => "$file_size",
    "remove_spaces" => TRUE,
    "encrypt_name" => TRUE,
  ));
}

function upload_file($filename='',$path='',$url='',$file='',$unlink_file=false)
{ $CI = &get_instance();
  // if(!file_exists($filename)){ return ''; }
  if($path==''){$path='uploads';}
  if (empty($_FILES[$filename])) {
    return $file;
  }else{
    if ($_FILES[$filename]['error'] <> 4) {
        if ( $CI->upload->do_upload($filename))
        {
          if ($unlink_file) {
            if (file_exists($file)) { unlink($file); }
          }
            $uploadData = $CI->upload->data();
            $filename = "$path/".$uploadData['file_name'];
            $file = preg_replace('/ /', '_', $filename);
            return $file;
        }else {
          $error = $CI->upload->display_errors();
          if ($url=='ajax') {
            return ['msg'=>$error];
          }else {
            return ["msg"=>$error, "url"=>"$url"];
          }
        }
    }else {
      return $file;
    }
  }
}


function upload_multi($filename='', $path='', $no_order='', $limit='', $ekstensi='', $nama='Foto', $foto_arr=[], $compress=true, $watermark=true)
{
  $fotonya=[];
  if (is_array($_FILES[$filename]['name']) || is_object($_FILES[$filename]['name']))
  {
    createPath("$path", 0775, 'cek');
    foreach ($_FILES[$filename]['name'] as $key => $value) {
      if ($_FILES[$filename]['error'][$key] <> 4) {
        $namafile  = $_FILES[$filename]['name'][$key];
        $tmp       = $_FILES[$filename]['tmp_name'][$key];
        $tipe_file = pathinfo($namafile, PATHINFO_EXTENSION);
        $ukuran    = $_FILES[$filename]['size'][$key] / 1024;
        $limit     = $limit * 1024;
        if ($ukuran > $limit) {
          $ukuranText = formatSizeUnits($ukuran, 'KB');
          hapus_multi_foto($fotonya);
          hapus_multi_foto($foto_arr);
          ResponseError('Ukuran '.$nama.' terlalu besar (<b>'.$ukuranText.'</b>)! maksimal <b class="text-danger">'.formatSizeUnits($limit, 'KB').'</b> perfoto.');
        }
        if(!in_array(strtolower($tipe_file), $ekstensi)){
          hapus_multi_foto($fotonya);
          hapus_multi_foto($foto_arr);
          ResponseError($tipe_file.' Ekstensi '.$nama.' Tidak Diperbolehkan! Ekstensi yang dibolehkan : jpeg|jpg|png');
        }
        $foto = $path.'/'.md5("$filename $namafile $no_order ".time().$key).".$tipe_file";
        $upload = move_uploaded_file($tmp, $foto);
        if ($upload) {
          $fotonya[] = $foto;
        }else {
          hapus_multi_foto($fotonya);
          hapus_multi_foto($foto_arr);
          ResponseError('Maaf, Upload '.$nama.' Gagal!');
        }
      }
    }
  }
  return $fotonya;
}


function resizeImage($filename='',$path='')
{ $CI = &get_instance();
  if(!file_exists($filename)){ return '1'; }
  if (empty($_FILES[$filename])) { return '1'; }
  if($path==''){$path='uploads';}
    $source_path = './'.$filename;
    $target_path = './'.$path.'/';
    $default_width  = 600;
    $default_height = 600;
    $config_manip = array(
        'image_library' => 'gd2',
        'source_image'  => $source_path,
        'create_thumb'  => FALSE,
        'maintain_ratio' => TRUE,
        'quality'       => '90%',
        'width'         => $default_width,
        'height'        => $default_height,
        'master_dim'    => 'auto',
        'new_image'     => $target_path,
    );
    $CI->image_lib->initialize($config_manip);
    if ($CI->image_lib->resize()) {
      $CI->image_lib->clear();
      if (file_exists($filename)) { unlink($filename); }
      return '1';
    }else{
      $CI->image_lib->clear();
      return $CI->image_lib->display_errors();
    }
}


function maxUploadFile($aksi='')
{
  // satuan MB
  if (in_array($aksi, ['profile','setup-webs'])) {
    return 0.2;
  }elseif (in_array($aksi, ['buku-tamu'])) {
    return 0.2;
  }
  return 0.1;
}
