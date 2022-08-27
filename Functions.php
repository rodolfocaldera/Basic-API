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
    }
?>