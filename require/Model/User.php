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
        $_SESSION[User::SESSION] = null;
    }

    public static function listAll()
    {

        $conn = new Sql();

        $sql = "
        SELECT *
        FROM usuarios a
        INNER JOIN pessoas b ON a.ps_codigo = b.ps_codigo
        ORDER BY ps_nome
        ";

        return $conn->select($sql);

    }

    public static function save($ps_nome, $user_senha, $ps_contato, $ps_email, $user_login, $user_admin = 0)
    {

        $conn = new Sql();

        $ps_contato = trim($ps_contato) != "" ? preg_replace("/[^0-9]/", "", $ps_contato) : NULL;
        $ps_email   = trim($ps_email)   != "" ? $ps_email : NULL;

        $hash_options = [
            'cost' => 12
        ];

        $user_senha = password_hash($user_senha, PASSWORD_DEFAULT, $hash_options);

        $sql = "
            INSERT INTO pessoas
            (ps_nome, ps_email, ps_contato)
            VALUES 
            (:ps_nome, :ps_email, :ps_contato);

            INSERT INTO usuarios
            (ps_codigo, user_login, user_senha, user_admin)
            VALUES 
            ((SELECT LAST_INSERT_ID()), :user_login, :user_senha, :user_admin);
        ";

        $dados = array(
            ':ps_nome' => $ps_nome,
            ':ps_email' => $ps_email,
            ':ps_contato' => $ps_contato,
            ':user_login' => $user_login,
            ':user_senha' => $user_senha,
            ':user_admin' => $user_admin
        );

        $conn->query($sql, $dados);

    }

    public static function get($user_codigo)
    {

        $conn = new Sql();

        $sql = "
            SELECT * 
            FROM usuarios a
            INNER JOIN pessoas b ON a.ps_codigo = b.ps_codigo
            WHERE user_codigo = :user_codigo
        ";

        $result = $conn->select($sql, array(
           ":user_codigo" => $user_codigo
        ));

        $result[0]['ps_contato'] = phoneMask($result[0]['ps_contato']);

        return $result[0];

    }

    public static function update($user_codigo, $ps_nome, $ps_contato, $ps_email, $user_login, $user_admin)
    {

        $conn = new Sql();

        $ps_contato = trim($ps_contato) != "" ? preg_replace("/[^0-9]/", "", $ps_contato) : NULL;
        $ps_email   = trim($ps_email)   != "" ? $ps_email : NULL;

        $sql = "
            SELECT @ps_codigo := ps_codigo 
            FROM usuarios 
            WHERE user_codigo = :user_codigo;

            UPDATE pessoas
            SET ps_nome = :ps_nome,
                ps_contato = :ps_contato,
                ps_email = :ps_email
            WHERE ps_codigo = @ps_codigo;

            UPDATE usuarios
            SET user_login = :user_login,
                user_admin = :user_admin
            WHERE user_codigo = :user_codigo;
        ";

        $dados = array(
            ':ps_nome' => $ps_nome,
            ':ps_email' => $ps_email,
            ':ps_contato' => $ps_contato,
            ':user_login' => $user_login,
            ':user_admin' => $user_admin,
            ':user_codigo' => $user_codigo
        );

        $conn->query($sql, $dados);

    }

    public static function delete($user_codigo)
    {

        $conn = new Sql();

        $sql = "
            SELECT ps_codigo
            FROM usuarios 
            WHERE user_codigo = :user_codigo
        ";

        $ret = $conn->select($sql, [":user_codigo" => $user_codigo]);

        $ret = $ret[0];

        $ps_codigo = $ret['ps_codigo'];

        $sql = "
            DELETE FROM usuarios
            WHERE user_codigo = :user_codigo;

            DELETE FROM pessoas
            WHERE ps_codigo = :ps_codigo;
        ";

        $conn->query($sql, array(
            ':user_codigo' => $user_codigo,
            ':ps_codigo' => $ps_codigo
        ));

    }

    public static function getForgot($email)
    {

    }

}