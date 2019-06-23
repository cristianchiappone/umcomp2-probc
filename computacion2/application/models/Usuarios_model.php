<?php

class Usuarios_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'users';
        $this->msg_name = 'Usuarios';
        $this->id_name = 'id';
        $this->columnas = array('id', 'username', 'email', 'active', 'first_name', 'last_name', 'created_on');
        $this->fields = array(
            'username' => array('label' => 'Nombre de Usuario', 'type' => 'text', 'maxlength' => '100'),
            'email' => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100'),
            'first_name' => array('label' => 'Nombre', 'type' => 'text', 'maxlength' => '50'),
            'last_name' => array('label' => 'Apellido', 'type' => 'text', 'maxlength' => '50'),
            'password' => array('label' => 'Password', 'type' => 'password', 'minlength' => '8'),
        );
        $this->requeridos = array('email', 'password', 'username', 'first_name', 'last_name');
        $this->default_join = array();
    }

    public function get_user_data($email)
    {
        return $this->db->select('users.id as user_id')
                ->from('users')
                ->where('users.email', $email)
                ->get()->result();
    }
}
