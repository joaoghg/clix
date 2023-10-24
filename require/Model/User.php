<?php

namespace Model;

use \DB\Sql;
use Model;

class User extends Model
{
    const SESSION = "user";

    public static function login($login, $password)
    {

        $conn = new Sql();

        $sql = "
            SELECT *
            FROM usuarios
            WHERE user_login = :login
        ";

        $results = $conn->select($sql, [':login' => $login]);

        if(count($results) === 0){
            throw new \Exception("Credenciais inválidas!");
        }

        $data = $results[0];

        if(password_verify($password, $data['user_senha'])){
            $user = new User();

            $user->setData($data);

            $_SESSION[User::SESSION] = $user->getData();

            return $user;
        }else{
            throw new \Exception("Credenciais inválidas!");
        }

    }

    public static function verifyLogin($user_admin = true)
    {

        if( !isset($_SESSION[User::SESSION])
            || !$_SESSION[User::SESSION]
            || !(int)$_SESSION[User::SESSION]['user_codigo'] > 0
            || (bool)$_SESSION[User::SESSION]['user_admin'] !== $user_admin)
        {
            header("Location: /admin/login");
            exit;
        }

    }

    public static function logout()
    {

    }

    public static function listAll()
    {

    }

    public function save()
    {

    }

    public function get($user_codigo)
    {

    }

    public function update(){

    }

    public function delete(){

    }

    public static function getForgot($email)
    {

    }

}