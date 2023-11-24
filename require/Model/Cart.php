<?php

namespace Model;

use \DB\Sql;
use Model;

class Cart extends Model
{

    public static function add($prd_codigo)
    {

        $conn = new Sql();

        $sql = "
            INSERT INTO carrinhos
            (car_sessao_id, user_codigo, end_codigo, car_frete)
            VALUES 
            (1, :user_codigo, NULL, NULL)
        ";

        $conn->query($sql, array(':user_codigo' => $_SESSION['user']['user_codigo']));

        $sql = "
            INSERT INTO produtos_carrinho
            (car_codigo, prd_codigo)
            VALUES 
            ((SELECT LAST_INSERT_ID()), :prd_codigo)
        ";

        $conn->query($sql, array(':prd_codigo' => $prd_codigo));

    }

}