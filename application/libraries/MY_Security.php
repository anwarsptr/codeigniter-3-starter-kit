<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// application/libraries/MY_Security.php
class MY_Security {

    /**
     * Fungsi untuk melakukan verifikasi CSRF
     */
    public function csrf_verify() {
        // log_message('debug', 'csrf_verify method called');

        $CI = &get_instance();
        // Mengambil CSRF token dari header 'X-CSRF-TOKEN'
        $csrf_token = $CI->input->get_request_header('X-CSRF-TOKEN');

        if ($csrf_token) {
            // Lakukan dekripsi atau validasi CSRF sesuai keinginan Anda
            $decrypted = decode($csrf_token);  // Fungsi custom decode()
            // log_message('debug', 'CSRF Token Decrypted: ' . $decrypted);

            // Token yang sudah didekripsi adalah array dengan nama dan hash
            $data = json_decode($decrypted, true);
            if (is_array($data) && count($data) == 2) {
                $token_name = $data[0];
                $token_hash = $data[1];

                // Verifikasi apakah token hash sesuai dengan hash yang tersimpan
                if ($token_hash != $CI->security->get_csrf_hash()) {
                    // log_message('error', 'Invalid CSRF Token!');
                    if (isAjax()) {
                      ResponseError('Invalid CSRF Token');
                    }

                    show_error('Invalid CSRF Token');
                }
            } else {
                // log_message('error', 'CSRF Token format invalid');
                if (isAjax()) {
                  ResponseError('Invalid CSRF Token');
                }
                show_error('Invalid CSRF Token');
            }
        } else {
            // log_message('error', 'CSRF Token missing');
            if (isAjax()) {
              ResponseError('CSRF Token is missing');
            }
            show_error('CSRF Token is missing');
        }

        return TRUE; // Jika CSRF token valid
    }

}
