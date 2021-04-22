<?php

namespace controle;

use Rain\Tpl;


class page{
    private $tpl;
    private $options = [];
    private $defaults = [
        "data"=>[]
    ];
    //primeiro a ser executado!
    public function __construct($opts = array()){
        $this->options = array_merge($this->defaults, $opts);

        $config = array(
            "tpl_dir"       => $_SERVER["DOCUMENT_ROOT"]."/views/",
            "cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
            "debug"         => false // set to false to improve the speed
           );

        Tpl::configure( $config );
        $this->tpl = new Tpl;

        $this->setData($this->options["data"]);
        //cabeçalho
        $this->tpl->draw("header");

    }
    private function setData($data = array()){
       
        foreach($data as $key => $value){
            $this->tpl->assign($key, $value);
        }

    }
    public function setTpl($name, $data = array(), $returnHTML = false){
        $this->setData($data);
        return $this->tpl->draw($name, $returnHTML);
         

    }
    //ultimo a ser executado!
    public function __destruct(){
        //roda pé
        $this->tpl->draw("footer");        
    }



}


?>