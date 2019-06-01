<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dispositivo extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('dispositivo_model');
        $this->grupos_permitidos = array('admin');
        $this->nav_route = 'dispositivo';
    }

    public function listar() {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => '#', 'data' => 'id', 'width' => 15),
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 30),
                array('label' => 'Usuario', 'data' => 'usuario', 'width' => 25),
                array('label' => 'Fecha Alta', 'data' => 'fecha_alta', 'render' => 'datetime', 'width' => 10),
                array('label' => 'Activo', 'data' => 'activo', 'width' => 10),
                array('label' => '', 'data' => 'edit', 'width' => 10, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
            ),
            'table_id' => 'dispositivo_table',
            'order' => array(array(0, 'desc')),
            'source_url' => 'dispositivo/listar_data/',
            'reuse_var' => true,
            'initComplete' => 'complete_dispositivo_table',
            'footer' => true,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title'] = TITLE . ' - Dispositivos';
        $this->load_template('dispositivo/dispositivo_listar', $data);
    }

    public function listar_data() {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }
        $this->datatables
                ->select("dispositivo.id, dispositivo.descripcion as nombre, dispositivo.fecha_alta, (CASE WHEN dispositivo.activo = 1 THEN 'Si' ELSE 'No' END) as activo, concat(username, '(', first_name, ' ', last_name, ')') as usuario")
                ->unset_column('id')
                ->from('dispositivo')
                ->join('usuario_dispositivo', 'usuario_dispositivo.dispositivo_id = dispositivo.id')
                ->join('users', 'users.id = usuario_dispositivo.usuario_id');

        $this->datatables->add_column('edit', ''
                . '<div class="btn-group" role="group">'
                . '<a class="btn btn-xs btn-default" href="dispositivo/ver/$1" title="Ver"><i class="fa fa-search"></i> Ver</a>'
                . '<button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>'
                . '<ul class="dropdown-menu dropdown-menu-right">'
                . '<li><a class="dropdown-item" href="dispositivo/modal_editar/$1"data-remote="false" data-toggle="modal" data-target="#remote_modal" title="Editar"><i class="fa fa-pencil"></i> Editar</a></li>'
                . '<li><a class="dropdown-item" href="dispositivo/modal_baja/$1"data-remote="false" data-toggle="modal" data-target="#remote_modal" title="Dar de baja"><i class="fa fa-remove"></i> Dar de baja</a></li>'
                . '</ul></div>', 'id');

        echo $this->datatables->generate();
    }

    public function ver($id = null) {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
        }

        $dispositivo = $this->dispositivo_model->get_one($id);
        if (empty($dispositivo)) {
            $this->modal_error('No se encontró el registro a ver', 'Registro no encontrado');
        }

        $tableData = array(
            'columns' => array(
                array('label' => '#', 'data' => 'id', 'width' => 5),
                array('label' => 'Fecha', 'data' => 'fecha_medicion', 'render' => 'datetime', 'width' => 30),
                array('label' => 'Mediciones', 'data' => 'mediciones', 'width' => 65, 'searchable' => 'false'),
            ),
            'table_id' => 'dispositivo_medicion_table',
            'order' => array(array(0, 'asc')),
            'source_url' => "dispositivo/listar_data_medicion/$dispositivo->id",
            'reuse_var' => true,
            'initComplete' => 'complete_dispositivo_medicion_table',
            'footer' => true,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);

        $model = new stdClass();
        $model->fields = array(
            'tipos_medicion' => array('label' => 'Tipos de Medición', 'readonly' => true),
        );
        $model->fields['tipos_medicion']['value'] = $dispositivo->tipos_medicion;

        $mediciones = $this->dispositivo_model->get_mediciones($dispositivo->id);
        $data['mediciones'] = (!empty($mediciones) ? $mediciones : '');
        $data['fields'] = $this->build_fields($this->dispositivo_model->fields, $dispositivo, true);
        $data['fields_m'] = $this->build_fields($model->fields);
        $data['dispositivo'] = $dispositivo;
        $data['txt_btn'] = '';
        $data['btn_color'] = 'btn-primary';
        $data['title'] = TITLE . ' - Ver Dispositivo';
        $this->load_template('dispositivo/ver_dispositivo', $data);
    }

    public function listar_data_medicion($dispositivo_id) {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $dispositivo_id == null || !ctype_digit($dispositivo_id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }
        $this->datatables
                ->select("medicion.id, medicion.fecha as fecha_medicion, group_concat(concat('<strong>', tipo_medicion.descripcion, ': </strong>', medicion_valor.valor) separator ' - &nbsp') as mediciones")
                ->unset_column('id')
                ->from('dispositivo')
                ->join('medicion', 'medicion.dispositivo_id=dispositivo.id')
                ->join('medicion_valor', 'medicion_valor.medicion_id=medicion.id')
                ->join('tipo_medicion', 'tipo_medicion.id=medicion_valor.tipo_medicion_id')
                ->where('dispositivo.id', $dispositivo_id)
                ->group_by('medicion.id');
        $this->datatables->add_column('edit', '', 'id');

        echo $this->datatables->generate();
    }

    public function modal_editar($id = null) {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == null || !ctype_digit($id)) {
            $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
        }
        $dispositivo = $this->dispositivo_model->get_one($id);
        if (empty($dispositivo)) {
            $this->modal_error('No se encontró el registro a editar', 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->dispositivo_model);
        if (isset($_POST) && !empty($_POST)) {
            if ($id !== $this->input->post('id')) {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }
            if ($this->form_validation->run() === true) {
                $trans_ok = true;
                $trans_ok &= $this->dispositivo_model->update(array(
                    'id' => $dispositivo->id,
                    'descripcion' => $this->input->post('descripcion'),
                    'activo' => '1',
                ));
                if ($trans_ok) {
                    $this->session->set_flashdata('message', $this->dispositivo_model->get_msg());
                    redirect('dispositivo/listar', 'refresh');
                } else {
                    $this->session->set_flashdata('error', $this->dispositivo_model->get_error());
                    redirect('dispositivo/listar', 'refresh');
                }
            } else {
                $this->session->set_flashdata('error', validation_errors());
                redirect('dispositivo/listar', 'refresh');
            }
        }
        $data['fields'] = $this->build_fields($this->dispositivo_model->fields, $dispositivo);
        $data['dispositivo'] = $dispositivo;
        $data['txt_btn'] = 'Editar';
        $data['btn_color'] = 'btn-primary';
        $data['title'] = TITLE . ' - Editar dispositivo';
        $this->load->view('dispositivo/dispositivo_modal_abm', $data);
    }

    public function modal_agregar() {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
        }

        $this->set_model_validation_rules($this->dispositivo_model);
        if (isset($_POST) && !empty($_POST)) {
            if ($this->form_validation->run() === true) {
                $this->db->trans_begin();
                $trans_ok = true;
                $trans_ok &= $this->dispositivo_model->create(array(
                    'descripcion' => $this->input->post('descripcion'),
                    'fecha_alta' => date('Y-m-d H:i:s'),
                    'activo' => '1',
                        ), false);
                $dispositivo_id = $this->dispositivo_model->get_row_id();
                $this->load->model('usuario_dispositivo_model');
                $trans_ok &= $this->usuario_dispositivo_model->create(array(
                    'usuario_id' => $this->input->post('usuario'),
                    'dispositivo_id' => $dispositivo_id,
                    'fecha_alta' => date('Y-m-d H:i:s'),
                        ), false);

                if ($this->db->trans_status() && $trans_ok) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->dispositivo_model->get_msg());
                } else {
                    $errors = $this->dispositivo_model->get_error() . "<br>" . $this->usuarios_model->get_error() . "<br>";
                    $this->session->set_flashdata('error', $errors);
                }
            } else {
                $this->session->set_flashdata('error', validation_errors());
            }
            redirect('dispositivo/listar', 'refresh');
        }

        $model = new stdClass();
        $model->fields = array(
            'usuario' => array('label' => 'Usuario', 'input_type' => 'combo', 'class' => 'selectize', 'id_name' => 'usuario_id', 'required' => true),
        );

        $this->load->model('usuarios_model');
        $array_usuarios = $this->get_array('usuarios', "usuario", 'id', array(
            'select' => array("concat(username, '(', first_name, ' ', last_name, ')') as usuario, id"),
            'where' => array('active = 1'),
                ), array('' => '-- Seleccionar usuario --'));
        $model->fields['usuario']['array'] = $array_usuarios;
        $data['fields_u'] = $this->build_fields($model->fields);
        $data['fields'] = $this->build_fields($this->dispositivo_model->fields);
        $data['txt_btn'] = 'Crear';
        $data['btn_color'] = 'btn-primary';
        $data['title'] = TITLE . ' - Crear Dispositivo';
        $this->load->view('dispositivo/dispositivo_modal_abm', $data);
    }

    public function modal_enviar_notificacion($id = null) {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == null || !ctype_digit($id)) {
            $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
        }
        $dispositivo = $this->dispositivo_model->get_one($id);
        if (empty($dispositivo)) {
            $this->modal_error('No se encontró el registro', 'Registro no encontrado');
        }

        if (isset($_POST) && !empty($_POST)) {
            $host = "localhost";
            $port = 9000;
            if ($socket = socket_create(AF_INET, SOCK_STREAM, 0)) {
                if (socket_connect($socket, $host, $port)) {
                    if (socket_write($socket, $this->input->post('mensaje'), strlen($this->input->post('mensaje')))) {
                        socket_close($socket);
                        $this->session->set_flashdata('message', 'Mensaje enviado');
                    } else {
                        $this->session->set_flashdata('error', 'Error al enviar el mensaje');
                    }
                } else {
                    $this->session->set_flashdata('error', 'Error al conectar al socket');
                }
            } else {
                $this->session->set_flashdata('error', 'Error al crear el socket');
            }
            redirect("dispositivo/ver/$dispositivo->id", 'refresh');
        }

        $model = new stdClass();
        $model->fields = array(
            'mensaje' => array('label' => 'Mensaje', 'type' => 'text', 'maxlength' => '100', 'required' => true),
        );

        $data['dispositivo'] = $dispositivo;
        $data['fields'] = $this->build_fields($model->fields);
        $data['txt_btn'] = 'Enviar';
        $data['btn_color'] = 'btn-primary';
        $data['title'] = TITLE . ' - Enviar notificación';
        $this->load->view('dispositivo/dispositivo_modal_notificacion', $data);
    }

    public function ajax_get_ultima_medicion() {
        $dispositivo_id = $this->input->post('dispositivo_id');
        $tipos = $this->input->post('tipos');
        $ultima_medicion = $this->dispositivo_model->get_ultima_medicion($dispositivo_id, $tipos);
        if (!empty($ultima_medicion)) {
            $array_medicion = array();
            foreach ($ultima_medicion as $medicion) {
                array_push($array_medicion, array($medicion->id => $medicion->valor));
            }
            echo json_encode($array_medicion);
            return;
        } else {
            echo json_encode(array('error' => 'error'));
            return;
        }
    }

}

/* End of file Cliente.php */
    /* Location: ./application/controllers/Cliente.php */
    