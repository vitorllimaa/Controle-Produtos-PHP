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
 
    public function getAllB2wStiloDash(){

        $sql = new Sql();

        return $sql->select("SELECT count(sku) from tb_b2w_stilo");

    } 

    public function getB2wClickDash(){

        $sql = new Sql();

        return $sql->select("SELECT count(*) from tb_b2w_Click a inner join tb_aton_estoque_Click b on a.sku = b.id_produto
        where a.estoque != b.estoque_aton");

 }
    
    public function getAllB2wClickDash(){

            $sql = new Sql();

            return $sql->select("SELECT count(sku) from tb_b2w_click");

 }

    public function getMagaluStiloDash(){

        $sql = new Sql();

        return $sql->select("SELECT count(*) from tb_magalu_stilo a inner join tb_aton_estoque_stilo b on a.sku = b.id_produto
        where a.estoque != b.estoque_aton");

 }

    public function getAllMagaluStiloDash(){
        $sql = new Sql();

        return $sql->select("SELECT count(sku) from tb_magalu_stilo");
}

    public function getMagaluClickDash(){

        $sql = new Sql();

        return $sql->select("SELECT count(*) from tb_magalu_click a inner join tb_aton_estoque_Click b on a.sku = b.id_produto
        where a.estoque != b.estoque_aton");

}

    public function getAllMagaluClickDash(){
        $sql = new Sql();

        return $sql->select("SELECT count(sku) from tb_magalu_click");

}

    public function getmlClickDash(){

        $sql = new Sql();

        return $sql->select("SELECT count(*) from tb_mlclick a inner join tb_aton_estoque_Click b on a.id_produto = b.id_produto
        where a.qtd != b.estoque_aton");

 }

    public function getAllmlClickDash(){
        $sql = new Sql();

        return $sql->select("SELECT count(mlb) from tb_mlclick");

    }

    public function teste(){
            $curl = curl_init();
            $sql = new Sql();
            $consul = $sql->select("SELECT mlb FROM tb_mlclick limit 1,1");
            foreach($consul as $v){
        
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.mercadolibre.com/items?ids=MLB1841289191',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer APP_USR-4353001852101296-052820-4f72493aabff9db0b46d3a05d2b9b1c6-645921558',
                'Cookie: _d2id=1fc17c45-ba74-4760-8258-60917a5a6ec6-n'
            ),
            ));

            $response = curl_exec($curl);

            $resp = json_decode($response, true);
            foreach($resp as $value){ 
                $sku = [];
                if(count($sku) != 0){
                    $r = $value["body"]["variations"];

                    if($value["body"]["seller_custom_field"] == null){
                        
                        if(!($value["body"]["variations"] == null)){
                            if($value["body"]["variations"][0]["seller_custom_field"] === null){

                                foreach($r as $values){
                                    $values = $values["attribute_combinations"];
                                foreach($values as $key){
                                    if($key["id"] == 'COLOR' || $key['id'] == 'STRUCTURE_COLOR'){
                                        if($key["value_id"] != null){
                                            $sku = ($key["value_id"]);
                                        }
                                        } 
                                        } 
                            }
                                
                            }else{
                                $sku = $value["body"]["variations"][0]["seller_custom_field"];
                            }
                            }else{
                                    $value = ($value["body"]["attributes"]);
                                        foreach($value as $values){
                                            if($values["id"] == 'SELLER_SKU'){
                                                $sku = $values["value_name"];
                                                            } 
                                                        }
                                                    }
                                }else{
                                $sku = $value["body"]["seller_custom_field"];
                            }               
            } 

            $nome = $value["body"]["title"];
            $preco = $value["body"]["price"];
            $qtd = $value["body"]["available_quantity"];
            if($consul && $sku){
                $sql->select("UPDATE tb_mlclick SET id_produto = :sku, name = :name, preco = :preco, qtd = :qtd WHERE mlb = :mlb",array(
                    ":mlb"=>$v["mlb"],
                    ":sku"=>$sku,
                    ":name"=>$nome,
                    ":preco"=>$preco,
                    ":qtd"=>$qtd
            ));
            }
            //MLB1841289191
            var_dump($qtd);
            
        }
      }
      curl_close($curl);

       
 }

}