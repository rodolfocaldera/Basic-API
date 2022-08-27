<?php 
    require_once "vendor/autoload.php";
    use Firebase\JWT\JWT;
    class Login{
        private $connection;
        public function __construct($connection){
            $this->connection = $connection;
        }

        public function login(){
            $functions = new Functions($this->connection);
            $response = null;
            $jwt = null;
            if(!empty($_POST)){
                $email = $_POST["email"];
                $password = $_POST["password"];
                if(ctype_alnum($password) && filter_var($email,FILTER_VALIDATE_EMAIL)){
                    $response = $functions->login($email,$password);
                    if(!is_null($password) && password_verify($password,$response["password"])){
                        $data = $this->jwt($response["id"],$email);
                        $affectedRows = $functions->save_session($email,$data["token"],$data["jwt"]);
                        if($affectedRows>0){
                            $response = Responses::successfullyMessage("Started session",$data["jwt"]);
                        }else{
                            $response = Responses::badProcessResponse();
                        }
                    }else{
                        $response = ErrorsResponse::notFoundResponse("Invalid email and password");
                    }
                }else{
                    $response = ErrorsResponse::unprocessableEntityResponse();
                }
            }else{
                $response = ErrorsResponse::unprocessableEntityResponse();
            }
           return $response;
        }

        private function jwt($id,$email){
            $time = time();

            $token = [
                "iat"=>$time,
                "exp"=>$time+(60*60*24),
                "data"=>[
                    "id"=>$id,
                    "email"=>$email
                ]
            ];
            $jwt = JWT::encode($token,"dffewgir465454erfer1","HS512");
            return ["token"=>$token,"jwt"=>$jwt];
        }
    }
?>