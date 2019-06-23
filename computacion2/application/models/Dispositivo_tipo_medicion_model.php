<?php

class Usuario_dispositivo_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->table_name = 'dispositivo_tipo_medicion';
        $this->msg_name = 'Tipo de Medición de Dispositivo';
        $this->id_name = 'id';
        $this->columnas = array('id', 'dispositivo_id', 'tipo_medicion_id');
        $this->fields = array(
        );
        $this->requeridos = array('tipo_medicion_id', 'dispositivo_id');
        $this->default_join = array('');
    }

}
