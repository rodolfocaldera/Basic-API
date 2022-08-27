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
            if(strtoupper($this->request[2])=="LOG"){
                $login = new Login($this->connection);
                $response = $login->login();
            }else if(strtoupper($this->request[2])=="USER"){
                $response = $this->createUser();
            }else if((strtoupper($this->request[2])=="POST")){
                $response = $this->processPost();
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
                $token = $_POST["token"];

                $validate_token_info = $this->validateToken($token);
                if($validate_token_info["valid"]){
                    $rol = intval($validate_token_info["rol"]);
                    if($rol>2){
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
                        $response = Responses::badProcessResponse("You don't have the permissions necessary for this request");
                    }
                }else{
                    $response = Responses::badProcessResponse("Token has been expired"); 
                }
            }else{
                $response = Responses::notFoundDataResponse();
            }
            
            return $response;
        }

        private function processPost(){
            $method = $_SERVER['REQUEST_METHOD'];
            $response = null;
            if($method=="POST"){
                $response = $this->create_post();
            }
            return $response;
        }

        private function create_post(){
            $response = null;
            if(!empty($_POST)){
                $token = $_POST["token"];
                $title = $_POST["title"];
                $description = $_POST["description"];

                $validate_token_info = $this->validateToken($token);
                if($validate_token_info["valid"]){
                    $rol = intval($validate_token_info["rol"]);
                    if($rol>2){
                        if(ctype_alnum($title) && ctype_alnum($description)){
                            $affectedRows = $this->functions->insert_post($validate_token_info["user_id"],$title,$description);
                            if($affectedRows>0){
                                $response = Responses::successfullyMessage("The post was inserted successfully");
                            }else{
                                $response = Responses::badProcessResponse();
                            }
                        }else{
                            $response = Responses::badProcessResponse("Invalid data sended, only alphanumeric characters accepted");
                        }
                    }else{
                        $response = Responses::badProcessResponse("You don't have the permissions necessary for this request");
                    }
                }else{
                    $response = Responses::badProcessResponse("Token has been expired"); 
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

        private function validateToken($token){
            $token_info = $this->functions->get_token_data($token);
            $actual_time = time();
            $response = ["valid"=>false];
            if(count($token_info)>0){
                $exp_token= intval($token_info[0]["expirate"]);
                if($exp_token>$actual_time){
                    $response=["valid"=>true,"user_id"=>$token_info[0]["id"],"rol"=>$token_info[0]["rol"]];
                }
            }
            return $response;
        }
    }
?>