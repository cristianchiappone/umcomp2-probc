<?php

class Usuario_dispositivo_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->table_name = 'usuario_dispositivo';
        $this->msg_name = 'UsuarioDispositivo';
        $this->id_name = 'id';
        $this->columnas = array('id', 'usuario_id', 'dispositivo_id', 'fecha_alta');
        $this->fields = array(
        );
        $this->requeridos = array('usuario_id', 'dispositivo_id');
        $this->default_join = array('');
    }

}
