<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Permission_model extends CN_Model {
    //Management Vars
    var $perm_priv_id;
    var $perm_rolc_id;

    
    function __construct() {
        parent::__construct();
        $this->perm_priv_id = null;
        $this->perm_rolc_id = null;
        $this->table_prefix = "per";
        $this->table_name = "perm";
        $this->model_name = "Permission_model";
    }
    
}

