<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Buku_tamu_model extends CI_Model {

  var $tbl = 'buku_tamu';
  var $primaryKey = 'id';
  var $permissionName = 'buku-tamu';
  var $andDeleted = "deleted_at is null";

  public function generateNumber()
  {
      getGenerateNumber($this->permissionName.'-add', $this->tbl, 'nomor');
  }

  public function getData()
  {
      $tgl_1 = post('from');
      $tgl_2 = post('to');
      if (empty($tgl_1)) { ResponseError('<b>Dari Tanggal</b> wajib diisi'); }
      if (empty($tgl_2)) { ResponseError('<b>Sampai Tanggal</b> wajib diisi'); }
      $tgl_1 = tgl_format($tgl_1, 'Y-m-d');
      $tgl_2 = tgl_format($tgl_2, 'Y-m-d');

      $tbl = $this->tbl; $primaryKey = $this->primaryKey;

      $nama_pengunjung = post('nama_pengunjung');

      $select = "nomor, tgl_kunjungan, nama_tamu, no_telp_tamu, jenis_identitas, nomor_kendaraan";
      $where = "tgl_kunjungan BETWEEN '$tgl_1' AND '$tgl_2' AND $this->andDeleted";
      if (!empty($nama_pengunjung)) {
        $where .= " AND nama_tamu = '$nama_pengunjung'";
      }
      $this->db->select($select);
      $this->db->where($where);
      $getData = $this->db->get($tbl)->result_array();
      if (empty($getData)) {
        ResponseError("Data <b>Buku Tamu</b> tidak ditemukan!");
      }

      ResponseSuccess($getData);
  }

  public function get() {
      checkPermission($this->permissionName.'-show');
      $status = (!empty($_GET['status'])) ? $_GET['status']:0;
      $select = "id, foto_kendaraan, nomor, tgl_kunjungan, jam_kunjungan, nama_tamu, no_telp_tamu, nomor_kendaraan, jenis_identitas, nama_yang_dikunjungi";
      $where = "no_tanda_masuk_dikembalikan=$status AND $this->andDeleted";
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
      checkPermission($this->permissionName.'-detail');
      $id = decode($getID);
      $this->db->where("$this->andDeleted");
      $get = $this->db->get_where($this->tbl, ["$this->primaryKey"=>$id])->row_array();
      if (empty($get)) { ResponseError('Data not found'); }
      $get['id'] = $getID;
      ResponseSuccess($get);
  }

  public function save()
  {
      $form_id = post('form_id');
      $ifEdit = !empty($form_id);
      $permission = $ifEdit ? 'edit' : 'add';

      checkPermission("$this->permissionName-$permission");

      $tbl = $this->tbl;
      $id = $ifEdit ? decode($form_id) : '';

      $no_tanda_masuk_dikembalikan = post('no_tanda_masuk_dikembalikan');
      $foto_tanda_pengenal_old=[]; $foto_kendaraan_old=[];
      if (!$ifEdit) {
          $nomor = post('nomor');
          $tgl_kunjungan = post('tgl_kunjungan');
          $jam_kunjungan = post('jam_kunjungan');
          $nama_tamu = post('nama_tamu');
          $no_telp_tamu = preg_replace('/\D/', '', post('no_telp_tamu'));
          $membawa_kendaraan = post('membawa_kendaraan');

          $no1 = post('no1_kendaraan');
          $no2 = post('no2_kendaraan');
          $no3 = post('no3_kendaraan');
          $nomor_kendaraan = ($no1 && $no2 && $no3) ? strtoupper("$no1 $no2 $no3") : '';

          $jenis_identitas_id = post('jenis_identitas_id');
          $blok_perumahan_id = post('blok_perumahan_id');
          $nama_yang_dikunjungi = post('nama_yang_dikunjungi');
          $no_tanda_masuk = post('no_tanda_masuk');
          $keterangan = post('keterangan');

          $isKendaraan = in_array($membawa_kendaraan, ['YES','YA']) ? true:false;

          if (empty($nomor)) return ResponseError('Nomor is required');
          if (empty($tgl_kunjungan)) return ResponseError('Tanggal Kunjungan is required');
          if (empty($jam_kunjungan)) return ResponseError('Jam Kunjungan is required');
          if (empty($nama_tamu)) return ResponseError('Nama Tamu is required');
          if (empty($no_telp_tamu)) return ResponseError('Nomor Telp Tamu is required');
          if (empty($membawa_kendaraan)) return ResponseError('Membawa Kendaraan is required');
          if ($isKendaraan && empty($nomor_kendaraan)) {
              return ResponseError('Nomor Kendaraan is required');
          }
          if (empty($jenis_identitas_id)) return ResponseError('Jenis Identitas is required');
          if (empty($nama_yang_dikunjungi)) return ResponseError('Nama yang dikunjungi is required');
          if (empty($no_tanda_masuk)) return ResponseError('No Tanda Masuk is required');

          $getJI = get_field('md_jenis_identitas', ['id'=>$jenis_identitas_id]);
          if (!$getJI) return ResponseError("Jenis Identitas not found");

          $this->db->where("$this->andDeleted");
          $get = get_field($tbl, ['nomor'=>$nomor]);
          if (!empty($get)) {
              return ResponseError("Nomor '<b>$nomor</b>' already exists");
          }

          $foto_arr=[];
          $foto_kendaraan="";
          if ($isKendaraan) {
            if (!empty($_FILES['foto_kendaraan']['name'][0])) {
              $jml_foto = count($foto_kendaraan_old) + count($_FILES['foto_kendaraan']['name']);
              if ($jml_foto > 10) {
                ResponseError("Total Foto yang diupload tidak boleh lebih dari <b>10</b>");
              }

              $path = 'uploads/buku-tamu/kendaraan/'.date('Y/m');
              $maxUpload = maxUploadFile('buku-tamu');
              $ekstensi = ['png','jpg','jpeg'];
              $foto_arr = upload_multi('foto_kendaraan', $path, $nomor, $maxUpload, $ekstensi, 'Foto Kendaraan');
              if (!empty($foto_arr) && !empty($foto_kendaraan_old)) {
                $foto_arr = array_merge($foto_kendaraan_old, $foto_arr);
              }
              $foto_kendaraan = (!empty($foto_arr)) ? json_encode($foto_arr):"";
            }else {
              if ($ifEdit) {
                $foto_kendaraan = (!empty($foto_kendaraan_old)) ? json_encode($foto_kendaraan_old):"";
              }else {
                ResponseError("Foto Kendaraan is required");
              }
            }
          }

          $foto_tanda_pengenal="";
          if (!empty($_FILES['foto_tanda_pengenal']['name'][0])) {
            $jml_foto = count($foto_tanda_pengenal_old) + count($_FILES['foto_tanda_pengenal']['name']);
            if ($jml_foto > 10) {
              ResponseError("Total Foto yang diupload tidak boleh lebih dari <b>10</b>");
            }

            $path = 'uploads/buku-tamu/tanda_pengenal/'.date('Y/m');
            $maxUpload = maxUploadFile('buku-tamu');
            $ekstensi = ['png','jpg','jpeg'];
            $foto_arr = upload_multi('foto_tanda_pengenal', $path, $nomor, $maxUpload, $ekstensi, 'Foto Tanda Pengenal');
            if (!empty($foto_arr) && !empty($foto_tanda_pengenal_old)) {
              $foto_arr = array_merge($foto_tanda_pengenal_old, $foto_arr);
            }
            $foto_tanda_pengenal = (!empty($foto_arr)) ? json_encode($foto_arr):"";
          }else {
            $foto_tanda_pengenal = (!empty($foto_tanda_pengenal_old)) ? json_encode($foto_tanda_pengenal_old):"";
          }

          $data = [
              'nomor' => $nomor,
              'tgl_kunjungan' => date('Y-m-d', strtotime($tgl_kunjungan)),
              'jam_kunjungan' => date('H:i:s', strtotime($jam_kunjungan)),
              'nama_tamu' => $nama_tamu,
              'no_telp_tamu' => $no_telp_tamu,
              'membawa_kendaraan' => $membawa_kendaraan,
              'nomor_kendaraan' => $nomor_kendaraan,
              'jenis_identitas_id' => $jenis_identitas_id,
              'jenis_identitas' => $getJI['kode'] . ' - ' . $getJI['keterangan'],
              'nama_yang_dikunjungi' => $nama_yang_dikunjungi,
              'no_tanda_masuk' => $no_tanda_masuk,
              'no_tanda_masuk_dikembalikan' => 0,
              'keterangan' => $keterangan,
              'foto_kendaraan' => !empty($foto_kendaraan) ? $foto_kendaraan:null,
              'foto_tanda_pengenal' => !empty($foto_tanda_pengenal) ? $foto_tanda_pengenal:null,
              'created_by' => get_session('name'),
              'created_by_id' => get_session('id_user'),
              'created_at' => tgl_now()
          ];

          $simpan = $this->db->insert($tbl, $data);
      } else {
          $data = [
              'no_tanda_masuk_dikembalikan' => $no_tanda_masuk_dikembalikan == 1 ? 1 : 0,
              'updated_by' => get_session('name'),
              'updated_by_id' => get_session('id_user'),
              'updated_at' => tgl_now()
          ];
          $this->db->where('id', $id);
          $simpan = $this->db->update($tbl, $data);
      }

      if ($simpan) {
          ResponseSuccess('Saved successfully');
      } else {
          ResponseError('Failed, try again in a few minutes');
      }
  }

  public function delete($getID='')
  {
      checkPermission($this->permissionName.'-delete');
      $id = decode($getID);

      $tbl = $this->tbl; $primaryKey = $this->primaryKey;
      $get = get_field($tbl, "$primaryKey=$id");
      if (empty($get)) { ResponseError("Buku tamu not found"); }

      $foto = (!empty($get['foto_tanda_pengenal'])) ? json_decode($get['foto_tanda_pengenal']):[];
      if (!empty($get['foto_kendaraan'])) {
        $foto_kendaraan = json_decode($get['foto_kendaraan']);
        if (!empty($foto_kendaraan)) {
          $foto = array_merge($foto_kendaraan, $foto);
        }
      }

      $this->db->trans_begin();
      $delete = delete_data($tbl, "$primaryKey='$id'");
      if ($delete) {
        $this->db->trans_commit();
        if (!empty($foto)) {
          hapus_multi_foto($foto);
        }
        ResponseSuccess('Deleted successfully');
      }else{
        $this->db->trans_rollback();
        ResponseError("Failed, try again in a few minutes");
      }
  }

  public function deleteFoto($getID='')
  {
      checkPermission($this->permissionName.'-delete');
      $id = decode($getID);

      $tbl = $this->tbl; $primaryKey = $this->primaryKey;
      $get = get_field($tbl, "$primaryKey=$id");
      if (empty($get)) { ResponseError("Buku tamu not found"); }

      $foto_delete = @$_GET['img'];
      $tipe_kendaraan = (@$_GET['tipe']=='kendaraan') ? true:false;
      $foto = $tipe_kendaraan ? $get['foto_kendaraan']:$get['foto_tanda_pengenal'];
      if (empty($foto)) {
        ResponseSuccess('Deleted successfully');
      }
      $foto_new=[];
      $foto = json_decode($foto);
      if (!empty($foto)) {
        foreach ($foto as $key => $value) {
          if ($value != $foto_delete) {
            $foto_new[] = $value;
          }
        }
      }
      $foto = (!empty($foto_new)) ? json_encode($foto_new):Null;

      $field_foto = $tipe_kendaraan ? 'foto_kendaraan':'foto_tanda_pengenal';

      $this->db->trans_begin();
      $delete = update_data($tbl, ["$field_foto"=>$foto], ["$primaryKey"=>$id]);
      if ($delete) {
        $this->db->trans_commit();
        if (!empty($foto_delete)) {
          delete_file($foto_delete);
        }
        ResponseSuccess('Deleted successfully');
      }else{
        $this->db->trans_rollback();
        ResponseError("Failed, try again in a few minutes");
      }
  }

}
