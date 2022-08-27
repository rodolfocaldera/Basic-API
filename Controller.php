<?php
    require "DatabaseConnector.php";
    require "Functions.php";
    require_once "Login.php";
    require_once "Responses.php";
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
            }else if(strtoupper($this->request[2])=="LOG"){
                $login = new Login($this->connection);
                $response = $login->login();
            }else{
                $response = Responses::notFoundResponse();
                
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
                            $response = Responses::successfullyMessage("User created successfully");
                        }else{
                            $response = Responses::badProcessResponse();
                        }
                    }else{
                        $response = Responses::badProcessResponse("the email already register, please enter another");
                    }
                }else{
                    $response = Responses::notFoundDataResponse();
                }

            }else{
                $response = Responses::notFoundDataResponse();
            }
            
            return $response;
        }

        private function validateUserData($name,$last_name,$email,$password,$role){
            if(ctype_alpha($name) && ctype_alpha($last_name) && ctype_alnum($password) && filter_var($email, FILTER_VALIDATE_EMAIL) && $role >=1 && $role <=5){
                return true;
            }else{
                return false;
            }
        }
    }
?>