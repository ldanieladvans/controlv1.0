<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Binnacle_model extends CI_Model {
    
    var $id;
    
    function __construct($bitc_result,$bitc_msj,$bitc_dat,$bitc_modulo=FALSE,$bitc_tipo_op=FALSE) {
        parent::__construct();
        date_default_timezone_set('America/Merida');
        $this->bitc_usrc_id = $this->session->userdata('user_id');
        $this->bitc_fecha = date("d-m-Y H:i:s");
        $this->bitc_tipo_op = $bitc_tipo_op ? $bitc_tipo_op:"create";
        $this->bitc_ip = $this->getUserIP();
        $this->bitc_naveg = $this->getBrowser();
        $this->bitc_modulo = $bitc_modulo ? $bitc_modulo:"control";;
        $this->bitc_result = $bitc_result;
        $this->bitc_msj = $bitc_msj;
        $this->bitc_dat = $bitc_dat;
        
        $this->table_name = "bitc";
        $this->table_prefix = "bit";
        $this->id_field = "id";
        $this->model_name = "Binnacle_model";
    }

    function insert($data) {
        $this->db->insert($this->table_name, $data);
    }

    private function getUserIP() {
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }

    private function getBrowser() {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
                ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
        $i = count($matches['browser']);
        if ($i != 1) {
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }
        if ($version == null || $version == "") {
            $version = "?";
        }
        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern
        );
    }

    function gadget() {
        $idCuenta = $this->session->id_cuenta;
        $this->db
                ->select("accion, mensaje, " . $this->table_prefix . ".fecha_creacion")
                ->join("usuarios u", "u.id_usuario = " . $this->table_prefix . ".id_usuario", "INNER")->select("u.id_usuario, u.id_tipousuario, u.id_perfil")
                ->where($this->table_prefix . ".id_cuenta", $idCuenta)
                ->where($this->table_prefix . '.id_usuario >=', $this->session->id_tipousuario)
                ->order_by("fecha_creacion", "DESC")
                ->limit(10);
        $bitacora = $this->db->get($this->table_name . " " . $this->table_prefix);
        if ($bitacora->num_rows() > 0) {
            if ($this->session->id_tipocuenta == ADVANS_CUENTA_ADMINISTRADOR) {
                $select = '<div class="selectRow"><input type="hidden" class="" value="2" name="input" id="select2" style="width:100%;"></div><br>';
            } else {
                $select = "";
            }
            $html = '<h3>Bitacora de eventos en Panel de Control</h3>'
                    . $select
                    . $this->crearHTMLBitacoraApartirDelArray($bitacora->result_array());
            echo $html;
        }
    }

    function crearHTMLBitacoraApartirDelArray($array) {
        $html = '<div class="list-group" id="gadgetBitacora" style="overflow-y:scroll;height: 350px;">';
        foreach ($array as $b) {
            $class = array();
            switch (trim($b['accion'])) {
                case "Iniciar sesión":
                    array_push($class, "list-group-item-success");
                    break;
                case "Cerrar sesión":
                    array_push($class, "list-group-item-danger");
                    break;
            }

            $html .= '<a href="#" class="list-group-item ' . implode(" ", $class) . '" style="cursor:default;">'
                    . ($this->session->id_tipocuenta == ADVANS_CUENTA_ADMINISTRADOR ? '<span class="badge">' . $b['accion'] . '</span>' : '')
                    . '<h4 class="list-group-item-heading">' . mysqlDate2Date($b['fecha_creacion'], FALSE) . '</h4>'
                    . '<p class="list-group-item-text">' . $b['mensaje'] . '</p>'
                    . '</a>';
        }
        $html .= '</div>';
        return $html;
    }
    
    function getGadgets(){
        $this->gadget();
    }

}


