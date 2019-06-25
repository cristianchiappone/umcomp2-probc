<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Usuarios extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('usuarios_model');
        $this->load->library(array('ion_auth', 'form_validation'));
        $this->load->model('ion_auth_model');
        $this->grupos_permitidos = array('admin');
        $this->nav_route = 'usuarios';
    }

    public function listar($users_type = 'admin')
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'Usuario', 'data' => 'usuario', 'width' => 15),
                array('label' => 'Grupo', 'data' => 'grupo', 'width' => 10),
                array('label' => 'Email', 'data' => 'email', 'width' => 20),
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 15),
                array('label' => 'Apellido', 'data' => 'apellido', 'width' => 15),
                array('label' => 'Activo', 'data' => 'active', 'width' => 15),
                array('label' => '', 'data' => 'edit', 'width' => 10, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
            ),
            'table_id' => 'usuarios_table',
            'order' => array(array(2, 'desc')),
            'source_url' => "usuarios/listar_data/$users_type",
            'reuse_var' => true,
            'initComplete' => 'complete_usuarios_table',
            'footer' => true,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['users_type'] = $users_type;
        $data['title'] = TITLE.' - Usuarios';
        $this->load_template('usuarios/usuarios_listar', $data);
    }

    public function listar_data($users_type = null)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $users_type == null) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }
        $this->datatables
            ->select("users.id, users.username as usuario, users.email, users.first_name as nombre, users.last_name as apellido, (CASE WHEN users.active = 1 THEN 'Si' ELSE 'No' END) as active, groups.name as grupo")
            ->unset_column('id')
            ->from('users')
            ->join('users_groups', 'users_groups.user_id = users.id')
            ->join('groups', 'users_groups.group_id = groups.id')
            ->where('groups.name', $users_type);
        $this->datatables->add_column('edit', '<div class="btn-group">'
            .'<a href="usuarios/modal_ver/$1" data-remote="false" data-toggle="modal" data-target="#remote_modal"  class="btn btn-sm btn-default" title="Ver"><i class="fa fa-search"></i></a> '
            .'<a href="usuarios/modal_editar/$1" data-remote="false" data-toggle="modal" data-target="#remote_modal" class="btn btn-sm btn-warning" title="Editar"><i class="fa fa-pencil"></i></a> '
            .'<a href="usuarios/modal_baja/$1" data-remote="false" data-toggle="modal" data-target="#remote_modal" class="btn btn-sm btn-danger" title="dar de baja"><i class="fa fa-ban"></i></a>'
            .'</div>', 'id');

        echo $this->datatables->generate();
    }

    public function modal_ver($id = null)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
        }

        $usuario = $this->usuarios_model->get_one($id);
        if (empty($usuario)) {
            $this->modal_error('No se encontró el registro a ver', 'Registro no encontrado');
        }
        $usuario->password = '';
        $data['fields'] = $this->build_fields($this->usuarios_model->fields, $usuario, true);

        $data['usuario'] = $usuario;
        $data['txt_btn'] = '';
        $data['btn_color'] = 'btn-primary';
        $data['title'] = TITLE.' - Ver Usuario';
        $this->load->view('usuarios/usuarios_modal_abm', $data);
    }

    public function modal_editar($id = null)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == null || !ctype_digit($id)) {
            $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
        }
        $usuario = $this->usuarios_model->get_one($id);
        if (empty($usuario)) {
            $this->modal_error('No se encontró el registro a editar', 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->usuarios_model);
        if (isset($_POST) && !empty($_POST)) {
            if ($this->form_validation->run() === true) {
                $trans_ok = true;
                $trans_ok &= $this->usuarios_model->update(array(
                    'id' => $usuario->id,
                    'last_name' => $this->input->post('last_name'),
                    'first_name' => $this->input->post('first_name'),
                    'email' => $this->input->post('email'),
                ));
                if ($trans_ok) {
                    $this->session->set_flashdata('message', $this->usuarios_model->get_msg());
                    redirect('usuarios/listar', 'refresh');
                } else {
                    $this->session->set_flashdata('error', $this->usuarios_model->get_error());
                    redirect('usuarios/listar', 'refresh');
                }
            } else {
                $this->session->set_flashdata('error', validation_errors());
                redirect('usuarios/listar', 'refresh');
            }
        }
        $this->usuarios_model->fields['password']['required'] = true;
        $this->usuarios_model->fields['password']['readonly'] = true;
        $this->usuarios_model->fields['last_name']['required'] = true;
        $this->usuarios_model->fields['first_name']['required'] = true;
        $this->usuarios_model->fields['email']['required'] = true;
        $this->usuarios_model->fields['username']['required'] = true;
        $this->usuarios_model->fields['username']['readonly'] = true;
        $usuario->password = '';

        $data['fields'] = $this->build_fields($this->usuarios_model->fields, $usuario);
        $data['usuario'] = $usuario;
        $data['txt_btn'] = 'Editar';
        $data['btn_color'] = 'btn-primary';
        $data['title'] = TITLE.' - Editar usuario';
        $this->load->view('usuarios/usuarios_modal_abm', $data);
    }

    public function modal_agregar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
        }
        $this->set_model_validation_rules($this->usuarios_model);
        if (isset($_POST) && !empty($_POST)) {
            if (!empty($this->usuarios_model->get_user_data($this->input->post('email')))) {
                $this->session->set_flashdata('error', 'Usuario ya existente.');
                redirect('usuarios/listar', 'refresh');
            }
            if ($this->input->post('group') === '0') {
                $this->session->set_flashdata('error', 'No se reconoció el grupo seleccionado como válido.');
                redirect('usuarios/listar', 'refresh');
            }
            if ($this->form_validation->run() === true) {
                $this->db->trans_begin();
                $trans_ok = true;
                $trans_ok &= $this->usuarios_model->create(array(
                    'username' => $this->input->post('username'),
                    'email' => $this->input->post('email'),
                    'last_name' => $this->input->post('last_name'),
                    'first_name' => $this->input->post('first_name'),
                    'password' => $this->generate_password($this->input->post('password')),
                    'active' => '1',
                    'created_on' => time(),
                ), false);
                $usuario_id = $this->usuarios_model->get_row_id();
                $this->load->model('users_groups_model');
                $trans_ok &= $this->users_groups_model->create(array(
                    'user_id' => $usuario_id,
                    'group_id' => $this->input->post('group'),
                ), false);
                if ($this->db->trans_status() && $trans_ok) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->usuarios_model->get_msg());
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('error', $this->usuarios_model->get_error());
                }
                redirect('usuarios/listar', 'refresh');
            } else {
                $this->session->set_flashdata('error', validation_errors());
                redirect('usuarios/listar', 'refresh');
            }
        }
        $model = new stdClass();
        $model->fields = array(
            'group' => array('label' => 'Grupo', 'input_type' => 'combo', 'class' => 'selectize', 'id_name' => 'group_id', 'required' => true),
        );
        $this->usuarios_model->fields['password']['required'] = true;
        $this->usuarios_model->fields['last_name']['required'] = true;
        $this->usuarios_model->fields['first_name']['required'] = true;
        $this->usuarios_model->fields['email']['required'] = true;
        $this->usuarios_model->fields['username']['required'] = true;

        $this->load->model('groups_model');
        $array_groups = $this->get_array('groups', 'description', 'id', array(
            'where' => array('groups.id in(1,3)'),
            ), array(0 => '-- Seleccionar grupo --'));

        $model->fields['group']['array'] = $array_groups;
        $data['fields'] = $this->build_fields($this->usuarios_model->fields);
        $data['fields_m'] = $this->build_fields($model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['btn_color'] = 'btn-primary';
        $data['title'] = TITLE.' - Crear usuario';
        $this->load->view('usuarios/usuarios_modal_abm', $data);
    }

    private function generate_password($password)
    {
        $this->store_salt = $this->config->item('store_salt', 'ion_auth');
        $salt = $this->store_salt ? $this->ion_auth->salt() : false;

        return $this->ion_auth->hash_password($password, $salt);
    }

    public function modal_baja($id = null)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == null || !ctype_digit($id)) {
            $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
        }
        $usuario = $this->usuarios_model->get_one($id);
        if (empty($usuario)) {
            $this->modal_error('No se encontró el registro', 'Registro no encontrado');
        }
        $this->set_model_validation_rules($this->usuarios_model);
        if (isset($_POST) && !empty($_POST)) {
            if ($this->form_validation->run() === true) {
                $trans_ok = true;
                $trans_ok &= $this->usuarios_model->update(array(
                    'id' => $usuario->id,
                    'active' => '0',
                ));
                if ($trans_ok) {
                    $this->session->set_flashdata('message', $this->usuarios_model->get_msg());
                    redirect('usuarios/listar', 'refresh');
                } else {
                    $this->session->set_flashdata('error', $this->usuarios_model->get_error());
                    redirect('usuarios/listar', 'refresh');
                }
            } else {
                $this->session->set_flashdata('error', validation_errors());
                redirect('usuarios/listar', 'refresh');
            }
        }
        $data['usuario'] = $usuario;
        $usuario->password = '';
        $data['fields'] = $this->build_fields($this->usuarios_model->fields, $usuario, true);
        $data['txt_btn'] = 'Dar de baja';
        $data['btn_color'] = 'btn-danger';
        $data['title'] = TITLE.' - Dar de baja al usuario';
        $this->load->view('usuarios/usuarios_modal_abm', $data);
    }
}
/* End of file Cliente.php */
/* Location: ./application/controllers/Cliente.php */
