<?php

namespace controle\model;

use controle\DB\Sql;
use Exception;
use controle\Model;
use DOMComment;
use DOMDocument;
use PDO;
use PDOException;
use PDORow;

class Preco{

    public static function uploadPrecoStilo($planilha){

        if(!empty($planilha["tmp_name"])){
            $arquivo = new DOMDocument();
            $arquivo->load($planilha["tmp_name"]);
            
            $linhas = $arquivo->getElementsByTagName("Row");
            $primeiralinha = true;
            foreach($linhas as $value){
                if($primeiralinha == false){
                $sku = $value->getElementsByTagName("Data")->item(0)->nodeValue;
                $preco = $value->getElementsByTagName("Data")->item(1)->nodeValue;
                $preco = $preco*(1);
                     $sql = new Sql();
                     $consul = $sql->select("SELECT * FROM tb_aton_preco_stilo WHERE id_produto = :sku",array(
                         ":sku"=>$sku
                     ));
                     if(count($consul) === 0){
                        $sql->select("INSERT INTO tb_aton_preco_stilo VALUES(NULL, :sku, :preco)",array(
                            ":sku"=>$sku,
                            ":preco"=>$preco
                        ));
    
                     }else{
                        $sql->select("UPDATE tb_aton_preco_stilo SET preco_venda = :preco where id_produto = :sku",array(
                            ":sku"=>$sku,
                            ":preco"=>$preco
                        ));
                    } } 
                    $primeiralinha = false;
                }  
    
                     }

    }

    public static function uploadPrecoClick($planilha){

        if(!empty($planilha["tmp_name"])){
            $arquivo = new DOMDocument();
            $arquivo->load($planilha["tmp_name"]);
            
            $linhas = $arquivo->getElementsByTagName("Row");
            $primeiralinha = true;
            foreach($linhas as $value){
                if($primeiralinha == false){
                $sku = $value->getElementsByTagName("Data")->item(0)->nodeValue;
                $preco = $value->getElementsByTagName("Data")->item(1)->nodeValue;
                     $sql = new Sql();
                     $consul = $sql->select("SELECT * FROM tb_aton_preco_click WHERE id_produto = :sku",array(
                         ":sku"=>$sku
                     ));
                     if(count($consul) === 0){
                        $sql->select("INSERT INTO tb_aton_preco_click VALUES(NULL, :sku, :preco)",array(
                            ":sku"=>$sku,
                            ":preco"=>$preco
                        ));
    
                     }else{
                        $sql->select("UPDATE tb_aton_preco_click SET preco_venda = :preco where id_produto = :sku",array(
                            ":sku"=>$sku,
                            ":preco"=>$preco
                        ));
                    } } 
                    $primeiralinha = false;
                }  
    
                     }


    }
    //paginação b2w
    public static function precob2wstilo($page = 1, $itensporpage = 200){
        $start = ($page - 1)* $itensporpage;

        $sql = new Sql();
        $result = $sql->select("SELECT sql_calc_found_rows * , if(preco = preco_venda, :v, :f) as Comparativo
        FROM tb_b2w_stilo a inner join tb_aton_preco_stilo b on a.sku = b.id_produto order by nome asc
        limit $start, $itensporpage",array(
            ":v"=>"Preço correto!",
            ":f"=>"Preço divergente!"
        ));

        $resulttotal = $sql->select("SELECT found_rows() AS NRTOTAL");
        $valor = ($resulttotal[0]["NRTOTAL"]);
         return[
        'data'=>$result,
        'total'=>(int)$resulttotal[0]["NRTOTAL"],
        'pages'=>ceil($valor/$itensporpage)+1
         ];

    }

    public static function precob2wclick($page = 1, $itensporpage = 200){
        $start = ($page - 1)* $itensporpage;

        $sql = new Sql();
        $resutl = $sql->select("SELECT sql_calc_found_rows * , if(preco = preco_venda, :v, :f) as Comparativo
        FROM tb_b2w_click a inner join tb_aton_preco_click b on a.sku = b.id_produto order by nome asc
        limit $start, $itensporpage",array(
            ":v"=>"Preço correto!",
            ":f"=>"Preço divergente!"
        ));

        $resulttotal = $sql->select("SELECT found_rows() AS NRTOTAL");
        $valor = ($resulttotal[0]["NRTOTAL"]);
         return[
        'data'=>$resutl,
        'total'=>(int)$resulttotal[0]["NRTOTAL"],
        'pages'=>ceil($valor/$itensporpage)+1
         ];

    }
    //gerar planilha excel b2w
    public static function gerarPlanilhaPrecob2wstilo(){

        $arquivo = 'Preço_B2wStilo.xls';
        $html = '';
        $html .= '<table border="1">';

        $html .= '<tr>';
        $html .= '<td><b>ID</b></td>';
        $html .= '<td><b>Nome</b></td>';
        $html .= '<td><b>Comercializado</b></td>';
        $html .= '<td><b>Preço MKTP</b></td>';
        $html .= '<td><b>Preço ERP</b></td>';
        $html .= '<td><b>Comparativo</b></td>';
        $html .= '</tr>';
        
        $sql = new Sql();
        $result = $sql->select("SELECT sql_calc_found_rows * , if(preco = preco_venda, :v, :f) as Comparativo
        FROM tb_b2w_stilo a inner join tb_aton_preco_stilo b on a.sku = b.id_produto order by nome asc",array(
            ":v"=>"Preço correto!",
            ":f"=>"Preço divergente!"
        ));

        foreach($result as $value){
            $status = $value["status"] == "linked" ? "Integrado": "Recusado";
            $html .= '<tr>';
            $html .= '<td>'.$value["sku_produto"].'</td>';
            $html .= '<td>'.$value["nome"].'</td>';
            $html .= '<td>'.$status.'</td>';
            $html .= '<td>'.$value["preco"].'</td>';
            $html .= '<td>'.$value["preco_venda"].'</td>';
            $html .= '<td>'.$value["Comparativo"].'</td>';
            $html .= '</tr>';}

        header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header ("Last-Modified: ".gmdate ("D,d M YH: i : s")." GMT");
        header ("Cache-Control: no-cache, must-revalidate");
        header ("Pragma: no-cacho");
        header ("Content-type: application/x-msexcel");
        header ("Content-Disposition: attachment; filename=\"{$arquivo}\"");
        header ("Content-Description: PHP Generated Data");
        
        echo utf8_decode($html);

        exit;
    }

    public static function gerarPlanilhaPrecob2wclick(){

        $arquivo = 'Preço_B2wClick.xls';
        $html = '';
        $html .= '<table border="1">';

        $html .= '<tr>';
        $html .= '<td><b>ID</b></td>';
        $html .= '<td><b>Nome</b></td>';
        $html .= '<td><b>Comercializado</b></td>';
        $html .= '<td><b>Preço MKTP</b></td>';
        $html .= '<td><b>Preço ERP</b></td>';
        $html .= '<td><b>Comparativo</b></td>';
        $html .= '</tr>';
        
        $sql = new Sql();
        $result = $sql->select("SELECT * , if(preco = preco_venda, :v, :f) as Comparativo
        FROM tb_b2w_click a inner join tb_aton_preco_click b on a.sku = b.id_produto order by nome asc",array(
            ":v"=>"Preço correto!",
            ":f"=>"Preço divergente!"
        ));

        foreach($result as $value){
        $status = $value["status"] == "linked" ? "Integrado": "Recusado";
        $html .= '<tr>';
        $html .= '<td>'.$value["sku_produto"].'</td>';
        $html .= '<td>'.$value["nome"].'</td>';
        $html .= '<td>'.$status.'</td>';
        $html .= '<td>'.$value["preco"].'</td>';
        $html .= '<td>'.$value["preco_venda"].'</td>';
        $html .= '<td>'.$value["Comparativo"].'</td>';
        $html .= '</tr>';}

        header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header ("Last-Modified: ".gmdate ("D,d M YH: i : s")." GMT");
        header ("Cache-Control: no-cache, must-revalidate");
        header ("Pragma: no-cacho");
        header ("Content-type: application/x-msexcel");
        header ("Content-Disposition: attachment; filename=\"{$arquivo}\"");
        header ("Content-Description: PHP Generated Data");
        
        echo utf8_decode($html);

        exit;
    }
    //paginação magalu
    public static function precoMagalustilo($page = 1, $itensporpage = 200){    
        $start = ($page - 1)* $itensporpage;
         
        $sql = new Sql();

        $result = $sql->select("SELECT sql_calc_found_rows * , if(preco = preco_venda, :v, :f) as Comparativo
        FROM tb_magalu_stilo a inner join tb_aton_preco_stilo b on a.sku = b.id_produto order by nome asc",array(
            ":v"=>"Preço correto!",
            ":f"=>"Preço divergente!"
        ));

        $resulttotal = $sql->select("SELECT found_rows() AS NRTOTAL");
        $valor = ($resulttotal[0]["NRTOTAL"]);
         return[
        'data'=>$result,
        'total'=>(int)$resulttotal[0]["NRTOTAL"],
        'pages'=>ceil($valor/$itensporpage)+1
         ];

    }

    public static function precoMagaluclick($page = 1, $itensporpage = 200){
        $start = ($page - 1)* $itensporpage;
         
        $sql = new Sql();

        $result = $sql->select("SELECT sql_calc_found_rows * , if(preco = preco_venda, :v, :f) as Comparativo
        FROM tb_magalu_click a inner join tb_aton_preco_click b on a.sku = b.id_produto order by nome asc",array(
            ":v"=>"Preço correto!",
            ":f"=>"Preço divergente!"
        ));

        $resulttotal = $sql->select("SELECT found_rows() AS NRTOTAL");
        $valor = ($resulttotal[0]["NRTOTAL"]);
         return[
        'data'=>$result,
        'total'=>(int)$resulttotal[0]["NRTOTAL"],
        'pages'=>ceil($valor/$itensporpage)+1
         ];

    }
    //gerar planilha excel Magalu
    public static function gerarPlanilhaPrecoMaglustilo(){

        $arquivo = 'Preço_MagaluStilo.xls';
        $html = '';
        $html .= '<table border="1">';

        $html .= '<tr>';
        $html .= '<td><b>ID</b></td>';
        $html .= '<td><b>Nome</b></td>';
        $html .= '<td><b>Preço MKTP</b></td>';
        $html .= '<td><b>Preço ERP</b></td>';
        $html .= '<td><b>Comparativo</b></td>';
        $html .= '</tr>';
        
        $sql = new Sql();
        $result = $sql->select("SELECT * , if(preco = preco_venda, :v, :f) as Comparativo
        FROM tb_magalu_stilo a inner join tb_aton_preco_stilo b on a.sku = b.id_produto order by nome asc",array(
            ":v"=>"Preço correto!",
            ":f"=>"Preço divergente!"
        ));

        foreach($result as $value){
        $html .= '<tr>';
        $html .= '<td>'.$value["id_produto"].'</td>';
        $html .= '<td>'.$value["nome"].'</td>';
        $html .= '<td>'.$value["preco"].'</td>';
        $html .= '<td>'.$value["preco_venda"].'</td>';
        $html .= '<td>'.$value["Comparativo"].'</td>';
        $html .= '</tr>';}

        header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header ("Last-Modified: ".gmdate ("D,d M YH: i : s")." GMT");
        header ("Cache-Control: no-cache, must-revalidate");
        header ("Pragma: no-cacho");
        header ("Content-type: application/x-msexcel");
        header ("Content-Disposition: attachment; filename=\"{$arquivo}\"");
        header ("Content-Description: PHP Generated Data");
        
        echo utf8_decode($html);

        exit;
    }
    //gerar planilha excel Magalu
    public static function gerarPlanilhaPrecoMagluclick(){

            $arquivo = 'Preço_MagaluClick.xls';
            $html = '';
            $html .= '<table border="1">';
    
            $html .= '<tr>';
            $html .= '<td><b>ID</b></td>';
            $html .= '<td><b>Nome</b></td>';
            $html .= '<td><b>Preço MKTP</b></td>';
            $html .= '<td><b>Preço ERP</b></td>';
            $html .= '<td><b>Comparativo</b></td>';
            $html .= '</tr>';
            
            $sql = new Sql();
            $result = $sql->select("SELECT * , if(preco = preco_venda, :v, :f) as Comparativo
            FROM tb_magalu_click a inner join tb_aton_preco_click b on a.sku = b.id_produto order by nome asc",array(
                ":v"=>"Estoque correto!",
                ":f"=>"Estoque divergente!"
            ));
    
            foreach($result as $value){
            $html .= '<tr>';
            $html .= '<td>'.$value["id_produto"].'</td>';
            $html .= '<td>'.$value["nome"].'</td>';
            $html .= '<td>'.$value["preco"].'</td>';
            $html .= '<td>'.$value["preco_venda"].'</td>';
            $html .= '<td>'.$value["Comparativo"].'</td>';
            $html .= '</tr>';}
    
            header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header ("Last-Modified: ".gmdate ("D,d M YH: i : s")." GMT");
            header ("Cache-Control: no-cache, must-revalidate");
            header ("Pragma: no-cacho");
            header ("Content-type: application/x-msexcel");
            header ("Content-Disposition: attachment; filename=\"{$arquivo}\"");
            header ("Content-Description: PHP Generated Data");
            
            echo utf8_decode($html);
    
            exit;
    }
    //paginação mercado livre click
    public static function precoMlclcik($page = 1, $itensporpage = 200){
        $start = ($page - 1)* $itensporpage;
         
        $sql = new Sql();

        $result = $sql->select("SELECT sql_calc_found_rows * , if(preco = preco_venda, :v, :f) as Comparativo
        FROM tb_mlclick a inner join tb_aton_preco_click b on a.id_produto = b.id_produto order by name asc",array(
            ":v"=>"Preço correto!",
            ":f"=>"Preço divergente!"
        ));

        $resulttotal = $sql->select("SELECT found_rows() AS NRTOTAL");
        $valor = ($resulttotal[0]["NRTOTAL"]);
         return[
        'data'=>$result,
        'total'=>(int)$resulttotal[0]["NRTOTAL"],
        'pages'=>ceil($valor/$itensporpage)+1
         ];

    }
    //gerar planilha excel Magalu
    public static function gerarPlanilhaPrecoMlclick(){

        $arquivo = 'Preço_MlClick.xls';
        $html = '';
        $html .= '<table border="1">';

        $html .= '<tr>';
        $html .= '<td><b>ID</b></td>';
        $html .= '<td><b>MLB</b></td>';
        $html .= '<td><b>Nome</b></td>';
        $html .= '<td><b>Preço MKTP</b></td>';
        $html .= '<td><b>Preço ERP</b></td>';
        $html .= '<td><b>Comparativo</b></td>';
        $html .= '</tr>';
        
        $sql = new Sql();
        $result = $sql->select("SELECT sql_calc_found_rows * , if(preco = preco_venda, :v, :f) as Comparativo
        FROM tb_mlclick a inner join tb_aton_preco_click b on a.id_produto = b.id_produto order by name asc",array(
            ":v"=>"Preço correto!",
            ":f"=>"Preço divergente!"
        ));

        foreach($result as $value){
        $html .= '<tr>';
        $html .= '<td>'.$value["id_produto"].'</td>';
        $html .= '<td>'.$value["mlb"].'</td>';
        $html .= '<td>'.$value["name"].'</td>';
        $html .= '<td>'.$value["preco"].'</td>';
        $html .= '<td>'.$value["preco_venda"].'</td>';
        $html .= '<td>'.$value["Comparativo"].'</td>';
        $html .= '</tr>';}

        header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header ("Last-Modified: ".gmdate ("D,d M YH: i : s")." GMT");
        header ("Cache-Control: no-cache, must-revalidate");
        header ("Pragma: no-cacho");
        header ("Content-type: application/x-msexcel");
        header ("Content-Disposition: attachment; filename=\"{$arquivo}\"");
        header ("Content-Description: PHP Generated Data");
        
        echo utf8_decode($html);

        exit;
}
















}