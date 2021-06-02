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

class product{

    public static function apiB2wStilo($pg){

        $page = [];
            ob_start();
            $email = 'contato@stiloeletro.com.br';
            $pass = 'uEK5MypMq6ecUENYwmxy';
            $pass64x = 'dUVLNU15cE1xNmVjVUVOWXdteHk=';
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, 'https://api.skyhub.com.br/products?page='.$pg.'&per_page=99'); 
            curl_setopt( $ch, CURLOPT_HEADER, 0 );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('X-User-Email:'.$email,'X-Api-Key:'.$pass,'X-Accountmanager-Key:'.$pass64x) );
            curl_exec( $ch );
            $resposta = ob_get_contents();
            ob_end_clean();
            $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
            curl_close( $ch );

            header("Content-Type: text/html; charset=utf8");
                $array = json_decode($resposta, true);
                array_push($page, $array["products"]);

                return($page);
 }

    public static function updateb2wStilo(){
            
            for($i=0; $i<99; $i++){
            ob_start();
            $email = 'contato@stiloeletro.com.br';
            $pass = 'uEK5MypMq6ecUENYwmxy';
            $pass64x = 'dUVLNU15cE1xNmVjVUVOWXdteHk=';
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, 'https://api.skyhub.com.br/products?page='.$i.'&per_page=99'); 
            curl_setopt( $ch, CURLOPT_HEADER, 0 );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('X-User-Email:'.$email,'X-Api-Key:'.$pass,'X-Accountmanager-Key:'.$pass64x) );
            curl_exec( $ch );
            $resposta = ob_get_contents();
            ob_end_clean();
            $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
            curl_close( $ch );
            $array = json_decode($resposta, true);
            $products = $array["products"];

            if(!(count($products) === 0)){
            foreach($products as $value){
                $sku = ($value["sku"]);
                $nome = ($value["name"]);
                $preco = ($value["promotional_price"]);
                $qtd = ($value["qty"]);
                $status = ($value["associations"][0]["status"]);

                $sql = new Sql();
                $consul = $sql->select("SELECT * FROM tb_b2w_stilo WHERE sku_produto = :sku",array(
                    ":sku"=>$sku
                ));

                if(count($consul) === 0){
                    $sql->select("INSERT INTO tb_b2w_stilo VALUES(NULL, :sku, :nome, :preco, :qtd, null, :status)",array(
                        ":sku"=>$sku,
                        ":nome"=>$nome,
                        ":preco"=>$preco,
                        ":qtd"=>$qtd,
                        ":status"=>$status
                    ));

                }else{
                    $sql->select("UPDATE tb_b2w_stilo SET nome = :nome, preco = :preco, estoque = :qtd, status = :status WHERE sku_produto = :sku",array(
                        ":sku"=>$sku,
                        ":nome"=>$nome,
                        ":preco"=>$preco,
                        ":qtd"=>$qtd,
                        ":status"=>$status
                    ));
                }
            } 
            
            } 
        }
            $sql = new Sql();
            $result = $sql->select("SELECT sku_produto FROM tb_b2w_stilo");
            foreach($result as $value){
                $value = $value["sku_produto"];
                if(strpos($value, '-')){
                    $sku = explode('-', $value);
                    $sql->select("UPDATE tb_b2w_stilo SET sku = :sku WHERE sku_produto = :sku_produto",array(
                        ":sku_produto"=>$value,
                        ":sku"=>$sku[0]
                    ));  
                }else{
                    $sql->select("UPDATE tb_b2w_stilo SET sku = :sku WHERE sku_produto = :sku_produto",array(
                        ":sku_produto"=>$value,
                        ":sku"=>$value

                    ));     
                }
                
            } 
            
            

 }

    public static function apiB2wClick($pg){
            $page = [];
                ob_start();
                $email = 'comercial@click24.com.br';
                $pass = 'ZuPoArr5Bygveofen24U';
                $pass64x = 'WnVQb0FycjVCeWd2ZW9mZW4yNFU=';
                $ch = curl_init();
                curl_setopt( $ch, CURLOPT_URL, 'https://api.skyhub.com.br/products?page='.$pg.'&per_page=99'); 
                curl_setopt( $ch, CURLOPT_HEADER, 0 );
                curl_setopt( $ch, CURLOPT_HTTPHEADER, array('X-User-Email:'.$email,'X-Api-Key:'.$pass,'X-Accountmanager-Key:'.$pass64x) );
                curl_exec( $ch );
                $resposta = ob_get_contents();
                ob_end_clean();
                $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
                curl_close( $ch );
            
                header("Content-Type: text/html; charset=utf8");
                    $array = json_decode($resposta, true);
                    array_push($page, $array["products"]);

                    return($page);
 } 

    public static function updateb2wClick(){
                for($i=0; $i<99; $i++){
                    ob_start();
                    $email = 'comercial@click24.com.br';
                    $pass = 'ZuPoArr5Bygveofen24U';
                    $pass64x = 'WnVQb0FycjVCeWd2ZW9mZW4yNFU=';
                    $ch = curl_init();
                    curl_setopt( $ch, CURLOPT_URL, 'https://api.skyhub.com.br/products?page='.$i.'&per_page=99'); 
                    curl_setopt( $ch, CURLOPT_HEADER, 0 );
                    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('X-User-Email:'.$email,'X-Api-Key:'.$pass,'X-Accountmanager-Key:'.$pass64x) );
                    curl_exec( $ch );
                    $resposta = ob_get_contents();
                    ob_end_clean();
                    $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
                    curl_close( $ch );
                    $array = json_decode($resposta, true);
                    $products = $array["products"];
        
                    if(!empty($products)){
                    foreach($products as $value){
                        $sku = ($value["sku"]);
                        $nome = ($value["name"]);
                        $preco = ($value["promotional_price"]);
                        $qtd = ($value["qty"]);
                        if(!empty($value["associations"][0]) || !empty($value["associations"])){
                        $status = ($value["associations"][0]["status"]);
                    }else{$qtd = null;}
                    
                        $sql = new Sql();
                        $consul = $sql->select("SELECT * FROM tb_b2w_click WHERE sku_produto = :sku",array(
                            ":sku"=>$sku
                        ));
        
                        if(count($consul) === 0){
                            $sql->select("INSERT INTO tb_b2w_click VALUES(NULL, :sku, :nome, :preco, :qtd, NULL, :status)",array(
                                ":sku"=>$sku,
                                ":nome"=>$nome,
                                ":preco"=>$preco,
                                ":qtd"=>$qtd,
                                ":status"=>$status
                            ));
        
                        }else{
                            $sql->select("UPDATE tb_b2w_click SET nome = :nome, preco = :preco, estoque = :qtd, status = :status WHERE sku_produto = :sku",array(
                                ":sku"=>$sku,
                                ":nome"=>$nome,
                                ":preco"=>$preco,
                                ":qtd"=>$qtd,
                                ":status"=>$status
                            ));
                        }
                    } 
                }
                    
                    } 
                    $sql = new Sql();
                    $result = $sql->select("SELECT sku_produto FROM tb_b2w_click");
                    foreach($result as $value){
                        $value = $value["sku_produto"];
                        if(strpos($value, '-')){
                            $sku = explode('-', $value);
                            $sql->select("UPDATE tb_b2w_click SET sku = :sku WHERE sku_produto = :sku_produto",array(
                                ":sku_produto"=>$value,
                                ":sku"=>$sku[0]
                            ));  
                        }else{
                            $sql->select("UPDATE tb_b2w_click SET sku = :sku WHERE sku_produto = :sku_produto",array(
                                ":sku_produto"=>$value,
                                ":sku"=>$value
        
                            ));     
                        }
                        
                    }                    

 }    

    public static function apimagalustilo($pg){
            $page = [];
            $sku = [];
            $allSku = [];
            $arrayList = [];
            ob_start();

            $user = 'stiloeletroapi';
            $pass = '2017164G50';

            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, "https://api.integracommerce.com.br/api/Product?page=".$pg."&perPage=99" ); 
            curl_setopt( $ch, CURLOPT_HEADER, 0 );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode( $user . ':' . $pass ) ) );
            curl_exec( $ch );
            $resposta = ob_get_contents();
            ob_end_clean();
            $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
            curl_close( $ch );

            header("Content-Type: text/html; charset=utf8");
            $pages = json_decode($resposta, true);
            array_push($page, $pages["Products"]);
            $let = $page[0];
            foreach($let as $key => $value){

                array_push($sku, $value["Code"]);
            }

            foreach($sku as $key => $value){
            ob_start();
            
            $user = 'stiloeletroapi';
            $pass = '2017164G50';

            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, 'https://api.integracommerce.com.br/api/Sku/'.$value ); 
            curl_setopt( $ch, CURLOPT_HEADER, 0 );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode( $user . ':' . $pass ) ) );
            curl_exec( $ch );
            $resposta = ob_get_contents();
            ob_end_clean();
            $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
            curl_close( $ch );

            header("Content-Type: text/html; charset=utf8");
            $skus = json_decode($resposta, true);
            if(!($skus === 'Sku não encontrado')){
                array_push($allSku, $skus);
            }
            
        } 

        return $allSku;

 } 

    public static function updatemagalustilo(){
            $page = [];
            $sku = [];
            $allSku = [];
            $arrayList = [];
                for($i=1; $i<90; $i++){ 
            ob_start();
            $user = 'stiloeletroapi';
            $pass = '2017164G50';
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, "https://api.integracommerce.com.br/api/Product?page=".$i."&perPage=99" ); 
            curl_setopt( $ch, CURLOPT_HEADER, 0 );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode( $user . ':' . $pass ) ) );
            curl_exec( $ch );
            $resposta = ob_get_contents();
            ob_end_clean();
            $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
            curl_close( $ch );

            header("Content-Type: text/html; charset=utf8");
            $pages = json_decode($resposta, true);
            if(!(count($pages["Products"]) === 0)){
                array_push($page, $pages["Products"][0]);
                foreach($page as $key => $value){
                $sql = new Sql();
                    $consul = $sql->select("SELECT * FROM tb_magalu_stilo WHERE id_produto = :sku",array(
                        ":sku"=>$value["Code"]
                    ));  
                    if(count($consul) === 0){
                        $sql->select("INSERT INTO tb_magalu_stilo VALUES(NULL, :sku, null, null, null, null)",array(
                            ":sku"=>$value["Code"]
                        ));
                    } 
            }
            } } 
                
            $sql = new Sql();
            $result = $sql->select("SELECT id_produto FROM tb_magalu_stilo");
                foreach($result as $key => $value){
            ob_start();
            $user = 'stiloeletroapi';
            $pass = '2017164G50';

            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, 'https://api.integracommerce.com.br/api/Sku/'.$value["id_produto"]); 
            curl_setopt( $ch, CURLOPT_HEADER, 0 );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode( $user . ':' . $pass ) ) );
            curl_exec( $ch );
            $resposta = ob_get_contents();
            ob_end_clean();
            $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
            curl_close( $ch );

            header("Content-Type: text/html; charset=utf8");
            $resp = json_decode($resposta, true);
            if(!($resp === 'Sku não encontrado')){
                    $skus = ($resp["IdSku"]);
                    $nome = ($resp["Name"]);
                    $preco = ($resp["Price"]["SalePrice"]);
                    $qtd = ($resp["StockQuantity"]);

                    $sql = new Sql();
                    $consul = $sql->select("SELECT * FROM tb_magalu_stilo WHERE id_produto = :sku",array(
                        ":sku"=>$skus
                    ));
                    
                    $sql->select("UPDATE tb_magalu_stilo SET id_produto = :sku, nome = :name, preco = :preco, estoque = :qtd WHERE id_produto = :sku",array(
                            ":sku"=>$skus,
                            ":name"=>$nome,
                            ":preco"=>$preco,
                            ":qtd"=>$qtd
                        ));
                    
                
                
            }

            } 
                $sql = new Sql();
                $result = $sql->select("SELECT id_produto FROM tb_magalu_stilo");
                foreach($result as $value){
                    $value = $value["id_produto"];
                if(strpos($value, '-')){
                    $sku = explode('-', $value);
                    $sql->select("UPDATE tb_magalu_stilo SET sku = :sku WHERE id_produto = :sku_produto",array(
                        ":sku_produto"=>$value,
                        ":sku"=>$sku[0]
                    ));  
                }else{
                    $sql->select("UPDATE tb_magalu_stilo SET sku = :sku WHERE id_produto = :sku_produto",array(
                        ":sku_produto"=>$value,
                        ":sku"=>$value

                    ));     
                }
                
            }    



        
 }

    public static function apimagaluclick($pg){
        $page = [];
        $sku = [];
        $allSku = [];
        $arrayList = [];
        ob_start();

        $user = 'click24api';
        $pass = 'ScvjSHljAXpBMgOBd-wL';

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, "https://api.integracommerce.com.br/api/Product?page=".$pg."&perPage=99" ); 
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode( $user . ':' . $pass ) ) );
        curl_exec( $ch );
        $resposta = ob_get_contents();
        ob_end_clean();
        $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        curl_close( $ch );

        header("Content-Type: text/html; charset=utf8");
        $pages = json_decode($resposta, true);
        array_push($page, $pages["Products"]);
        $let = $page[0];
        foreach($let as $key => $value){

            array_push($sku, $value["Code"]);
        }

        foreach($sku as $key => $value){
        ob_start();

        $user = 'click24api';
        $pass = 'ScvjSHljAXpBMgOBd-wL';

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, 'https://api.integracommerce.com.br/api/Sku/'.$value ); 
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode( $user . ':' . $pass ) ) );
        curl_exec( $ch );
        $resposta = ob_get_contents();
        ob_end_clean();
        $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        curl_close( $ch );

        header("Content-Type: text/html; charset=utf8");
        $skus = json_decode($resposta, true);
        if(!($skus === 'Sku não encontrado')){
            array_push($allSku, $skus);
        }

        } 

        return $allSku;

 } 

    public static function updatemagaluclick(){
            
            $page = [];
            $sku = [];
            $allSku = [];
            $arrayList = [];
                for($i=1; $i<90; $i++){ 
            ob_start();
            $user = 'click24api';
            $pass = 'ScvjSHljAXpBMgOBd-wL';
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, "https://api.integracommerce.com.br/api/Product?page=".$i."&perPage=99" ); 
            curl_setopt( $ch, CURLOPT_HEADER, 0 );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode( $user . ':' . $pass ) ) );
            curl_exec( $ch );
            $resposta = ob_get_contents();
            ob_end_clean();
            $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
            curl_close( $ch );

            header("Content-Type: text/html; charset=utf8");
            $pages = json_decode($resposta, true);
            if(!(count($pages["Products"]) === 0)){
                array_push($page, $pages["Products"][0]);
                foreach($page as $key => $value){
                $sql = new Sql();
                    $consul = $sql->select("SELECT * FROM tb_magalu_click WHERE id_produto = :sku",array(
                        ":sku"=>$value["Code"]
                    ));  
                    if(count($consul) === 0){
                        $sql->select("INSERT INTO tb_magalu_click VALUES(NULL, :sku, null, null, null, null)",array(
                            ":sku"=>$value["Code"]
                        ));
                    } 
            }
            } } 
                
            $sql = new Sql();
            $result = $sql->select("SELECT id_produto FROM tb_magalu_click");
                foreach($result as $key => $value){
            ob_start();
            $user = 'click24api';
            $pass = 'ScvjSHljAXpBMgOBd-wL';

            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, 'https://api.integracommerce.com.br/api/Sku/'.$value["id_produto"]); 
            curl_setopt( $ch, CURLOPT_HEADER, 0 );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode( $user . ':' . $pass ) ) );
            curl_exec( $ch );
            $resposta = ob_get_contents();
            ob_end_clean();
            $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
            curl_close( $ch );

            header("Content-Type: text/html; charset=utf8");
            $resp = json_decode($resposta, true);
            if(!($resp === 'Sku não encontrado')){
                    $skus = ($resp["IdSku"]);
                    $nome = ($resp["Name"]);
                    $preco = ($resp["Price"]["SalePrice"]);
                    $qtd = ($resp["StockQuantity"]);

                    $sql = new Sql();
                    $consul = $sql->select("SELECT * FROM tb_magalu_click WHERE id_produto = :sku",array(
                        ":sku"=>$skus
                    ));
                    
                    $sql->select("UPDATE tb_magalu_click SET id_produto = :sku, nome = :name, preco = :preco, estoque = :qtd WHERE id_produto = :sku",array(
                            ":sku"=>$skus,
                            ":name"=>$nome,
                            ":preco"=>$preco,
                            ":qtd"=>$qtd
                        ));
                    
                
                
            }

            } 
                $sql = new Sql();
                $result = $sql->select("SELECT id_produto FROM tb_magalu_click");
                foreach($result as $value){
                    $value = $value["id_produto"];
                if(strpos($value, '-')){
                    $sku = explode('-', $value);
                    $sql->select("UPDATE tb_magalu_click SET sku = :sku WHERE id_produto = :sku_produto",array(
                        ":sku_produto"=>$value,
                        ":sku"=>$sku[0]
                    ));  
                }else{
                    $sql->select("UPDATE tb_magalu_click SET sku = :sku WHERE id_produto = :sku_produto",array(
                        ":sku_produto"=>$value,
                        ":sku"=>$value

                    ));     
                }
                
            }  

 }

    public static function uploadMlclick($resp){
            if(!empty($resp["tmp_name"])){
            $arquivo = new DOMDocument();
            $arquivo->load($resp["tmp_name"]);

            $linhas = $arquivo->getElementsByTagName("Row");
            $primeiralinha = true;
            foreach($linhas as $value){
                if($primeiralinha == false){
                $MLB = $value->getElementsByTagName("Data")->item(0)->nodeValue;
                $value->getElementsByTagName("Data")->item(1) == '' ? $sku = null : $sku = $value->getElementsByTagName("Data")->item(1)->nodeValue;
                        $sql = new Sql();
                        $consul = $sql->select("SELECT * FROM tb_mlclick WHERE mlb = :mlb",array(
                            ":mlb"=>$MLB
                        ));
                        if(count($consul) === 0){
                        $sql->select("INSERT INTO tb_mlclick VALUES(NULL, :mlb, :id_produto, NULL, NULL, NULL)",array(
                            ":mlb"=>$MLB,
                            ":id_produto"=>$sku
                        ));

                        }else{
                        $sql->select("UPDATE tb_mlclick SET mlb = :mlb, id_produto = :sku where mlb = :mlb",[
                            ":mlb"=>$MLB,
                            ":sku"=>$sku
                        ]);
                    }}
                    $primeiralinha = false;
                }  
                
                        }
 }

    public static function updateMlclick(){
            $curl = curl_init();
            $sql = new Sql();
            $consul = $sql->select("SELECT mlb FROM tb_mlclick");
            foreach($consul as $v){
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.mercadolibre.com/items?ids='.$v['mlb'].'&attributes={id,price,available_quantity,title,listing_type_id,status,id}',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer APP_USR-4353001852101296-060119-1e40bd343d214d6e66fe2bc0f03e5bd0-645921558',
                'Cookie: _d2id=1fc17c45-ba74-4760-8258-60917a5a6ec6-n'
            ),
            ));
            $response = curl_exec($curl);
            $resp = json_decode($response, true);
            foreach($resp as $value){ 

            $nome = $value["body"]["title"];
            $preco = $value["body"]["price"];
            $qtd = $value["body"]["available_quantity"];
            $tipo = $value["body"]["listing_type_id"] == "gold_special" ? "Clássico" : "Premium";
            $status = $value["body"]["status"] == "active" ? "Ativo" : "Inativo";

            if($consul){
                $sql->select("UPDATE tb_mlclick SET name = :name, preco = :preco, qtd = :qtd, tipo = :tipo, status = :status WHERE mlb = :mlb",array(
                    ":mlb"=>$v["mlb"],
                    ":name"=>$nome,
                    ":preco"=>$preco,
                    ":qtd"=>$qtd,
                    ":tipo"=>$tipo,
                    ":status"=>$status
            ));
            }
            //MLB1841289191
            
        }
      }
      curl_close($curl);

       
 }
            


}


    ?>