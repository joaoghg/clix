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
            SELECT car_codigo
            FROM carrinhos 
            WHERE user_codigo = :user_codigo
        ";

        $response =  $conn->select($sql, array(':user_codigo' => $_SESSION['user']['user_codigo']));
        $car_codigo = $response[0]['car_codigo'];

        if(trim($car_codigo) == ""){

            $sql = "
                select MAX(car_codigo) as car_codigo
                FROM carrinhos
            ";

            $response =  $conn->select($sql);
            $car_codigo = $response[0]['car_codigo'];
            if(empty($car_codigo)){
                $car_codigo = 1;
            }
            else{
                $car_codigo++;
            }

            $sql = "
                INSERT INTO carrinhos
                (car_codigo, car_sessao_id, user_codigo, end_codigo, car_frete)
                VALUES 
                (:car_codigo, 1, :user_codigo, NULL, NULL)
            ";

            $conn->query($sql, array(':user_codigo' => $_SESSION['user']['user_codigo'], ':car_codigo' => $car_codigo));

            $sql = "
                INSERT INTO produtos_carrinho
                (car_codigo, prd_codigo)
                VALUES 
                (:car_codigo, :prd_codigo)
            ";

            $conn->query($sql, array(':prd_codigo' => $prd_codigo, ':car_codigo' => $car_codigo));

        }
        else{

            $sql = "
                INSERT INTO produtos_carrinho
                (car_codigo, prd_codigo)
                VALUES 
                (:car_codigo, :prd_codigo)
            ";

            $conn->query($sql, array(':car_codigo' => $car_codigo, ':prd_codigo' => $prd_codigo));

        }

    }

}