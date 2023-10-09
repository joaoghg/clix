<?php

namespace Model;

use \DB\Sql;
use Model;

class User extends Model
{
    const SESSION = "user";
    const SECRET = "HcodePhp7_Secret";
    const SECRET_IV = "HcodePhp7_Secret_IV";

    /**
     * @throws \Exception
     */
    public static function login($login, $password){

        $conn = new Sql();

        $sql = "
            SELECT *
            FROM tb_users
            WHERE deslogin = :login
        ";

        $results = $conn->select($sql, [':login' => $login]);

        if(count($results) === 0){
            throw new \Exception("Credenciais inválidas!");
        }

        $data = $results[0];

        if(password_verify($password, $data['despassword'])){
            $user = new User();

            $user->setData($data);

            $_SESSION[User::SESSION] = $user->getData();

            return $user;
        }else{
            throw new \Exception("Credenciais inválidas!");
        }
    }

    public static function verifyLogin($inadmin = true){

        if( !isset($_SESSION[User::SESSION])
            || !$_SESSION[User::SESSION]
            || !(int)$_SESSION[User::SESSION]['iduser'] > 0
            || (bool)$_SESSION[User::SESSION]['inadmin'] !== $inadmin)
        {
            header("Location: /admin/login");
            exit;
        }

    }

    public static function logout(){
        $_SESSION[User::SESSION] = null;
    }

    public static function listAll()
    {
        $conn = new Sql();

        $sql = "
            SELECT *
            FROM tb_users a
            INNER JOIN tb_persons b ON a.idperson = b.idperson
            ORDER BY desperson
        ";

        return $conn->select($sql);

    }

    /*
     * pdesperson VARCHAR(64),
     * pdeslogin VARCHAR(64),
     * pdespassword VARCHAR(256),
     * pdesemail VARCHAR(128),
     * pnrphone BIGINT,
     * pinadmin TINYINT
     */
    public function save()
    {
        $conn = new Sql();

        $sql = "
            CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)
        ";

        $results = $conn->select($sql, array(
            ":desperson" => $this->getdesperson(),
            ":deslogin" => $this->getdeslogin(),
            ":despassword" => $this->getdespassword(),
            ":desemail" => $this->getdesemail(),
            ":nrphone" => $this->getnrphone(),
            ":inadmin" => $this->getinadmin()
        ));

        $this->setData($results[0]);

    }

    public function get($iduser)
    {
        $conn = new Sql();

        $sql = "
            SELECT *
            FROM tb_users a
            INNER JOIN tb_persons b ON a.idperson = b.idperson
            WHERE a.iduser = :iduser
        ";

        $results = $conn->select($sql, array(
            ":iduser" => $iduser
        ));

        $this->setData($results[0]);
    }

    public function update(){
        $conn = new Sql();

        $sql = "
            CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)
        ";

        $results = $conn->select($sql, array(
            ":iduser" => $this->getiduser(),
            ":desperson" => $this->getdesperson(),
            ":deslogin" => $this->getdeslogin(),
            ":despassword" => $this->getdespassword(),
            ":desemail" => $this->getdesemail(),
            ":nrphone" => $this->getnrphone(),
            ":inadmin" => $this->getinadmin()
        ));

        $this->setData($results[0]);
    }

    public function delete(){
        $conn = new Sql();

        $sql = "CALL sp_users_delete(:iduser)";

        $conn->query($sql, array(
            ":iduser" => $this->getiduser()
        ));
    }

    public static function getForgot($email)
    {

        $conn = new Sql();

        $sql = "
            SELECT *
            FROM tb_persons a
            INNER JOIN tb_users b ON a.idperson = b.idperson
            WHERE a.desemail = :email
        ";

        $results = $conn->select($sql, array(
           ":email" => $email
        ));

        if (count($results) === 0) {
            throw new \Exception("Não foi possível recuperar a senha.");
        }

        $data = $results[0];

        $response = $conn->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
            ":iduser" => $data['iduser'],
            ":desip" => $_SERVER['REMOTE_ADDR']
        ));

        if(count($response) === 0){
            throw new \Exception("Não foi possível recuperar a senha");
        }

        $dataRec = $response[0];

        base64_encode(openssl_encrypt($dataRec['idrecovery'], 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV)));

    }

}