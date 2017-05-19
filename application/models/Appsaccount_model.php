<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Appsaccount_model extends CN_Model {
    //Management Vars
    var $appcta_app;
    var $appcta_cuenta_id;
    var $appcta_cta_id;
    var $appcta_paq_id;
    var $appcta_rfc;
    var $appcta_gig;
    var $appcta_f_vent;
    var $appcta_f_act;
    var $appcta_f_fin;
    var $appcta_f_caduc;    
    
    function __construct() {
        parent::__construct();
        $this->appcta_app = null;
        $this->appcta_cuenta_id = null;
        $this->appcta_paq_id = null;
        $this->appcta_rfc = null;
        $this->appcta_gig = null;
        $this->appcta_f_vent = null;        
        $this->appcta_f_act = null;
        $this->appcta_f_fin = null;
        $this->appcta_f_caduc = null;
        $this->table_prefix = "apa";
        $this->table_name = "appcta";
        $this->model_name = "Appsaccount_model";
    }
    
}

