<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Domicile_model extends CN_Model {
    //Management Vars
    var $dom_calle;
    var $dom_numext;
    var $dom_numint;
    var $dom_col;
    var $dom_ciudad;
    var $dom_munic;
    var $dom_estado;
    var $dom_pais;
    
    function __construct() {
        parent::__construct();
        $this->dom_calle = null;
        $this->dom_numext = null;
        $this->dom_numint = null;
        $this->dom_col = null;
        $this->dom_ciudad = null;
        $this->dom_munic = null;        
        $this->dom_estado = null;
        $this->dom_pais = null;
        $this->table_prefix = "dom";
        $this->table_name = "dom";
        $this->model_name = "Domicile_model";
    }
    
}

