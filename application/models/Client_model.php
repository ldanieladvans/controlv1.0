<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Client_model extends CN_Model {
    //Management Vars
    var $cliente_refer_id;
    var $cliente_nom;
    var $cliente_sexo;
    var $cliente_f_nac;
    var $cliente_rfc;
    var $cliente_tipo;
    var $cliente_tel;
    var $cliente_correo;
    var $cliente_nac;
    var $cliente_sector;
    var $cliente_dom_id;
    
    function __construct() {
        parent::__construct();
        $this->cliente_refer_id = null;
        $this->cliente_nom = null;
        $this->cliente_sexo = null;
        $this->cliente_f_nac = null;
        $this->cliente_rfc = null;
        $this->cliente_tipo = null;        
        $this->cliente_tel = null;
        $this->cliente_correo = null;
        $this->cliente_nac = null;
        $this->cliente_sector = null;
        $this->cliente_dom_id = null;
        $this->table_prefix = "cli";
        $this->table_name = "cliente";
        $this->model_name = "Client_model";
    }
    
}

