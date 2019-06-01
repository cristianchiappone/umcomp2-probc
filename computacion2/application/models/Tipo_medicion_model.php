<?php

class Tipo_medicion_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->table_name = 'tipo_medicion';
        $this->msg_name = 'Tipo de Medición';
        $this->id_name = 'id';
        $this->columnas = array('id', 'descripcion', 'codigo', 'color_linea', 'activo');
        $this->fields = array(
            'descripcion' => array('label' => 'Descripción', 'type' => 'text', 'maxlength' => '45', 'required' => true),
            'codigo' => array('label' => 'Código', 'type' => 'text', 'maxlength' => '45', 'required' => true),
            'color_linea' => array('label' => 'Color', 'type' => 'color', 'maxlength' => '7', 'required' => true, 'style' => 'height:38px'),
        );
        $this->requeridos = array('descripcion', 'activo', 'color_linea');
        $this->default_join = array();
    }

}
