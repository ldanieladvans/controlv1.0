<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Privilege_model extends CN_Model {
    //Management Vars
    var $priv_nom;
    var $priv_desc;

    
    function __construct() {
        parent::__construct();
        $this->priv_nom = null;
        $this->priv_desc = null;
        $this->table_prefix = "pri";
        $this->table_name = "priv";
        $this->model_name = "Privilege_model";
    }
    
}

