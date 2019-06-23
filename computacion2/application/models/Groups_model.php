<?php

class Groups_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->table_name = 'groups';
        $this->msg_name = 'Grupos';
        $this->id_name = 'id';
        $this->columnas = array('id', 'name', 'description');
        $this->fields = array();
        $this->requeridos = array('name', 'description');
        $this->default_join = array();
    }

}
