<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class User_model extends CN_Model {
    //Management Vars
    var $usr_distrib_id;
    var $usrc_nom;
    var $usrc_nick;
    var $usrc_pwd;
    var $usrc_correo;
    var $usrc_tel;
    var $usrc_super;
    var $usrc_ult_acces;
    var $usrc_actpwd;
    
    function __construct() {
        parent::__construct();
        $this->usr_distrib_id = null;
        $this->usrc_nom = null;
        $this->usrc_nick = null;
        $this->usrc_pwd = null;
        $this->usrc_correo = null;
        $this->usrc_tel = null;        
        $this->usrc_super = null;
        $this->usrc_ult_acces = null;
        $this->usrc_actpwd = null;
        
        $this->table_prefix = "usr";
        $this->table_name = "usrc";
        $this->model_name = "User_model";
    }
    
}

