<?php

namespace controle\model;

use controle\DB\Sql;
use Exception;
use controle\Model;

class Dashboard{
    //upload do estoque do aton
    public function getB2wStiloDash(){

        $sql = new Sql();

        return $sql->select("SELECT count(*) from tb_b2w_stilo a inner join tb_aton_estoque_stilo b on a.sku = b.id_produto
        where a.estoque != b.estoque_aton");

    }

    public function getB2wClickDash(){

        $sql = new Sql();

        return $sql->select("SELECT count(*) from tb_b2w_Click a inner join tb_aton_estoque_Click b on a.sku = b.id_produto
        where a.estoque != b.estoque_aton");

    }

    public function getMagaluStiloDash(){

        $sql = new Sql();

        return $sql->select("SELECT count(*) from tb_magalu_stilo a inner join tb_aton_estoque_stilo b on a.sku = b.id_produto
        where a.estoque != b.estoque_aton");

    }

    public function getMagaluClickDash(){

        $sql = new Sql();

        return $sql->select("SELECT count(*) from tb_magalu_click a inner join tb_aton_estoque_Click b on a.sku = b.id_produto
        where a.estoque != b.estoque_aton");

    }

    public function getmlClickDash(){

        $sql = new Sql();

        return $sql->select("SELECT count(*) from tb_mlclick a inner join tb_aton_estoque_Click b on a.id_produto = b.id_produto
        where a.qtd != b.estoque_aton");

    }

}