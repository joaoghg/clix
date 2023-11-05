<?php

namespace Model;

use DB\Sql;
use Model;

class Products extends Model
{

    public static function listAll()
    {

        $conn = new Sql();

        $sql = "
            SELECT group_concat(( select cat_codigo from produto_categoria where prd_codigo = prd.prd_codigo )) categorias, prd.* 
            FROM produtos prd
        ";

        return $conn->select($sql);

    }

    public function save($conn, $prd_descricao, $prd_preco, $prd_peso, $prd_largura, $prd_altura, $prd_comprimento, $prd_obs, array $categorias, array $imagens)
    {

        $sql = "
            INSERT INTO produtos
            (prd_descricao, prd_preco, prd_peso, prd_largura, prd_altura, prd_comprimento, prd_obs)
            VALUES 
            (:prd_descricao, :prd_preco, :prd_peso, :prd_largura, :prd_altura, :prd_comprimento, :prd_obs);
        ";

        $dados = array(
            ":prd_descricao" => $prd_descricao,
            ":prd_preco" => $prd_preco,
            ":prd_peso" => $prd_peso,
            ":prd_largura" => $prd_largura,
            ":prd_altura" => $prd_altura,
            ":prd_comprimento" => $prd_comprimento,
            ":prd_obs" => $prd_obs
        );

        $conn->query($sql, $dados);

        $sql = "
            SELECT MAX(prd_codigo) prd_codigo FROM produtos
        ";
        $result = $conn->select($sql);

        $prd_codigo = $result[0]['prd_codigo'];

        self::inserirCategorias($categorias, $prd_codigo, $conn);

        for ($i = 0; $i < count($imagens['name']); $i++){

            if(!is_dir($_SERVER['DOCUMENT_ROOT']."/res/template/img/produtos")){
                mkdir($_SERVER['DOCUMENT_ROOT']."/res/template/img/produtos", 0777, true);
            }

            $parts = explode('.', $imagens['name'][$i]);

            $extension = end($parts);

            $img_path = "/res/template/img/produtos/img_{$prd_codigo}_$i.$extension";

            if(move_uploaded_file($imagens['tmp_name'][$i], $_SERVER['DOCUMENT_ROOT'].$img_path)){

                $sql = "
                    INSERT INTO produto_imagens
                    (prd_codigo, img_caminho)
                    VALUES 
                    (:prd_codigo, :img_caminho)
                ";

                $conn->query($sql, array(
                    ":prd_codigo" => $prd_codigo,
                    ":img_caminho" => $img_path
                ));

            }

        }



    }

    public static function get($prd_codigo)
    {

        $conn = new Sql();

        $sql = "
            SELECT *
            FROM produtos
            WHERE prd_codigo = :prd_codigo
        ";

        $result = $conn->select($sql, array(
            ":prd_codigo" => $prd_codigo
        ));

        return $result[0];

    }

    public static function getCategorias($prd_codigo)
    {

        $conn = new Sql();

        $sql = "
            SELECT cat.*, CASE WHEN pcat.prd_codigo IS NOT NULL THEN 1 ELSE 0 END checked
            FROM categorias cat
            LEFT JOIN produto_categoria pcat ON pcat.cat_codigo = cat.cat_codigo
            WHERE prd_codigo = :prd_codigo
        ";

        return $conn->select($sql, array(
            ":prd_codigo" => $prd_codigo
        ));


    }

    public static function getImages($prd_codigo)
    {

        $conn = new Sql();

        $sql = "
            SELECT *
            FROM produto_imagens
            WHERE prd_codigo = :prd_codigo
        ";

        return $conn->select($sql, array(
            ":prd_codigo" => $prd_codigo
        ));

    }

    public static function saveImage(array $imagens, $prd_codigo)
    {

        $conn = new Sql();

        for ($i = 0; $i < count($imagens['name']); $i++){

            if(!is_dir($_SERVER['DOCUMENT_ROOT']."/res/template/img/produtos")){
                mkdir($_SERVER['DOCUMENT_ROOT']."/res/template/img/produtos", 0777, true);
            }

            $parts = explode('.', $imagens['name'][$i]);

            $extension = end($parts);

            $img_path = "/res/template/img/produtos/img_{$prd_codigo}_$i.$extension";

            if(move_uploaded_file($imagens['tmp_name'][$i], $_SERVER['DOCUMENT_ROOT'].$img_path)){

                $sql = "
                    INSERT INTO produto_imagens
                    (prd_codigo, img_caminho)
                    VALUES 
                    (:prd_codigo, :img_caminho)
                ";

                $conn->query($sql, array(
                    ":prd_codigo" => $prd_codigo,
                    ":img_caminho" => $img_path
                ));

            }

        }

    }

    public static function deleteImage($img_codigo)
    {

        $conn = new Sql();

        $sql = "
            SELECT img_caminho
            FROM produto_imagens
            WHERE img_codigo = :img_codigo
        ";

        $result = $conn->select($sql, array("img_codigo" => $img_codigo));

        $img_caminho = $result[0]['img_caminho'];

        if( unlink($_SERVER['DOCUMENT_ROOT'].$img_caminho) ){

            $sql = "
                DELETE FROM produto_imagens
                WHERE img_codigo = :img_codigo
            ";

            $conn->query($sql, array("img_codigo" => $img_codigo));

        }
        else{
            throw new \Exception("Erro ao excluir imagem! Entre em contato com o Garnica imediatamente");
        }

    }

    public function update($prd_codigo, $prd_descricao, $prd_preco, $prd_peso, $prd_largura, $prd_altura, $prd_comprimento, $prd_obs, array $categorias)
    {

        try{

            $conn = new Sql();

            $conn->beginTransaction();

            $sql = "
                
                UPDATE produtos
                SET prd_descricao = :prd_descricao,
                    prd_preco = :prd_preco,
                    prd_peso = :prd_peso,
                    prd_largura = :prd_largura,
                    prd_altura = :prd_altura,
                    prd_comprimento = :prd_comprimento,
                    prd_obs = :prd_obs
                WHERE prd_codigo = :prd_codigo;

                DELETE FROM produto_categoria
                WHERE prd_codigo = :prd_codigo;

            ";

            $dados = array(
                ":prd_descricao" => $prd_descricao,
                ":prd_preco" => $prd_preco,
                ":prd_peso" => $prd_peso,
                ":prd_largura" => $prd_largura,
                ":prd_altura" => $prd_altura,
                ":prd_comprimento" => $prd_comprimento,
                ":prd_obs" => $prd_obs,
                ":prd_codigo" => $prd_codigo
            );

            $conn->query($sql, $dados);

            self::inserirCategorias($categorias, $prd_codigo, $conn);

            $conn->commit();
        }catch(\Exception $erro){
            $conn->rollback();
            throw new \Exception($erro->getMessage());
        }

    }

    public static function inserirCategorias(array $categorias, $prd_codigo,  $conn)
    {

        foreach ($categorias as $categoria){

            $categoria = (int) $categoria;

            $sql = "
                INSERT INTO produto_categoria
                (cat_codigo, prd_codigo)
                VALUES 
                (:cat_codigo, :prd_codigo)
            ";
            $conn->query($sql, array(
                ":cat_codigo" => $categoria,
                ":prd_codigo" => $prd_codigo
            ));

        }

    }

    public static function delete($prd_codigo)
    {

        $conn = new Sql();

        $sql = "
            SELECT img_codigo 
            FROM produto_imagens 
            WHERE prd_codigo = :prd_codigo
        ";

        $results = $conn->select($sql, array( ":prd_codigo" => $prd_codigo ));

        foreach ($results as $img){

            self::deleteImage($img['img_codigo']);

        }

        $sql = "
            DELETE FROM produto_categoria
            WHERE prd_codigo = :prd_codigo;

            DELETE FROM produtos
            WHERE prd_codigo = :prd_codigo;
        ";

        $conn->query($sql, array( ":prd_codigo" => $prd_codigo ));

    }

}