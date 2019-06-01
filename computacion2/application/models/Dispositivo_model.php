<?php

class Dispositivo_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->table_name = 'dispositivo';
        $this->msg_name = 'Dispositivo';
        $this->id_name = 'id';
        $this->columnas = array('id', 'descripcion', 'fecha_alta', 'activo');
        $this->fields = array(
            'descripcion' => array('label' => 'Nombre', 'type' => 'text', 'maxlength' => '100', 'required' => true),
            'fecha_alta' => array('label' => 'Fecha de Alta', 'type' => 'datetime', 'readonly' => true),
        );
        $this->requeridos = array('descripcion');
        $this->default_join = array(
            array('dispositivo_tipo_medicion', 'dispositivo_tipo_medicion.dispositivo_id=dispositivo.id', 'left', array('count(dispositivo_tipo_medicion.id) as tipos')),
            array('tipo_medicion', 'tipo_medicion.id=dispositivo_tipo_medicion.tipo_medicion_id', '', array("group_concat(tipo_medicion.descripcion SEPARATOR ', ') as tipos_medicion"))
        );
    }

    public function get_mediciones($dispositivo_id) {
        $tipos_medicion = $this->db->select('tipo_medicion.id, tipo_medicion.descripcion')
                        ->from('dispositivo_tipo_medicion')
                        ->join('tipo_medicion', 'tipo_medicion.id = dispositivo_tipo_medicion.tipo_medicion_id')
                        ->where('dispositivo_id', $dispositivo_id)
                        ->get()->result();
        $array_tipos_medicion = array();
        foreach ($tipos_medicion as $tipo) {
            if (!isset($array_tipos_medicion[$tipo->id])) {
                $array_tipos_medicion[$tipo->id] = $tipo;
                $array_tipos_medicion[$tipo->id]->valores = array();
                $array_tipos_medicion[$tipo->id]->color_linea = ($this->db->select('color_linea')
                                ->from('tipo_medicion')
                                ->where('tipo_medicion.id', $tipo->id)
                                ->get()->row()->color_linea);
                $array_tipos_medicion[$tipo->id]->valores = ($this->db->select('group_concat(medicion_valor.valor) as valor')
                                ->from('medicion')
                                ->join('medicion_valor', 'medicion_valor.medicion_id = medicion.id')
                                ->join('tipo_medicion', 'tipo_medicion.id= medicion_valor.tipo_medicion_id')
                                ->where('medicion.dispositivo_id', $dispositivo_id)
                                ->where('tipo_medicion.id', $tipo->id)
                                ->order_by('medicion.id', 'asc')
                                ->get()->row()->valor);
                $array_tipos_medicion[$tipo->id]->min = ($this->db->select('min(medicion_valor.valor) as valor')
                                ->from('medicion')
                                ->join('medicion_valor', 'medicion_valor.medicion_id = medicion.id')
                                ->join('tipo_medicion', 'tipo_medicion.id= medicion_valor.tipo_medicion_id')
                                ->where('medicion.dispositivo_id', $dispositivo_id)
                                ->where('tipo_medicion.id', $tipo->id)
                                ->order_by('medicion.id', 'asc')
                                ->get()->row()->valor) - 10;
                $array_tipos_medicion[$tipo->id]->max = ($this->db->select('max(medicion_valor.valor) as valor')
                                ->from('medicion')
                                ->join('medicion_valor', 'medicion_valor.medicion_id = medicion.id')
                                ->join('tipo_medicion', 'tipo_medicion.id= medicion_valor.tipo_medicion_id')
                                ->where('medicion.dispositivo_id', $dispositivo_id)
                                ->where('tipo_medicion.id', $tipo->id)
                                ->order_by('medicion.id', 'asc')
                                ->get()->row()->valor) + 10;
            }
        }
        return $array_tipos_medicion;
    }

    public function get_ultima_medicion($dispositivo_id, $tipos) {
        return $this->db->select('tipo_medicion.id,floor(medicion_valor.valor) as valor')
                        ->from('dispositivo_tipo_medicion')
                        ->join('tipo_medicion', 'tipo_medicion.id = dispositivo_tipo_medicion.tipo_medicion_id')
                        ->join('medicion_valor', 'medicion_valor.tipo_medicion_id = tipo_medicion.id')
                        ->where('dispositivo_id', $dispositivo_id)
                        ->order_by('medicion_valor.id', 'desc')
                        ->limit($tipos)
                        ->get()->result();
    }

}
