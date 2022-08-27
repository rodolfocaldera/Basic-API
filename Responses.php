<?php
    class Responses{
        public static function successfullyMessage($message,$token=null){
            $response['status_code_header'] = 'HTTP/1.1 200';
            $body = ['message' => $message];
            if(!is_null($token)){
                $body["token"]=$token;
            }
            $response['body'] = json_encode($body);
            return $response;
        }

        public static function unprocessableEntityResponse(){
            $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
            $response['body'] = json_encode([
                'error' => 'Invalid data'
            ]);
            return $response;
        }

        public static function notFoundDataResponse(){
            $response['status_code_header'] = 'HTTP/1.1 404 Not found data';
            $response['body'] = json_encode([
                'error' => 'Not found data'
            ]);
            return $response;
        }

        public static function badProcessResponse($error_message='the request has not been processed, please try later'){
            $response['status_code_header'] = 'HTTP/1.1 400';
            $response['body'] = json_encode([
                'error' => $error_message
            ]);
            return $response;
        }

        public static function notFoundResponse($error_message='not found route'){
            $response['status_code_header'] = 'HTTP/1.1 404';
            $response['body'] = json_encode([
                'error' => $error_message
            ]);
            return $response;
        }
    }