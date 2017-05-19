<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class CN_Controller extends CI_Controller {

    function __construct() {
        parent::__construct();
        date_default_timezone_set('America/Merida');
        $this->template = "";
        $this->module['name'] = "";
        $this->module['folder'] = "";
        $this->module['controller'] = "";
        $this->module['title'] = "";
        $this->module['title_new'] = "";
        $this->module['title_edit'] = "";
        $this->module['title_delete'] = "";
        $this->module['id_field'] = "id";
        $this->module['tabla'] = "";
        $this->module['prefix'] = "";
    }
    
    function switchLanguage($language = "") {
        $language = ($language != "") ? $language : "spanish";
        $this->session->set_userdata('site_lang', $language);
        redirect(base_url());
    }
    
    function isTipoCuenta($tipo) {
        return $this->session->id_tipocuenta == $tipo;
    }

    function isTipoUsuario($tipo) {
        return $this->session->id_tipousuario == $tipo;
    }

    function isPerfil($idPerfil) {
        return $this->session->id_perfil == $idPerfil;
    }

    function sin_permisos() {
        $informacion = array('state' => 'warning', 'message' => 'No tiene permisos suficientes');
        $this->session->set_flashdata("informacion", $informacion);
        $this->ajax_redirect();
    }

    function initSessionDeUsuario($username, $password) {
        if (empty($username)) {
            $username = $this->session->userdata('username');
        }
        if (empty($password)) {
            $password = $this->session->userdata('contrasena');
        }
        $a = $this->{$this->module['controller'] . '_model'}->initSessionDeUsuario($username, $password);
        if ($a['success']) {
            $this->session->set_userdata($a['config']);
        } else {
            $this->session->set_flashdata('usuario_incorrecto', $a['message']);
            redirect(base_url() . 'login', 'refresh');
        }
    }

    function isAppActiva($idCuenta = NULL, $idApp = NULL) {
        if (empty($idCuenta)) {
            $idCuenta = $this->session->userdata("id_cuenta");
        }
        if (empty($idApp)) {
            $idApp = $this->session->userdata("id_app");
        }
        $activo = $this->{$this->module['controller'] . '_model'}->isAppActiva($idCuenta, $idApp);
        if (intval($activo) == 0) {
            unset($_SESSION['apps'][ADVANS_NAMESPACE]);
            $this->session->set_flashdata("informacion", "No tiene acceso a esta aplicación");
            $this->ajax_redirect(ADVANS_CONTROL_URL);
        }
        return TRUE;
    }

    function ajax_redirect($url = NULL, $permanent = TRUE, $statusCode = 303) {
        if (empty($url)) {
            $url = base_url() . "Dashboard";
        }
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === "XMLHttpRequest") {
            echo json_encode(array("success" => false, "redirect" => $url));
        } else {
            if (!headers_sent()) {
                header('location: ' . $url, $permanent, $statusCode);
            } else {
                echo '<script>location.href="' . $url . '"</script>';
            }
        }
        exit(0);
    }

    function isLoggin($modal = FALSE) {
        if (!$this->session->logueado) {
            if ($modal == FALSE) {
                $this->ajax_redirect(base_url() . "login");
            } else {
                echo '<script>window.location.href="' . base_url() . 'login";</script>';
            }
        }
        return TRUE;
    }

    protected function _user_is_logged_in() {
        if (!$this->session->userdata("logueado")) {
            redirect(base_url() . "Login/");
        }
    }

    function _initialize() {
        $this->isLoggin();
        $this->module['url'] = base_url() . $this->module['controller'];
        $this->module['listado_url'] = $this->module['url'] . '/';
        $this->module['edit_url'] = $this->module['url'] . '/modificar';
        $this->module['delete_url'] = $this->module['url'] . '/eliminar';
        $this->module['new_url'] = $this->module['url'] . '/nuevo';
        $this->module['read_url'] = $this->module['url'] . '/leer';
    }

    function index() {
        $this->listado();
    }

    function visualizar($view = NULL, $data = array()) {
        if ($view == NULL) {
            $view = $this->module['name'] . "_view";
        }
        /*
         * Verificamos si existe el módulo en la carpeta de usuarios, en caso de que no EXISTA, verificamos
         * si existe en la seccion de administrador, de lo contrario, marcamos error.
         */
        $rutaUsuarios = implode(DIRECTORY_SEPARATOR, array("usuarios", $view . ".php"));
        $rutaDefault = implode(DIRECTORY_SEPARATOR, array($view . ".php"));
        $ruta = implode(DIRECTORY_SEPARATOR, array(realpath("."), "application", "views", ""));
        if (file_exists($ruta . $rutaUsuarios) && ($this->isTipoCuenta(ADVANS_CUENTA_EMPRESARIAL) || $this->isTipoCuenta(ADVANS_CUENTA_PROFESIONAL) || $this->isTipoCuenta(ADVANS_CUENTA_DEMO))) {
            $this->template = $this->parser->parse("usuarios/" . $view, $data, TRUE);
        } elseif (file_exists($ruta . $rutaDefault)) {
            $this->template = $this->parser->parse($view, $data, TRUE);
        } else {
            $data = array(
                'heading' => "Error 404",
                'message' => 'Ooops!!!<br>No se pudo encontrar la página solicitada.<br><br>Posibles causas:<br><ul><li>Es posible que la URL solicitada NO exista</li><li>Su perfil de usuario no posee los permisos necesarios.</li></ul>',
                'ruta' => $ruta,
                'session' => $this->session->userdata()
            );
            $this->template = $this->parser->parse("errors/html/error_404", $data, TRUE);
        }
        $this->load->view("panelcontrol_view");
    }

    function listado() {
        $config = array();
        $config["base_url"] = $this->module['url'];
        $config["total_rows"] = $this->{$this->module['controller'] . "_model"}->record_count();
        $config["per_page"] = 25;
        $config["uri_segment"] = 2;
        $choice = $config["total_rows"] / $config["per_page"];
        $config["num_links"] = floor($choice);

        //config for bootstrap pagination class integration
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="prev">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        $this->pagination->initialize($config);
        $page = ($this->uri->segment($config['uri_segment'], 0));
        $data['page'] = $page;
        $data['per_page'] = $config['per_page'];
        $data["registros"] = $this->{$this->module['controller'] . "_model"}->getResultados($config["per_page"], $page);
        $data["pagination"] = $this->pagination->create_links();
        $this->visualizar(NULL, $data);
    }

    function validaForm() {
        $this->form_validation->set_rules($this->rulesForm);
        foreach ($this->rulesForm as $rule) {
            $r[$rule['field']] = $this->input->post($rule['field']);
        }

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $result = array('success' => false);
            if ($this->form_validation->run() == FALSE) {
                foreach ($this->rulesForm as $rule) {
                    $result['errores'][$rule['field']] = form_error($rule['field']);
                }
            } else {
                $s = $this->_insert($r);
                if ($s['state'] == "success") {
                    $result['success'] = TRUE;
                    $result['data'] = $r;
                    $result['data'][$this->module['id_field']] = $s['data']['insert_id'];
                }
            }
        }
        echo json_encode($result);
    }

    function nuevo($data = array()) {
        $data['id'] = 0;
               
        if ($this->input->server('REQUEST_METHOD') === "POST") {
            $this->form_validation->set_rules($this->rulesForm);
            foreach ($this->rulesForm as $rule) {
                $r[$rule['field']] = $this->input->post($rule['field']);
            }
            if ($this->form_validation->run() == FALSE) {
                $data['r'] = $r;
            } else {
                $s = $this->_insert($r);
                if ($s['state'] == 'success') {
                    $this->session->set_flashdata("informacion", $s);
                    redirect(base_url() . $this->module['name'] . "/");
                }
            }
        }
        $data['accion'] = "nuevo";
        $this->visualizar($this->module['name'] . "_nuevo_view", $data);
    }

    function _insert($data) {
        return $this->{$this->module['controller'] . '_model'}->insert($data);
    }

    function modificar($id = null, $data = array()) {
        
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules($this->rulesForm);
            $id = $this->input->post($this->module['id_field']);
            
            
            
            foreach ($this->rulesForm as $rule) {
                $r[$rule['field']] = $this->input->post($rule['field']);
            }
            
            if ($this->form_validation->run() == FALSE) {
                $data['r'] = $r;
            } else {
                $s = $this->_update($id, $r);
                $this->session->set_flashdata("informacion", $s);
                if ($s['state'] == 'success') {
                    redirect(base_url() . $this->module['name'] . "/");
                }
            }
        }
        if (!$this->isTipoCuenta(ADVANS_CUENTA_ADMINISTRADOR)) {
            if (in_array('id_cuenta', $this->db->list_fields($this->module['tabla']))){
                $this->db->where("id_cuenta", $this->session->userdata("id_cuenta"));
            }
        }

        $res = $this->db->where($this->module['prefix'] . "." . $this->module['id_field'], $id)->get($this->module['tabla'] . " " . $this->module['prefix']);
        if ($res->num_rows() == 1) {
            $data['r'] = $res->row_array();
        } else {
            $this->session->set_flashdata("informacion", array('state' => 'warning', 'message' => 'El elemento que intentó modificar no existe'));
            redirect(base_url() . $this->module['controller']);
        }        
        $data['accion'] = "modificar";

        $this->visualizar($this->module['name'] . "_nuevo_view", $data);
    }

    function _update($id, $data) {
        return $this->{$this->module['controller'] . '_model'}->update($id, $data);
    }

    function eliminar($id, $data = array()) {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $id = $this->input->post("id");
            $s = $this->_delete($id);
            $this->session->set_flashdata("informacion", $s);
            if ($s['state'] == 'success') {
                redirect(base_url() . $this->module['controller'] . "/");
            }
        }
        if (!$this->isTipoCuenta(ADVANS_CUENTA_ADMINISTRADOR)) {
            if (in_array('id_cuenta', $this->db->list_fields($this->module['tabla']))){
                $this->db->where("id_cuenta", $this->session->userdata("id_cuenta"));
            }
        }

        $res = $this->db->where($this->module['prefix'] . "." . $this->module['id_field'], $id)->get($this->module['tabla'] . " " . $this->module['prefix']);
        
        if ($res->num_rows() == 1) {
            $data['r'] = $res->row_array();
        } else {
            $this->session->set_flashdata("informacion", array('state' => 'warning', 'message' => 'El elemento que intentó eliminar no existe'));
            redirect(base_url() . $this->module['controller']);
        }
        $this->visualizar("eliminar_view", $data);
    }

    function _delete($id) {
        return $this->{$this->module['controller'] . '_model'}->delete($id);
    }

    function nuevoEnModal($data = array()) {
        $data['id'] = 0;
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules($this->rulesForm);
            foreach ($this->rulesForm as $rule) {
                $r[$rule['field']] = $this->input->post($rule['field']);
            }
            if ($this->form_validation->run() == FALSE) {
                $data['r'] = $r;
            } else {
                $s = $this->_insert($r);
                if ($s['state'] == 'success') {
                    $this->session->set_flashdata("informacion", $s);
                    redirect(base_url() . $this->module['name'] . "/");
                }
            }
        }
        $data['accion'] = "nuevo";
        $this->load->view($this->module['name'] . "_nuevo_view", $data);
    }
    
}
