<?php

namespace Model;

use \DB\Sql;
use Model;

class Categoria extends Model
{

    public static function listAll()
    {

        $conn = new Sql();

        $sql = "
            SELECT * 
            FROM categorias
        ";

        $results = $conn->select($sql);

        return $results;

    }

    public static function save($cat_descricao)
    {

        $conn = new Sql();

        $sql = "
            INSERT INTO categorias
            (cat_descricao)
            VALUES 
            (:cat_descricao)
        ";

        $conn->query($sql, array(
            ":cat_descricao" => $cat_descricao
        ));

    }

    public static function delete($cat_codigo)
    {

        $conn = new Sql();

        $sql = "
            DELETE FROM produto_categoria
            WHERE cat_codigo = :cat_codigo;

            DELETE FROM categorias
            WHERE cat_codigo = :cat_codigo;
        ";

        $conn->query($sql, array(
            ":cat_codigo" => $cat_codigo
        ));

    }

    public static function get($cat_codigo)
    {

        $conn = new Sql();

        $sql = "
            SELECT * 
            FROM categorias
            WHERE cat_codigo = :cat_codigo
        ";

        $result = $conn->select($sql, array(
            ":cat_codigo" => $cat_codigo
        ));

        return $result[0];

    }

    public static function update($cat_codigo, $cat_descricao)
    {

        $conn = new Sql();

        $sql = "
            UPDATE categorias
            SET cat_descricao = :cat_descricao
            WHERE cat_codigo = :cat_codigo
        ";

        $conn->query($sql, array(
            ":cat_codigo" => $cat_codigo,
            ":cat_descricao" => $cat_descricao
        ));

    }

}