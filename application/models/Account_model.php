<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Account_model extends CN_Model {
    //Management Vars
    var $cta_cliente_id;
    var $cta_distrib_id;
    var $cta_nomserv;
    var $cta_num;
    var $cta_fecha;
    var $cta_nom_bd;
    var $cta_estado;
    
    function __construct() {
        parent::__construct();
        $this->cta_cliente_id = null;
        $this->cta_distrib_id = null;
        $this->cta_nomserv = null;
        $this->cta_num = null;
        $this->cta_fecha = null;
        $this->cta_nom_bd = null;        
        $this->cta_estado = null;
        $this->table_prefix = "acc";
        $this->table_name = "cta";
        $this->model_name = "Account_model";
    }
    
}

