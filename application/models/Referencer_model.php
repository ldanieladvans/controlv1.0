<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Referencer_model extends CN_Model {
    //Management Vars
    var $refer_nom;
    var $refer_rfc;
    
    function __construct() {
        parent::__construct();
        $this->refer_nom = null;
        $this->refer_rfc = null;
        $this->table_prefix = "ref";
        $this->table_name = "refer";
        $this->model_name = "Referencer_model";
    }
    
}

