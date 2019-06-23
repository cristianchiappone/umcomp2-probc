<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tipo_medicion extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('tipo_medicion_model');
        $this->grupos_permitidos = array('admin');
        $this->nav_route = 'tipo_medicion';
    }

    public function listar() {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => '#', 'data' => 'id', 'width' => 10),
                array('label' => 'Descripción', 'data' => 'descripcion', 'width' => 35),
                array('label' => 'Código', 'data' => 'codigo', 'width' => 25),
                array('label' => 'Color Linea', 'data' => 'color_linea', 'width' => 10),
                array('label' => 'Activo', 'data' => 'activo', 'width' => 10),
                array('label' => '', 'data' => 'edit', 'width' => 10, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
            ),
            'table_id' => 'tipo_medicion_table',
            'order' => array(array(0, 'desc')),
            'source_url' => 'tipo_medicion/listar_data/',
            'reuse_var' => true,
            'initComplete' => 'complete_tipo_medicion_table',
            'footer' => true,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title'] = TITLE . ' - Tipos de Medición';
        $this->load_template('tipo_medicion/tipo_medicion_listar', $data);
    }

    public function listar_data() {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }
        $this->datatables
                ->select("tipo_medicion.id, tipo_medicion.descripcion, tipo_medicion.codigo,(CASE WHEN tipo_medicion.activo = 1 THEN 'Si' ELSE 'No' END) as activo, tipo_medicion.color_linea")
                ->unset_column('id')
                ->from('tipo_medicion')
                ->add_column('color_linea', '<span class="fa fa-square" style="color: $1"></span>', 'color_linea');

        $this->datatables->add_column('edit', ''
                . '<a href="tipo_medicion/modal_ver/$1" data-remote="false" data-toggle="modal" data-target="#remote_modal"  class="btn btn-xs btn-default" title="Ver"><i class="fa fa-search"></i></a> '
                . '<a href="tipo_medicion/modal_editar/$1" data-remote="false" data-toggle="modal" data-target="#remote_modal" class="btn btn-xs btn-warning" title="Editar"><i class="fa fa-pencil"></i></a> '
                . '<a href="tipo_medicion/modal_baja/$1" data-remote="false" data-toggle="modal" data-target="#remote_modal" class="btn btn-xs btn-danger" title="dar de baja"><i class="fa fa-ban"></i></a>'
                . '', 'id');

        echo $this->datatables->generate();
    }

    public function modal_ver($id = null) {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
        }

        $tipo_medicion = $this->tipo_medicion_model->get_one($id);
        if (empty($tipo_medicion)) {
            $this->modal_error('No se encontró el registro a ver', 'Registro no encontrado');
        }
        $data['fields'] = $this->build_fields($this->tipo_medicion_model->fields, $tipo_medicion, true);
        $data['tipo_medicion'] = $tipo_medicion;
        $data['txt_btn'] = '';
        $data['btn_color'] = 'btn-primary';
        $data['title'] = TITLE . ' - Ver Tipo de Medición';
        $this->load->view('tipo_medicion/tipo_medicion_modal_abm', $data);
    }

    public function modal_editar($id = null) {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == null || !ctype_digit($id)) {
            $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
        }
        $tipo_medicion = $this->tipo_medicion_model->get_one($id);
        if (empty($tipo_medicion)) {
            $this->modal_error('No se encontró el registro a editar', 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->tipo_medicion_model);
        if (isset($_POST) && !empty($_POST)) {
            if ($id !== $this->input->post('id')) {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }
            if ($this->form_validation->run() === true) {
                $trans_ok = true;
                $trans_ok &= $this->tipo_medicion_model->update(array(
                    'id' => $tipo_medicion->id,
                    'descripcion' => $this->input->post('descripcion'),
                    'codigo' => $this->input->post('codigo'),
                    'color_linea' => $this->input->post('color_linea')
                ));
                if ($trans_ok) {
                    $this->session->set_flashdata('message', $this->tipo_medicion_model->get_msg());
                    redirect('tipo_medicion/listar', 'refresh');
                } else {
                    $this->session->set_flashdata('error', $this->tipo_medicion_model->get_error());
                    redirect('tipo_medicion/listar', 'refresh');
                }
            } else {
                $this->session->set_flashdata('error', validation_errors());
                redirect('tipo_medicion/listar', 'refresh');
            }
        }
        $data['fields'] = $this->build_fields($this->tipo_medicion_model->fields, $tipo_medicion);
        $data['tipo_medicion'] = $tipo_medicion;
        $data['txt_btn'] = 'Editar';
        $data['btn_color'] = 'btn-primary';
        $data['title'] = TITLE . ' - Editar Tipo de Medición';
        $this->load->view('tipo_medicion/tipo_medicion_modal_abm', $data);
    }

    public function modal_agregar() {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
        }
        $this->set_model_validation_rules($this->tipo_medicion_model);
        if (isset($_POST) && !empty($_POST)) {
            if ($this->form_validation->run() === true) {
                $trans_ok = true;
                $trans_ok &= $this->tipo_medicion_model->create(array(
                    'descripcion' => $this->input->post('descripcion'),
                    'codigo' => $this->input->post('codigo'),
                    'color_linea' => $this->input->post('color_linea'),
                    'activo' => '1',
                        ), false);
                if ($trans_ok) {
                    $this->session->set_flashdata('message', $this->tipo_medicion_model->get_msg());
                } else {
                    $this->session->set_flashdata('error', $this->tipo_medicion_model->get_error());
                }
                redirect('tipo_medicion/listar', 'refresh');
            } else {
                $this->session->set_flashdata('error', validation_errors());
                redirect('tipo_medicion/listar', 'refresh');
            }
        }
        $data['fields'] = $this->build_fields($this->tipo_medicion_model->fields);
        $data['txt_btn'] = 'Crear';
        $data['btn_color'] = 'btn-primary';
        $data['title'] = TITLE . ' - Crear Tipo de Medición';
        $this->load->view('tipo_medicion/tipo_medicion_modal_abm', $data);
    }

    public function modal_baja($id = null) {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == null || !ctype_digit($id)) {
            $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
        }
        $tipo_medicion = $this->tipo_medicion_model->get_one($id);
        if (empty($tipo_medicion)) {
            $this->modal_error('No se encontró el registro', 'Registro no encontrado');
        }
        $this->set_model_validation_rules($this->tipo_medicion_model);
        if (isset($_POST) && !empty($_POST)) {
            if ($id !== $this->input->post('id')) {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }
            $trans_ok = true;
            $trans_ok &= $this->tipo_medicion_model->update(array(
                'id' => $tipo_medicion->id,
                'activo' => '0',
            ));
            if ($trans_ok) {
                $this->session->set_flashdata('message', $this->tipo_medicion_model->get_msg());
                redirect('tipo_medicion/listar', 'refresh');
            } else {
                $this->session->set_flashdata('error', $this->tipo_medicion_model->get_error());
                redirect('tipo_medicion/listar', 'refresh');
            }
        }

        $data['tipo_medicion'] = $tipo_medicion;
        $tipo_medicion->password = '';
        $data['fields'] = $this->build_fields($this->tipo_medicion_model->fields, $tipo_medicion, true);
        $data['txt_btn'] = 'Dar de baja';
        $data['btn_color'] = 'btn-danger';
        $data['title'] = TITLE . ' - Baja Tipo de Medición';
        $this->load->view('tipo_medicion/tipo_medicion_modal_abm', $data);
    }

    public function modal_agregar_tipo_medicion($dispositivo_id = NULL) {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $dispositivo_id == null || !ctype_digit($dispositivo_id)) {
            $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
        }
        $this->load->model('dispositivo_model');
        $dispositivo = $this->dispositivo_model->get_one($dispositivo_id);
        if (empty($dispositivo)) {
            $this->modal_error('No se encontró el registro a editar', 'Registro no encontrado');
        }

        $this->array_tipos_medicion_control = $array_tipos_medicion = $this->get_array('tipo_medicion', 'descripcion', 'id', array(0 => '-- Seleccionar departamento --'));

        $this->set_model_validation_rules($this->dispositivo_model);
        if (isset($_POST) && !empty($_POST)) {
            if ($id !== $this->input->post('id')) {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }
            if ($this->form_validation->run() === true) {
                $trans_ok = true;
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

        $model = new stdClass();
        $model->fields = array(
            'descripcion' => array('label' => 'Nombre', 'type' => 'text', 'maxlength' => '100', 'readonly' => true),
            'tipos_medicion' => array('label' => 'Tipos de Medición', 'input_type' => 'combo', 'id_name' => 'tipos_medicion_id', 'required' => TRUE, 'class' => 'selectize', 'type' => 'multiple'),
        );

        $model->fields['tipos_medicion']['array'] = $array_tipos_medicion;

        $data['fields'] = $this->build_fields($model->fields, $dispositivo);
        $data['dispositivo'] = $dispositivo;
        $data['txt_btn'] = 'Editar';
        $data['btn_color'] = 'btn-primary';
        $data['title'] = TITLE . ' - Agregar/Editar/Eliminar Tipo de Medición';
        $this->load->view('tipo_medicion/tipo_medicion_dispositivo_modal_Abm', $data);
    }

}

/* End of file Cliente.php */
/* Location: ./application/controllers/Cliente.php */
