<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SessionData {
    var $CI;

    function __construct(){
        $this->CI =& get_instance();
        if(!isset($this->CI->session)){
          $this->CI->load->library('session');
        }
    }
    
    function url_origin( $s, $use_forwarded_host = false ){
        $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
        $sp       = strtolower( $s['SERVER_PROTOCOL'] );
        $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
        $port     = $s['SERVER_PORT'];
        $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
        $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
        $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
        return $protocol . '://' . $host;
    }

    function full_url( $s, $use_forwarded_host = false ){
        return $this->url_origin( $s, $use_forwarded_host ) . $s['REQUEST_URI'];
    }

    function initializeData() {
          if(!$this->CI->session->userdata('logged_in')){
              if(!$this->CI->session->userdata('dont_redirect')){
                  redirect($this->full_url($_SERVER)."login/", 'refresh');
              }else{
                  $this->CI->session->unset_userdata('dont_redirect');
              }
                
          }else{
            $data['user'] = $this->CI->session->userdata('logged_in');
          }
    }
}