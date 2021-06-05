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

class estoque{
    //upload do estoque do aton
    public static function uploadEstoqueStilo($planilha){

        if(!empty($planilha["tmp_name"])){
            $arquivo = new DOMDocument();
            $arquivo->load($planilha["tmp_name"]);
            
            $linhas = $arquivo->getElementsByTagName("Row");
            $primeiralinha = true;
            foreach($linhas as $value){
                if($primeiralinha == false){
                $sku = $value->getElementsByTagName("Data")->item(0)->nodeValue;
                $estoque = $value->getElementsByTagName("Data")->item(1)->nodeValue;
                     $sql = new Sql();
                     $consul = $sql->select("SELECT * FROM tb_aton_estoque_stilo WHERE id_produto = :sku",array(
                         ":sku"=>$sku
                     ));
                     if(count($consul) === 0){
                        $sql->select("INSERT INTO tb_aton_estoque_stilo VALUES(NULL, :sku, :estoque)",array(
                            ":sku"=>$sku,
                            ":estoque"=>$estoque
                        ));
    
                     }else{
                        $sql->select("UPDATE tb_aton_estoque_stilo SET estoque_aton = :estoque where id_produto = :sku",array(
                            ":sku"=>$sku,
                            ":estoque"=>$estoque
                        ));
                    } } 
                    $primeiralinha = false;
                }  
    
                     }

    }

    public static function uploadEstoqueClick($planilha){

        if(!empty($planilha["tmp_name"])){
            $arquivo = new DOMDocument();
            $arquivo->load($planilha["tmp_name"]);
            
            $linhas = $arquivo->getElementsByTagName("Row");
            $primeiralinha = true;
            foreach($linhas as $value){
                if($primeiralinha == false){
                $sku = $value->getElementsByTagName("Data")->item(0)->nodeValue;
                $estoque = $value->getElementsByTagName("Data")->item(1)->nodeValue;
                     $sql = new Sql();
                     $consul = $sql->select("SELECT * FROM tb_aton_estoque_click WHERE id_produto = :sku",array(
                         ":sku"=>$sku
                     ));
                     if(count($consul) === 0){
                        $sql->select("INSERT INTO tb_aton_estoque_click VALUES(NULL, :sku, :estoque)",array(
                            ":sku"=>$sku,
                            ":estoque"=>$estoque
                        ));
    
                     }else{
                        $sql->select("UPDATE tb_aton_estoque_click SET estoque_aton = :estoque where id_produto = :sku",array(
                            ":sku"=>$sku,
                            ":estoque"=>$estoque
                        ));
                    } } 
                    $primeiralinha = false;
                }  
    
                     }

    }
    //paginação b2w
    public static function estoqueb2wstilo($page = 1, $search, $filter, $itensporpage = 200){
        $start = ($page - 1)* $itensporpage;

        $ativo = (isset($filter["status"])) ? $filter["status"] : '';
        $ativo = ($ativo == 'Ativo') ? 'linked': '%%';
        $inativo = (isset($filter["status"])) ? $filter["status"] : '';
        $inativo = ($inativo == 'Inativo') ? 'linked': '';
        $Alerta_divergente = (isset($filter["Comparativo"])) ? $filter["Comparativo"] : '';
        $Alerta_divergente = ($Alerta_divergente == 'Alerta') ? 'Alerta': '';
        $Estoque_Correto = (isset($filter["Comparativo"])) ? $filter["Comparativo"] : '';
        $Estoque_Correto = ($Estoque_Correto == 'Correto') ? 'Correto': '';

        $sql = new Sql();
        $result = $sql->select("SELECT sql_calc_found_rows * , if(estoque = estoque_aton, :v, :f) as Comparativo
        FROM tb_b2w_stilo a inner join tb_aton_estoque_stilo b on a.sku = b.id_produto and status LIKE :ativo and
        status != :inativo and IF(:Alerta_divergente = 'Alerta', estoque != estoque_aton, estoque like '%%') AND
        IF(:Estoque_Correto = 'Correto', estoque = estoque_aton, estoque like '%%') Where a.nome LIKE :name or a.sku = :sku order by nome asc limit $start, $itensporpage",array(
            ":v"=>"Estoque correto",
            ":f"=>"Estoque divergente",
            ":name"=>'%'.$search.'%',
            ":sku"=>$search,
            ":ativo"=>$ativo,
            ":inativo"=>$inativo,
            ":Alerta_divergente"=>$Alerta_divergente,
            ":Estoque_Correto"=>$Estoque_Correto
        ));

        $resulttotal = $sql->select("SELECT found_rows() AS NRTOTAL");
        $valor = ($resulttotal[0]["NRTOTAL"]);
         return[
        'data'=>$result,
        'total'=>(int)$resulttotal[0]["NRTOTAL"],
        'pages'=>ceil($valor/$itensporpage)+1
         ];

    }

    public static function estoqueb2wclick($page = 1, $search, $itensporpage = 200){
        $start = ($page - 1)* $itensporpage;

        $sql = new Sql();
        $result = $sql->select("SELECT sql_calc_found_rows * , if(estoque = estoque_aton, :v, :f) as Comparativo
        FROM tb_b2w_click a inner join tb_aton_estoque_click b on a.sku = b.id_produto
        Where a.nome LIKE :name or a.sku = :sku order by nome asc limit $start, $itensporpage",array(
            ":v"=>"Preço correto!",
            ":f"=>"Preço divergente!",
            ":name"=>'%'.$search.'%',
            ":sku"=>$search
        ));
    
        $resulttotal = $sql->select("SELECT found_rows() AS NRTOTAL");
        $valor = ($resulttotal[0]["NRTOTAL"]);
         return[
        'data'=>$result,
        'total'=>(int)$resulttotal[0]["NRTOTAL"],
        'pages'=>ceil($valor/$itensporpage)+1
         ];

    }
    //gerar planilha excel b2w
    public static function gerarPlanilhab2wstilo(){

        $arquivo = 'Estoque_B2wStilo.xls';
        $html = '';
        $html .= '<table border="1">';

        $html .= '<tr>';
        $html .= '<td><b>ID</b></td>';
        $html .= '<td><b>Nome</b></td>';
        $html .= '<td><b>Comercializado</b></td>';
        $html .= '<td><b>Estoque MKTP</b></td>';
        $html .= '<td><b>Estoque ERP</b></td>';
        $html .= '<td><b>Comparativo</b></td>';
        $html .= '</tr>';
        
        $sql = new Sql();
        $result = $sql->select("SELECT * , if(estoque = estoque_aton, :v, :f) as Comparativo
        FROM tb_b2w_stilo a inner join tb_aton_estoque_stilo b on a.sku = b.id_produto order by nome asc",array(
            ":v"=>"Estoque correto!",
            ":f"=>"Estoque divergente!"
        ));

        foreach($result as $value){
            $status = $value["status"] == "linked" ? "Integrado": "Recusado";
            $html .= '<tr>';
            $html .= '<td>'.$value["sku_produto"].'</td>';
            $html .= '<td>'.$value["nome"].'</td>';
            $html .= '<td>'.$status.'</td>';
            $html .= '<td>'.$value["estoque"].'</td>';
            $html .= '<td>'.$value["estoque_aton"].'</td>';
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

    public static function gerarPlanilhab2wclick(){

        $arquivo = 'Estoque_B2wClick.xls';
        $html = '';
        $html .= '<table border="1">';

        $html .= '<tr>';
        $html .= '<td><b>ID</b></td>';
        $html .= '<td><b>Nome</b></td>';
        $html .= '<td><b>Comercializado</b></td>';
        $html .= '<td><b>Estoque MKTP</b></td>';
        $html .= '<td><b>Estoque ERP</b></td>';
        $html .= '<td><b>Comparativo</b></td>';
        $html .= '</tr>';
        
        $sql = new Sql();
        $result = $sql->select("SELECT * , if(estoque = estoque_aton, :v, :f) as Comparativo
        FROM tb_b2w_click a inner join tb_aton_estoque_click b on a.sku = b.id_produto order by nome asc",array(
            ":v"=>"Estoque correto!",
            ":f"=>"Estoque divergente!"
        ));

        foreach($result as $value){
        $status = $value["status"] == "linked" ? "Integrado": "Recusado";
        $html .= '<tr>';
        $html .= '<td>'.$value["sku_produto"].'</td>';
        $html .= '<td>'.$value["nome"].'</td>';
        $html .= '<td>'.$status.'</td>';
        $html .= '<td>'.$value["estoque"].'</td>';
        $html .= '<td>'.$value["estoque_aton"].'</td>';
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
    public static function estoqueMagalustilo($page = 1, $search, $itensporpage = 200){
        $start = ($page - 1)* $itensporpage;
         
        $sql = new Sql();

        $result = $sql->select("SELECT sql_calc_found_rows * , if(estoque = estoque_aton, :v, :f) as Comparativo
        FROM tb_magalu_stilo a inner join tb_aton_estoque_stilo b on a.sku = b.id_produto
        Where a.nome LIKE :name or a.sku = :sku order by nome asc limit $start, $itensporpage",array(
            ":v"=>"Preço correto!",
            ":f"=>"Preço divergente!",
            ":name"=>'%'.$search.'%',
            ":sku"=>$search
        ));

        $resulttotal = $sql->select("SELECT found_rows() AS NRTOTAL");
        $valor = ($resulttotal[0]["NRTOTAL"]);
         return[
        'data'=>$result,
        'total'=>(int)$resulttotal[0]["NRTOTAL"],
        'pages'=>ceil($valor/$itensporpage)+1
         ];

    }

    public static function estoqueMagaluclick($page = 1, $search, $itensporpage = 200){
        $start = ($page - 1)* $itensporpage;
         
        $sql = new Sql();

        $result = $sql->select("SELECT sql_calc_found_rows * , if(estoque = estoque_aton, :v, :f) as Comparativo
        FROM tb_magalu_click a inner join tb_aton_estoque_click b on a.sku = b.id_produto
        Where a.nome LIKE :name or a.sku = :sku order by nome asc limit $start, $itensporpage",array(
            ":v"=>"Preço correto!",
            ":f"=>"Preço divergente!",
            ":name"=>'%'.$search.'%',
            ":sku"=>$search
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
    public static function gerarPlanilhaMaglustilo(){

        $arquivo = 'Estoque_MagaluStilo.xls';
        $html = '';
        $html .= '<table border="1">';

        $html .= '<tr>';
        $html .= '<td><b>ID</b></td>';
        $html .= '<td><b>Nome</b></td>';
        $html .= '<td><b>Estoque MKTP</b></td>';
        $html .= '<td><b>Estoque ERP</b></td>';
        $html .= '<td><b>Comparativo</b></td>';
        $html .= '</tr>';
        
        $sql = new Sql();
        $result = $sql->select("SELECT * , if(estoque = estoque_aton, :v, :f) as Comparativo
        FROM tb_magalu_stilo a inner join tb_aton_estoque_stilo b on a.sku = b.id_produto order by nome asc",array(
            ":v"=>"Estoque correto!",
            ":f"=>"Estoque divergente!"
        ));

        foreach($result as $value){
        $html .= '<tr>';
        $html .= '<td>'.$value["id_produto"].'</td>';
        $html .= '<td>'.$value["nome"].'</td>';
        $html .= '<td>'.$value["estoque"].'</td>';
        $html .= '<td>'.$value["estoque_aton"].'</td>';
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
    public static function gerarPlanilhaMagluclick(){

            $arquivo = 'Estoque_MagaluClick.xls';
            $html = '';
            $html .= '<table border="1">';
    
            $html .= '<tr>';
            $html .= '<td><b>ID</b></td>';
            $html .= '<td><b>Nome</b></td>';
            $html .= '<td><b>Estoque MKTP</b></td>';
            $html .= '<td><b>Estoque ERP</b></td>';
            $html .= '<td><b>Comparativo</b></td>';
            $html .= '</tr>';
            
            $sql = new Sql();
            $result = $sql->select("SELECT * , if(estoque = estoque_aton, :v, :f) as Comparativo
            FROM tb_magalu_click a inner join tb_aton_estoque_click b on a.sku = b.id_produto order by nome asc",array(
                ":v"=>"Estoque correto!",
                ":f"=>"Estoque divergente!"
            ));
    
            foreach($result as $value){
            $html .= '<tr>';
            $html .= '<td>'.$value["id_produto"].'</td>';
            $html .= '<td>'.$value["nome"].'</td>';
            $html .= '<td>'.$value["estoque"].'</td>';
            $html .= '<td>'.$value["estoque_aton"].'</td>';
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
    public static function estoqueMlclcik($page = 1, $itensporpage = 200){
        $start = ($page - 1)* $itensporpage;
         
        $sql = new Sql();

        $result = $sql->select("SELECT sql_calc_found_rows * , if(qtd = estoque_aton, :v, :f) as Comparativo
        FROM tb_mlclick a inner join tb_aton_estoque_click b on a.id_produto = b.id_produto order by name asc
        limit $start, $itensporpage",array(
            ":v"=>"Estoque correto!",
            ":f"=>"Estoque divergente!"
        ));

        $resulttotal = $sql->select("SELECT found_rows() AS NRTOTAL");
        $valor = ($resulttotal[0]["NRTOTAL"]);
         return[
        'data'=>$result,
        'total'=>(int)$resulttotal[0]["NRTOTAL"],
        'pages'=>ceil($valor/$itensporpage)+1
         ];

    }

    public static function estoqueMlClickFilter($page = 1, $search, $itensporpage = 200){
        
        $start = ($page - 1)* $itensporpage;
        $sql = new Sql();

        $result = $sql->select("SELECT sql_calc_found_rows * ,  if(qtd = estoque_aton, :v, :f) as Comparativo
        FROM tb_mlclick a inner join tb_aton_estoque_click b on a.id_produto = b.id_produto
        Where a.name LIKE :name or a.id_produto = :sku limit $start, $itensporpage",array(
            ":v"=>"Preço correto!",
            ":f"=>"Preço divergente!",
            ":name"=>'%'.$search.'%',
            ":sku"=>$search
            
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
    public static function gerarPlanilhaMlclick(){

        $arquivo = 'Estoque_MlClick.xls';
        $html = '';
        $html .= '<table border="1">';

        $html .= '<tr>';
        $html .= '<td><b>ID</b></td>';
        $html .= '<td><b>MLB</b></td>';
        $html .= '<td><b>Nome</b></td>';
        $html .= '<td><b>Tipo de Anúncio</b></td>';
        $html .= '<td><b>Estoque MKTP</b></td>';
        $html .= '<td><b>Estoque ERP</b></td>';
        $html .= '<td><b>Status</b></td>';
        $html .= '<td><b>Comparativo</b></td>';
        $html .= '</tr>';
        
        $sql = new Sql();
        $result = $sql->select("SELECT sql_calc_found_rows * , if(qtd = estoque_aton, :v, :f) as Comparativo
        FROM tb_mlclick a inner join tb_aton_estoque_click b on a.id_produto = b.id_produto order by name asc",array(
            ":v"=>"Estoque correto!",
            ":f"=>"Estoque divergente!"
        ));

        foreach($result as $value){
        $html .= '<tr>';
        $html .= '<td>'.$value["id_produto"].'</td>';
        $html .= '<td>'.$value["mlb"].'</td>';
        $html .= '<td>'.$value["name"].'</td>';
        $html .= '<td>'.$value["tipo"].'</td>';
        $html .= '<td>'.$value["qtd"].'</td>';
        $html .= '<td>'.$value["estoque_aton"].'</td>';
        $html .= '<td>'.$value["status"].'</td>';
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
//file:///C:/Users/vitor/OneDrive/%C3%81rea%20de%20Trabalho/Template/AdminLTE-master/pages/search/enhanced.html


}