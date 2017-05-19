<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Package_model extends CN_Model {
    //Management Vars
    var $paq_nom;
    var $paq_rfc;
    var $paq_gig;
    
    function __construct() {
        parent::__construct();
        $this->paq_nom = null;
        $this->paq_rfc = null;
        $this->paq_gig = null;
        $this->table_prefix = "paq";
        $this->table_name = "paq";
        $this->model_name = "Package_model";
    }
    
}

