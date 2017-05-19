<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class CN_Model extends CI_Model {
    //Management Vars
    var $id;
    var $active;
    var $create_user;
    var $write_user;
    var $create_date;
    var $write_date;
    //Meta Vars
    var $table_prefix;
    var $table_name;
    var $id_field;
    var $model_name;
    var $database;
    
    
    function __construct() {
        parent::__construct();
        date_default_timezone_set('America/Merida');
        $this->id = null;
        $this->active = TRUE;
        $this->create_user = null;
        $this->write_user = null;
        $this->create_date = null;
        $this->write_date = null;
        $this->table_prefix = "";
        $this->table_name = "";
        $this->id_field = "id";
        $this->model_name = "";
    }
    
    function baseCrud($db=null, $user_id=null){
        if(!$db){
            $db=$this->db;
        }
        if(!$user_id){
            $user_id=$this->session->userdata('user_id') ? $this->session->userdata('user_id'):null;
        }
        $this->database = $db;
        return $user_id;
    }
    
    function baseCreate($db=null, $user_id=null){
        $base_uid = $this->baseCrud($db, $user_id);
        $this->create_user = $user_id;
        $this->create_date = date($this->config->item('log_date_format'), time());
    }
    
    function baseWrite($db=null, $user_id=null){
        $base_uid = $this->baseCrud($db, $user_id);
        $this->write_user = $user_id;
        $this->write_date = date($this->config->item('log_date_format'), time());
    }
    
    function create($vals, $db=null, $user_id=null){
        $this->baseCreate($db, $user_id);
        try{
            if(is_array($vals)){
                $vals['create_user'] = $this->create_user;
                $vals['create_date'] = $this->create_date;
                return $this->{$this->database}->insert($vals);
            }else{
                throw new Exception();
            }
        }
        catch(Exception $e){
            show_error($this->lang->line('error_var_type'), 406, $this->lang->line('error_encountered'));
        }       
    }
    
    function read($show_fields=[], $domain_fields=[], $limit=null, $db=null, $user_id=null){
        $domain_fields_str = "";
        $show_fields_str = "";
        $counter_domain_fields = 0;
        $counter_show_fields = 0;
        
        //Query template
        $query_template_str = "";
        
        try{
            if(!is_array($show_fields) OR !is_array($domain_fields)){
                throw new Exception();
            }else{
                //Building select syntax
                if (count($show_fields) > 0){
                    foreach($show_fields as $sf){
                        if ($counter_show_fields==0){
                            $show_fields_str = str($sf);
                        }else{
                            $show_fields_str .= ",".str($sf);
                        }
                    $counter_show_fields++;
                    } 
                }else{
                    $show_fields_str = "*";
                }

                //Adding select syntax to query template
                $query_template_str = "{\$this->database}->select('\"$show_fields_str\"')";

                //Building where syntax
                if (count($domain_fields) > 0){
                    foreach($domain_fields as $key => $value){
                        if ($counter_domain_fields==0){
                            $domain_fields_str = "->where('".$key."',".$value.")";
                        }else{
                            $domain_fields_str .= ","."->where('".$key."',".$value.")";
                        }
                    $counter_domain_fields++;
                    }
                }

                //Adding where syntax to query template
                $query_template_str .= $domain_fields_str;

                //Adding limit
                if($limit){
                    $query_template_str .= "->limit(".$limit.")";
                }

                //Adding table
                $query_template_str .= "->get(".$this->table_name.")";

                return eval($query_template_str);
            }
            
        }catch(Exception $e){
            show_error($this->lang->line('error_var_type'), 406, $this->lang->line('error_encountered'));
        }       
    }
    
    function update($vals, $domain_fields=[], $db=null, $user_id=null){
        $this->baseWrite($db, $user_id);
        try{
            if(is_array($vals)){
                $vals['write_user'] = $this->write_user;
                $vals['write_date'] = $this->write_date;
                $domain_fields_str = "";
                $counter_domain_fields = 0;

                //Query template
                $query_template_str = "";

                //Adding select syntax to query template
                $query_template_str = "{\$this->database}->set(".$vals.")";

                //Building where syntax
                if (count($domain_fields) > 0){
                    foreach($domain_fields as $key => $value){
                        if ($counter_domain_fields==0){
                            $domain_fields_str = "->where('".$key."',".$value.")";
                        }else{
                            $domain_fields_str .= ","."->where('".$key."',".$value.")";
                        }
                    $counter_domain_fields++;
                    }
                }

                //Adding where syntax to query template
                $query_template_str .= $domain_fields_str;
                
                //Adding table;
                return $query_template_str .= "->update(".$this->table_name.")";
            }else{
                throw new Exception();
            }
        }
        catch(Exception $e){
            show_error($this->lang->line('error_var_type'), 406, $this->lang->line('error_encountered'));
        }       
    }
    
    function delete($domain_fields=[], $db=null, $user_id=null){
        $user_id = $this->baseCrud($db, $user_id);
        try{
            if(is_array($domain_fields)){
                $domain_fields_str = "";
                $counter_domain_fields = 0;

                //Query template
                $query_template_str = "";

                //Adding select syntax to query template
                $query_template_str = "{\$this->database}";

                //Building where syntax
                if (count($domain_fields) > 0){
                    foreach($domain_fields as $key => $value){
                        if ($counter_domain_fields==0){
                            $domain_fields_str = "->where('".$key."',".$value.")";
                        }else{
                            $domain_fields_str .= ","."->where('".$key."',".$value.")";
                        }
                    $counter_domain_fields++;
                    }
                }

                //Adding where syntax to query template
                $query_template_str .= $domain_fields_str;
                
                //Adding table;
                return $query_template_str .= "->delete(".$this->table_name.")";
            }else{
                throw new Exception();
            }
        }
        catch(Exception $e){
            show_error($this->lang->line('error_var_type'), 406, $this->lang->line('error_encountered'));
        }       
    }
    
}

