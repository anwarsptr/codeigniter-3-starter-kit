<?php

function tgl_format($date,$format,$custom='')
{
  $date = (string) $date;
  if ($custom=='') {
    return date($format,strtotime($date));
  }else {
    return date($format,strtotime($custom, strtotime($date)));
  }
}

function hari_indo($tgl='')
{
  // Konversi tanggal ke Day
  $hari = date('D', strtotime($tgl));
  // Array nama hari
  $nama_hari = [
    'Sun'=>'Minggu',
    'Mon'=>'Senin',
    'Tue'=>'Selasa',
    'Wed'=>'Rabu',
    'Thu'=>'Kamis',
    'Fri'=>"Jum'at",
    'Sat'=>'Sabtu'
  ];
  // Jika $hari tidak sesuai dgn array
  if (empty($nama_hari[$hari])) {
    return 'Hari tidak valid!';
  }
  return $nama_hari[$hari]; //tampilkan hasil
}

function namaBulan()
{
  return [
    'Januari',   'Februari', 'Maret',    'April',
    'Mei',       'Juni',     'Juli',     'Agustus',
    'September', 'Oktober',  'November', 'Desember'
  ];
}
function bln_indo($tgl='')
{
  // Konversi tanggal ke bulan
  $bln = (int)date('m', strtotime($tgl));
  $bln = $bln-1;
  // Array nama bulan, kita coba buat lebih simpel
  $nama_bln = namaBulan();
  // Jika $bln tidak sesuai dgn array bulan
  if (empty($nama_bln[$bln])) {
    return 'Bulan tidak valid!';
  }
  return $nama_bln[$bln]; //tampilkan hasil
}

function tgl_now($aksi='')
{
  date_default_timezone_set('Asia/Jakarta');
  if ($aksi=='tgl') {
    $v = date('Y-m-d');
  }elseif ($aksi=='jam') {
    $v = date('H:i:s');
  }elseif ($aksi=='x') {
    $v = date('YmdHis');
  }else {
    $v = date('Y-m-d H:i:s');
  }
  return $v;
}

function waktu($tgl='', $aksi='')
{
  $tgl = ($tgl=='') ? tgl_now() : $tgl;
  // Konversinya :D
  $harinya = hari_indo($tgl);
  $tglnya  = date('d', strtotime($tgl));
  $blnnya  = bln_indo($tgl);
  $thnnya  = date('Y', strtotime($tgl));
  $jamnya   = date('H', strtotime($tgl));
  $menitnya = date('i', strtotime($tgl));
  $detiknya = date('s', strtotime($tgl));

  // kita buat lebih simpel menggunakan array
  $arr = [
    'hari'      => $harinya,
    'tgl'       => "$tglnya $blnnya $thnnya",
    'hari_tgl'  => "$harinya, $tglnya $blnnya $thnnya",
    'jam'       => $jamnya,
    'menit'     => $menitnya,
    'detik'     => $detiknya,
    'waktu'     => "$jamnya:$menitnya:$detiknya",
    'jam_menit' => "$jamnya:$menitnya",
    'hari_tgl_jam_menit' => $harinya.", $tglnya $blnnya $thnnya $jamnya:$menitnya",
  ];
  // Jika nama $aksi tidak ada dalam array maka tampilkan lengkapnya
  if (empty($arr[$aksi])) {
    return $harinya.", $tglnya $blnnya $thnnya $jamnya:$menitnya:$detiknya";
  }
  return $arr[$aksi]; //tampilkan hasil sesuai dgn $arr & $aksi
}

function khususAngka($number=0, $aksi='')
{
  if (empty($number)) { return 0; }
  return preg_replace("/[^0-9$aksi]+/", '', $number);
}

function format_angka($data=0,$data2='',$aksi='')
{
  $data = khususAngka($data, $aksi);
  $v = (empty($data)) ? 0:number_format("$data",0,",",".");
  if ($data2=='rp') {
    $v = "Rp. ".$v;
  }
  return $v;
}

function formatFromMB($megabytes, $precision = 0) {
    $units = ['KB', 'MB', 'GB', 'TB'];

    if ($megabytes < 1) {
        // Convert ke KB dan bulatkan ke bawah
        $kb = floor($megabytes * 1024);
        return $kb . ' KB';
    }

    $bytes = $megabytes * 1024 * 1024;
    $pow = floor(log($bytes) / log(1024));

    // Mulai dari MB (index 1)
    $pow = max(1, $pow);
    $pow = min($pow, count($units) - 1);

    $size = $bytes / pow(1024, $pow);
    return floor($size) . ' ' . $units[$pow];
}
