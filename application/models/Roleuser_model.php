<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Roleuser_model extends CN_Model {
    //Management Vars
    var $rolusr_usrc_id;
    var $rolusr_rolc_id;

    
    function __construct() {
        parent::__construct();
        $this->rolusr_usrc_id = null;
        $this->rolusr_rolc_id = null;
        $this->table_prefix = "rus";
        $this->table_name = "rolusr";
        $this->model_name = "Roleuser_model";
    }
    
}

