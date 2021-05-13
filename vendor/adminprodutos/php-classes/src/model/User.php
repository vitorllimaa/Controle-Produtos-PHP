<?php
namespace controle\model;

use controle\DB\Sql;
use controle\Mailer;
use Exception;
use controle\Model;

class User extends Model {
    const SESSION = "User";
    const SECRET = "Maiscommerce2021";
    const SECRET_IV = "Maiscommerce2021_IV";
    public static function login($login, $password){
    
        $sql = new Sql();
        $result = $sql->select("SELECT * FROM tb_login WHERE login = :LOGIN", array(
            //segunça para login no html
            ":LOGIN"=>$login
        ));
        //existe login no banco
        if(count($result) === 0)
        { var_export(json_encode($result));
            throw new \Exception("Usuário ou senha não encontrado.");
        }
        //trazer o primeiro resultado do banco
        $data = $result[0]; 
        $Data = password_hash($data["senha"], PASSWORD_DEFAULT);

        if(password_verify($password, $Data) === true){
            $user = new User();
            $user->setData($data);

            $_SESSION[User::SESSION] =  $user->getValues();
            
            return $user;

        }else{
            
            throw new \Exception("Usuário ou senha não encontrado."); 
        }
    }

    public static function verifyLogin($adm = true){

        if(
            !isset($_SESSION[User::SESSION])
            ||
            !$_SESSION[User::SESSION]
            ||
            (bool)$_SESSION[User::SESSION]["adm"] !== $adm
        ) {
            header("Location: /admin/login");
            exit;
        }

    }
    public static function logout(){
        $_SESSION[User::SESSION] = NULL;
    }

    public static function listAll(){

        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_login");

    }

    public function save(){

        $sql = new Sql();

        $result = $sql->select("INSERT INTO tb_login VALUES(NULL, :nome, :login, :telefone, :email, :password, default, '1', NULL, NULL, NULL)", array(
            ":nome"=>$this->getname(),
            ":login"=>$this->getlogin(),
            ":telefone"=>$this->gettelefone(),
            ":email"=>$this->getemail(),
            ":password"=>$this->getpassword()
        ));
       
    }

    public function get($id_login){

        $sql = new Sql();

        $result = $sql->select("SELECT * FROM tb_login WHERE id_login = :login", array(
            "login"=>$id_login
        ));
        $this->setData($result[0]);
    }
    public function update($id_login){

        $sql = new Sql();

        $sql->select("UPDATE tb_login SET nome = :nome, login = :login, telefone = :telefone, email = :email WHERE id_login = $id_login",array(
            ":nome"=>$this->getname(),
            ":login"=>$this->getlogin(),
            ":telefone"=>$this->gettelefone(),
            ":email"=>$this->getemail()
        ));

    }

    public function delete($id_login){
        $sql = new Sql();

        $sql->select("DELETE FROM tb_login WHERE id_login = $id_login");
        

    }
    public static function getforgot($email){
        $sql = new Sql();

        $result = $sql->select("SELECT * FROM tb_login WHERE email = :email", array(
            ":email"=>$email
        ));

        if(count($result) === 0 ){
            throw new \Exception("Email não encontrado!"); 
        }else{
            $data = $result[0];

            $result2 = $sql->select("UPDATE tb_login SET IP = :IP, dtregister = NOW() WHERE id_login = :id_login", array(
                ":IP"=>$_SERVER["REMOTE_ADDR"],
                ":id_login"=>$data["id_login"]
            ));

            $result3 = $sql->select("SELECT * FROM tb_login WHERE id_login = :login", array(
                "login"=>$data["id_login"]
            ));

            if(count($result3) === 0){
                throw new \Exception("Email não encontrado!"); 
            }else{

                $dataRecovery = $result3[0];
                
				$code = openssl_encrypt($dataRecovery['id_login'], 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

				$code = base64_encode($code);

                $link = "http://www.adminprodutos.com.br/admin/forgot/reset?code=$code";

                $mailer = new Mailer($data["email"], $data["nome"], "Redefinir senha!", "forgot",
                array(
                    "name"=>$data["nome"],
                    "link"=>$link
                ));

                $mailer->send();
                
                return $data;
            }
        }

    }

     public static function validForgotDecrypt($result){

        $code = base64_decode($result);

        $idrecovery = openssl_decrypt($code, 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

        $sql = new Sql();
        $result1 = $sql->select("SELECT * FROM bdcontroleprodutos.tb_login WHERE id_login = :idlogin AND dtrecovery IS NULL AND DATE_ADD(dtregister, INTERVAL 1 HOUR) >= NOW()",array(
            ":idlogin"=>$idrecovery
        ));

        if(count($result1) === 0 ){
            throw new \Exception("Não foi possível recuperar a senha!");
        }else{
            return $result1[0];
        }

    }
    
    public static function setForgotUser($id, $password){
        
        $sql = new Sql();
        $result1 = $sql->select("UPDATE tb_login SET senha = :password WHERE id_login = :id_login", array(
            ":password"=>$password,
            ":id_login"=>$id
        ));


    }

    public function nameUser(){
        $sql = new Sql();
        $result1 = $sql->select("SELECT * FROM tb_login WHERE senha = :password", array(
            ":password"=>$this->getpassword()
        ));

        return $result1[0];

    }
}

?>