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
            }else if($method == "PUT"){
                $response = $this->update_post();
            }else if($method == "DELETE"){
                $response = $this->delete_post();
            }else if($method == "GET"){
                $response = $this->get_posts();
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
                            $createAt = date("Y-m-d h:i:s");
                            $affectedRows = $this->functions->insert_post($validate_token_info["user_id"],$title,$description,$createAt);
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

        private function update_post(){
            $response = null;
            if(isset($_GET["id"]) && is_numeric($_GET["id"])){
                $data = [];
                parse_str(file_get_contents("php://input"),$data);
                if(isset($data["token"])){
                    $validate_token_info = $this->validateToken($data["token"]);
                    if($validate_token_info["valid"]){
                        $rol = intval($validate_token_info["rol"]);
                        if($rol>3){
                            $title = null;
                            $description = null;

                            if(isset($data["title"])){
                                $title = $data["title"];
                            }

                            if(isset($data["description"])){
                                $description = $data["description"];
                            }

                            $affectedRows = $this->functions->update_post($_GET["id"],$title,$description);
                            if($affectedRows>0){
                                $response = Responses::successfullyMessage("The post was updated successfully");
                            }else{
                                $response = Responses::badProcessResponse();
                            }

                        }else{
                            $response = Responses::badProcessResponse("You don't have the permissions necessary for this request");
                        }
                    }else{
                        $response = Responses::badProcessResponse("Token has been expired"); 
                    }
                }else{
                    $response = Responses::badProcessResponse("Not found token"); 
                }
            }else{
                $response = Responses::notFoundDataResponse(); 
            }

            return $response;
        }

        private function delete_post(){
            $response = null;
            if(isset($_GET["id"]) && is_numeric($_GET["id"])){
                $data = [];
                parse_str(file_get_contents("php://input"),$data);
                if(isset($data["token"])){
                    $validate_token_info = $this->validateToken($data["token"]);
                    if($validate_token_info["valid"]){
                        $rol = intval($validate_token_info["rol"]);
                        if($rol==5){
                            $affectedRows = $this->functions->delete_post($_GET["id"]);
                            if($affectedRows>0){
                                $response = Responses::successfullyMessage("The post was deleted successfully");
                            }else{
                                $response = Responses::badProcessResponse();
                            }
                        }else{
                            $response = Responses::badProcessResponse("You don't have the permissions necessary for this request");
                        }
                    }else{
                        $response = Responses::badProcessResponse("Token has been expired"); 
                    }
                }else{
                    $response = Responses::badProcessResponse("Not found token"); 
                }
            }else{
                $response = Responses::notFoundDataResponse(); 
            }
            return $response;
        }

        private function get_posts(){
            $data = [];
            $response = null;
            parse_str(file_get_contents("php://input"),$data);
            if(isset($data["token"])){
                $validate_token_info = $this->validateToken($data["token"]);
                if($validate_token_info["valid"]){
                    $rol = intval($validate_token_info["rol"]);
                    if($rol>1){
                        $posts = $this->functions->get_posts();
                        for ($i = 0; $i<count($posts);$i++) {
                            $rol="";
                            switch (intval($posts[$i]["rol"])) {
                                case 1:
                                    $rol="basic";
                                    break;
                                case 2:
                                    $rol="medium";
                                    break;
                                case 3:
                                    $rol="high medium";
                                    break;
                                case 4:
                                    $rol="medium high";
                                    break;
                                case 5:
                                    $rol="high";
                                    break;
                            }
                            $posts[$i]["rol_name"]=$rol;
                        }
                        $response['status_code_header'] = 'HTTP/1.1 200';
                        $response['body'] = json_encode([
                            'posts' => $posts
                        ]);
                    }else{
                        $response = Responses::badProcessResponse("You don't have the permissions necessary for this request");
                    }
                }else{
                    $response = Responses::badProcessResponse("Token has been expired"); 
                }
            }else{
                $response = Responses::badProcessResponse("Not found token"); 
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