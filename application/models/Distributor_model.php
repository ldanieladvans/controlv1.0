<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Distributor_model extends CN_Model {
    //Management Vars
    var $ditrib_nom;
    var $ditrib_rfc;
    var $ditrib_f_nac;
    var $ditrib_limitgig;
    var $ditrib_limitrfc;
    var $ditrib_tel;
    var $ditrib_correo;
    var $ditrib_sector;
    var $ditrib_nac;
    var $ditrib_sup;    
    var $ditrib_dom_id;
    
    function __construct() {
        parent::__construct();
        $this->ditrib_nom = null;
        $this->ditrib_rfc = null;
        $this->ditrib_f_nac = null;
        $this->ditrib_limitgig = null;
        $this->ditrib_limitrfc = null;
        $this->ditrib_tel = null;        
        $this->ditrib_correo = null;
        $this->ditrib_sector = null;
        $this->ditrib_nac = null;
        $this->ditrib_sup = null;
        $this->ditrib_dom_id = null;
        $this->table_prefix = "dis";
        $this->table_name = "distrib";
        $this->model_name = "Distributor_model";
    }
    
}

