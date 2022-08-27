<?php
    require "DatabaseConnector.php";
    require "Functions.php";

    class Controller{
        private $connection;
        private $functions;
        private $request;
        private $userId;

        private $personGateway;

        public function __construct(){
            $dbConector = new DatabaseConnector();
            $this->connection = $dbConector->getConnection();
            $this->functions = new Functions($this->connection);
        }

        function setRequest($request){
            $this->request = $request;
        }

        function processRequest(){
            $response = null;
            if(strtoupper($this->request[2])=="USER"){
                $response = $this->createUser();
            }
            
            header($response['status_code_header']);
            if ($response['body']) {
                echo $response['body'];
            }
        }

        private function createUser(){
            if(!empty($_POST)){
                $name = $_POST["name"];
                $last_name = $_POST["last_name"];
                $email = $_POST["email"];
                $password = $_POST["password"];
                $role = $_POST["role"];
                $validUserData = $this->validateUserData($name,$last_name,$email,$password,$role);
                if($validUserData){
                    $existsEmail = $this->functions->existsEmail($email);
                    if(!$existsEmail){
                        $password = password_hash($password,PASSWORD_DEFAULT);
                        $affectedRows = $this->functions->create_user($name,$last_name,$password,$email,$role);
                        if($affectedRows > 0){
                            $response = $this->userCreatedResponse();
                        }else{
                            $response = $this->badProcessResponse();
                        }
                    }else{
                        $response = $this->badProcessResponse("the email already register, please enter another");
                    }
                }else{
                    $response = $this->notFoundDataResponse();
                }

            }else{
                $response = $this->notFoundDataResponse();
            }
            
            return $response;
        }

        private function validateUserData($name,$last_name,$email,$password,$role){
            if(ctype_alpha($name) && ctype_alpha($last_name) && ctype_alnum($password) && filter_var($email) && $role >=1 && $role <=5){
                return true;
            }else{
                return false;
            }
        }

        private function userCreatedResponse(){
            $response['status_code_header'] = 'HTTP/1.1 200 user created';
            $response['body'] = json_encode([
                'message' => 'User created successfully'
            ]);
            return $response;
        }

        private function unprocessableEntityResponse(){
            $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
            $response['body'] = json_encode([
                'error' => 'Invalid data'
            ]);
            return $response;
        }

        private function notFoundDataResponse(){
            $response['status_code_header'] = 'HTTP/1.1 404 Not found data';
            $response['body'] = json_encode([
                'error' => 'Not found data'
            ]);
            return $response;
        }

        private function badProcessResponse($error_message='the request has not been processed, please try later'){
            $response['status_code_header'] = 'HTTP/1.1 400';
            $response['body'] = json_encode([
                'error' => $error_message
            ]);
            return $response;
        }
    }
?>