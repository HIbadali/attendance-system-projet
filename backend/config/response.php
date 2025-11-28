<?php

class Response {

    public static function success($data = null, $message = "Succès") {
        echo json_encode([
            "success" => true,
            "message" => $message,
            "data" => $data
        ]);
        exit;
    }

    public static function error($message = "Erreur", $code = 400) {
        http_response_code($code);
        echo json_encode([
            "success" => false,
            "message" => $message
        ]);
        exit;
    }
}
