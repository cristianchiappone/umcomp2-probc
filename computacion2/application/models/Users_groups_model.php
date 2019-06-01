<?php

class Users_groups_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->table_name = 'users_groups';
        $this->msg_name = 'Grupos de Usuarios';
        $this->id_name = 'id';
        $this->columnas = array('id', 'user_id', 'group_id');
        $this->fields = array();
        $this->requeridos = array('user_id', 'group_id');
        $this->default_join = array();
    }

    public function get_user_groups($email) {
        return $this->db->select('users.id as user_id,users_groups.group_id, groups.name as group')
                        ->from('users')
                        ->join('users_groups', 'users.id = users_groups.user_id')
                        ->join('groups', 'groups.id = users_groups.group_id')
                        ->where('users.email', $email)
                        ->get()->row();
    }

}
