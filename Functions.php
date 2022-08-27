<?php
    class Functions{
        private $connection;
        public function __construct($connection){
            $this->connection = $connection;
        }

        public function create_user($name,$last_name,$password,$email,$rol){
            $stmt = $this->connection->prepare("INSERT INTO users (name,last_name,password,email,rol) values (?,?,?,?,?)");
            $stmt->execute([$name, $last_name, $password,$email,$rol]);
            $affectedRows = $stmt->rowCount();
            return $affectedRows;
        }

        public function existsEmail($email){
            $response = $this->find("email","users","email","'".$email."'");
            if(count($response)>0){
                return true;
            }else{
                return false;
            }
        }

        public function find($fields,$table,$key,$key_value){
            $sql = "
                SELECT 
                   $fields
                FROM
                    $table
                WHERE $key = $key_value;
            ";
            
            try {
                $stmt = $this->connection->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                return $result;
            } catch (\PDOException $e) {
                exit($e->getMessage());
            }    
        }

        public function login($email,$password){
            $sql = "select id,password from users where email = '$email'";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $password = null;
            $id=null;
            if(count($result)>0){
                $id = $result[0]["password"];
                $password = $result[0]["password"];
            }
            return ["id"=>$id,"password"=>$password];
        }

        public function save_session($email,$token,$jwt){
            $stmt = $this->connection->prepare("INSERT INTO sessions (email,token,inicialice,expirate) values (?,?,?,?)");
            $stmt->execute([$email,$jwt,$token["iat"],$token["exp"]]);
            $affectedRows = $stmt->rowCount();
            return $affectedRows;
        }

        public function get_token_data($token){
            $stmt = $this->connection->prepare("select expirate,users.id,rol from sessions join users on users.email = sessions.email where token = '$token'");
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        }

        public function insert_post($user_id,$title,$description){
            $stmt = $this->connection->prepare("INSERT INTO posts (title,description,user_id) values (?,?,?)");
            $stmt->execute([$title,$description,$user_id]);
            $affectedRows = $stmt->rowCount();
            return $affectedRows;
        }
    }
?>