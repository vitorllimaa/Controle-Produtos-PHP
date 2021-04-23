<?php
namespace controle\model;

use controle\DB\Sql;
use Exception;
use controle\Model;

class User extends Model {
    const SESSION = "User";

    public static function login($login, $password){
    
        $sql = new Sql();
        $result = $sql->select("SELECT * FROM tb_login WHERE login = :LOGIN", array(
            //segunça para login no html
            ":LOGIN"=>$login
        ));
        //existe login no banco
        if(count($result) === 0)
        {
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
            var_dump($password.$data["senha"]);
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
        }else{

        }

    }
    public static function logout(){
        $_SESSION[User::SESSION] = NULL;
    }
}

?>