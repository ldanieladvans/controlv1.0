<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Role_model extends CN_Model {
    //Management Vars
    var $rol_nom;
    var $rol_advans;

    
    function __construct() {
        parent::__construct();
        $this->rol_nom = null;
        $this->rol_advans = null;
        $this->table_prefix = "rol";
        $this->table_name = "rolc";
        $this->model_name = "Role_model";
    }
    
}

