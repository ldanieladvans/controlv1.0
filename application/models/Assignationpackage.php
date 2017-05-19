<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Assignationpackage_model extends CN_Model {
    //Management Vars
    var $asigpaq_distrib_id;
    var $asigpaq_paq_id;
    var $asigpaq_rfc;
    var $asigpaq_gig;
    var $asigpaq_f_vent;
    var $asigpaq_f_act;
    var $asigpaq_f_fin;
    var $asigpaq_f_caduc;
    
    function __construct() {
        parent::__construct();
        $this->asigpaq_distrib_id = null;
        $this->asigpaq_paq_id = null;
        $this->asigpaq_rfc = null;
        $this->asigpaq_gig = null;
        $this->asigpaq_f_vent = null;
        $this->asigpaq_f_act = null;        
        $this->asigpaq_f_fin = null;
        $this->asigpaq_f_caduc = null;
        $this->table_prefix = "asp";
        $this->table_name = "asigpaq";
        $this->model_name = "Assignationpackage_model";
    }
    
}

