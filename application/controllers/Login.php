<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!isset($this->form_validation)){
            $this->load->library(array('form_validation'));
        }
        $this->session->set_userdata('dont_redirect', TRUE);
    }

    public function index($data = array()){
        $data['token'] = $this->token();
        $data['titulo'] = $this->lang->line('session_init');
        if(!$this->session->userdata('success_login')){
            $this->load->view('login_view', $data);
        }else{
            $this->session->unset_userdata('success_login');
            redirect(base_url() . "/".strtolower($this->router->default_controller)."/");
        }
        
    }

    function iniciar_sesion(){
        /*echo '<pre>';
        print_r('aqui');die();
        echo '</pre>';*/
        $this->form_validation->set_rules('rfc', "RFC", 'required|trim|xss_clean');
        $this->form_validation->set_rules('usuario', 'Nombre de usuario', 'required|trim|min_length[2]|max_length[150]|xss_clean');
        $this->form_validation->set_rules('contrasena', 'Contrasena', 'required|trim|min_length[4]|max_length[150]|xss_clean');
        $r['usuario'] = $this->input->post('usuario');
        $r['numero_cuenta'] = $this->input->post("numero_cuenta");
        if ($this->form_validation->run() == FALSE) {
            $data['r'] = $r;
            $this->index($data);
        } else {
            $numeroCuenta = $this->input->post('rfc');
            $username = $this->input->post('usuario');
            $password = md5($this->input->post('contrasena'));
            $check_user = $this->login_user($username, $password, $numeroCuenta);
            if ($check_user == TRUE) {
                $this->db->set("ultimo_acceso", ahora())->where("id_usuario", $this->session->userdata("id_usuario"))->update("usuarios");
                $this->session->set_userdata('success_login', TRUE);
                $this->index();
            }
        }
    }

    public function token() {
        $token = md5(uniqid(rand(), true));
        $this->session->set_userdata('token', $token);
        return $token;
    }

    public function cerrar_sesion() {
        date_default_timezone_set('America/Merida');
        $this->Login_model->cerrar_sesion();
        $this->db->set("ultimo_acceso", ahora())->where("id_usuario", $this->session->userdata("id_usuario"))->update("usuarios");
        $this->session->sess_destroy();
        $this->index();
    }

    function error404() {
        redirect("Panelcontrol/");
    }

    function recuperarContrasena() {
        if ($this->input->post('token') && $this->input->post('token') == $this->session->userdata('token')) {
             $this->form_validation->set_rules('numero_cuenta', "Número de cuenta", 'required|trim|xss_clean');

            $r['numero_cuenta'] = $this->input->post('numero_cuenta');
            if ($this->form_validation->run() == TRUE) {
                $username = $this->input->post('numero_cuenta');
                $reset = $this->Login_model->resetContrasena($username);
                $this->session->set_flashdata('informacion', $reset['message']);
            }
        }
        $data['token'] = $this->token();
        $data['titulo'] = 'Recuperar contraseña';
        $this->load->view("recuperar_contrasena_view", $data);
    }

    function reestablecerContrasena($base64) {
       if ($this->input->post('token') && $this->input->post('token') == $this->session->userdata('token')) {
            $this->form_validation->set_rules('contrasena', 'Nueva contraseña', 'required|trim|min_length[8]|max_length[50]');
            $this->form_validation->set_rules('contrasena_r', 'Repite contraseña', 'required|trim|min_length[8]|max_length[50]|matches[contrasena]');
            $this->form_validation->set_rules('id_usuario', 'ID usuario', 'required|trim');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('reestablecer', "Ocurrió un error al actualizar su contraseña. Intente de nuevo.");
            } else {
                $url = base64_decode($base64);
                parse_str($url, $param);
                $this->db->delete('reset_passwords_time', $param);
                $idUsuario = $this->input->post('id_usuario');
                $pass = $this->input->post('contrasena');
                $this->db->where("id_usuario", $idUsuario)->update("usuarios", array('contrasena' => md5($pass)));
                $this->Bitacora_model->insert(array(
                    'tabla' => NULL,
                    'accion' => "Recuperar contraseña",
                    'modulo' => 'Login',
                    'data' => json_encode(array("id_usuario" => $idUsuario, "contrasena" => md5($pass))),
                    'result' => 'success',
                    'mensaje' => sprintf("El usuario %d olvidó su contraseña y la ha restaurado.", $idUsuario)
                ));
                $this->session->set_flashdata('informacion', "Su contraseña ha sido actualizada exitosamente.");
                $this->session->set_flashdata('class', 'success');
                redirect(base_url() . "Login");
            }
        }
        $url = base64_decode($base64);
        parse_str($url, $param);
        $data['token'] = $this->token();
        $data['titulo'] = 'Reestablecer contraseña';
        $data['base64'] = $base64;
        $data['id_usuario'] = $param['id_usuario'];
        $this->load->view("reestablecer_contrasena_view", $data);
    }

    function activarCuenta($base64 = NULL) {
        $url = base64_decode($base64);
        parse_str($url, $param);
        $idCuenta = $param['id_cuenta'];
        unset($param['id_cuenta']);
        $r = $this->db->where($param)->limit(1)->get('reset_passwords_time');
        if ($r->num_rows() == 1) {
            $data = $r->row_array();
            $ahora = new DateTime(ahora());
            $momento = new DateTime($data['time']);
            $delta = date_diff($ahora, $momento);
            $seconds = ($delta->s) + ($delta->i * 60) + ($delta->h * 60 * 60) + ($delta->d * 60 * 60 * 24) + ($delta->m * 60 * 60 * 24 * 30) + ($delta->y * 60 * 60 * 24 * 365);
            if ($seconds < 1800) { // 1800 = 60 segundos * 30 minutos
                $this->db->set("activo", 1)->where("id_usuario", $param['id_usuario'])->update("usuarios");
                $this->db->set("activo", 1)->where("id_cuenta", $idCuenta)->update("cuentas");
                $this->session->set_flashdata("informacion", "Su cuenta ha sido activada.");
                $this->session->set_flashdata("class", "success");
            } else {
                $this->session->set_flashdata("informacion", "El tiempo para activar su cuenta ha expirado.");
            }
            $this->db->where("id_usuario", $param['id_usuario'])->delete("reset_passwords_time");
        } else {
            $this->session->set_flashdata("informacion", "Esta llave ha sido usada o ha expirado.");
        }
        redirect(base_url() . "Login");
    }
    
    public function login_user($username, $password, $numeroCuenta) {
        $result = $this->initSessionDeUsuario($username, $password, $numeroCuenta);
        if (!$result['success']) {
            $this->Bitacora_model->insert(array(
                'id_cuenta' => NULL,
                'id_empresa' => NULL,
                'id_usuario' => NULL,
                'tabla' => NULL,
                'accion' => "Iniciar sesión",
                'modulo' => $this->model_name,
                'data' => json_encode(array("usuario" => $username, "contrasena" => $password)),
                'result' => json_encode($result),
                'mensaje' => sprintf("El usuario %s falló al iniciar sesión", $username)
            ));
            $this->session->set_flashdata('informacion', $result['message']);
            redirect(base_url() . 'Login');
        }
        $this->session->set_userdata($result['config']);
        $this->Bitacora_model->insert(array(
            'tabla' => NULL,
            'accion' => "Iniciar sesión",
            'modulo' => $this->model_name,
            'data' => json_encode(array("usuario" => $username, "contrasena" => $password)),
            'result' => json_encode($result),
            'mensaje' => sprintf("El usuario %s inició sesión", $username)
        ));
        return TRUE;
    }

    function cerrar_sesion_bitc() {
        $this->Bitacora_model->insert(array(
            'tabla' => NULL,
            'accion' => "Cerrar sesión",
            'modulo' => $this->model_name,
            'data' => NULL,
            'result' => NULL,
            'mensaje' => sprintf("El usuario %s finalizó sesión", $this->session->username)
        ));
    }

    function resetContrasena($user) {
        if (!empty($user)) {
            $idcuenta = $this->db->select("id_cuenta")->like("numero_cuenta",$user)->limit(1)->get("cuentas");
            if($idcuenta->num_rows() == 1){
                $idcuentas =$idcuenta->row_array();
                $id_cuenta = $idcuentas['id_cuenta'];
            }
            $r = $this->db->select("id_usuario,username")->like("id_cuenta", $id_cuenta)->limit(1)->get("usuarios");
            if ($r->num_rows() == 1) {
                $usuario = $r->row_array();
                $correo = $usuario['username'];
                $pass = randomPasswordLetras(40);
                $idUsuario = $usuario['id_usuario'];
                $data = array(
                    'id_usuario' => $usuario['id_usuario'],
                    'time' => ahora(),
                    'llave' => $pass
                );
                $this->db->insert("reset_passwords_time", $data);
                $this->load->library("email");
                $this->email->initialize(array(
                    'protocol' => 'smtp',
                    'smtp_host' => 'ssl://smtp.gmail.com',
                    'smtp_user' => 'app@solucionesadvans.com',
                    'smtp_pass' => 'soulax69xxx',
                    'smtp_port' => 465,
                    'mailtype' => 'html',
                    'charset' => 'utf-8',
                    'crlf' => "\r\n",
                    'newline' => "\r\n"
                ));
                $this->email->from("boveda@advans.mx", "Bóveda Soluciones Advans", "soporte@advans.mx");
                $this->email->to($correo);
                $this->email->subject("Recuperación de contraseña. Bóveda Soluciones Advans");
                $hex64 = substr(base64_encode("id_usuario=" . $usuario['id_usuario'] . "&llave=" . $pass), 0, -1);
                $data = array(
                    'url_reset_passrword' => base_url() . "Login/reestablecerContrasena/" . $hex64,
                    'nombre' => $correo
                );
                $html = $this->parser->parse("recuperar_contrasena_template", $data, TRUE);
                $this->email->message($html);
                if ($this->email->send()) {
                    return array('success' => TRUE, 'message' => 'Se le ha enviado un correo electrónico.');
                } else {
                    return array('success' => FALSE, 'message' => 'No se pudo enviar el correo');
                }
            } else {
                return array('success' => FALSE, 'message' => 'No se encontró ninguna cuenta con ese correo.');
            }
        }
        return array('success' => FALSE, 'message' => 'Faltó escribir el correo electrónico');
    }

}


