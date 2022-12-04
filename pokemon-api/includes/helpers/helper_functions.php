<?php

function getErrorUnsupportedFormat() {
    $error_data = array(
        "error:" => "unsuportedResponseFormat",
        "message:" => "The requested resouce representation is available only in JSON."
    );
    return $error_data;
}

function getErrorUnsupportedMethod($method_name) {
    $error_data = array(
        "error:" => "unsupportedMethod",
        "message:" => $method_name . " method is unsupported on this resource."
    );
    return $error_data;
}

function getErrorBadRequest($message) {
    $error_data = array(
        "error:" => "badRequest",
        "message:" => $message
    );
    return $error_data;
}

function makeCustomJSONError($error_code, $error_message) {
    $error_data = array(
        "error:" => $error_code,
        "message:" => $error_message
    );    
    return json_encode($error_data);
}



