<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CSRFHook {

    public function verify_csrf()
    {
        $CI = &get_instance();
        if ($CI->input->method(TRUE) !== 'GET')
        {
            $CI->my_security->csrf_verify();
        }
    }
    
}
