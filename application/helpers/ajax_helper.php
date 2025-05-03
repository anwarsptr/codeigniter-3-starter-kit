<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function isAjax()
{
  $CI = &get_instance();
  return $CI->input->is_ajax_request();
}

function isJson($string) {
    // Mengecek apakah string dimulai dengan { atau [ dan diakhiri dengan } atau ]
    return preg_match('/^[\[\{].*[\]\}]$/s', trim($string)) === 1;
}

function ResponseJson( $message, $data=[], $statusCode = 200, $status=0)
{
    $CI = &get_instance();
    $response = [
        'status' => $status,
        'message' => $message,
    ];
    if (!empty($CI->input->get_request_header('X-CSRF-TOKEN'))) {
        $response = array_merge($response, ['token'=>setCSRF()]);
    }
    if ($data) {
        $response = array_merge($response, is_array($data) ? $data : ['data'=>$data]);
    }
  	header('Content-Type: application/json');
  	http_response_code($statusCode);
    echo json_encode($response); exit;
}

function ResponseSuccess( $message, $data=[], $statusCode = 200, $status=1)
{
    return ResponseJson( $message, $data, $statusCode, $status);
}

function ResponseError($message, $data=[], $statusCode = 200, $status=0)
{
    return ResponseJson( $message, $data, $statusCode, $status);
}

function json_datatables($value='')
{
    return model('Datatables_model', 'json_datatables', $value);
}
