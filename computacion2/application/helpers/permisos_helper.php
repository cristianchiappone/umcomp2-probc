<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('groups_names')) {

    function groups_names($grupos) {
        $nombres = array();
        foreach ($grupos as $Grupo) {
            array_push($nombres, $Grupo['name']);
        }

        return $nombres;
    }

}

if (!function_exists('in_groups')) {

    function in_groups($grupos_permitidos, $grupos) {
        $result = array_intersect($grupos_permitidos, $grupos);

        return !empty($result);
    }

}

if (!function_exists('load_permisos_nav')) {

    function load_permisos_nav($userdata_groups, $route, $alertas = null) {
        foreach ($userdata_groups as $user_group) {
            $grupos[] = $user_group->name;
        }
            $li_class = array('usuarios' => '', 'dispositivo' => '', 'tipo_medicion' => '');
        $li_class[$route] = 'active';
        $nav = '';

        if (in_groups($grupos, array('admin'))) {
            $nav .= " <li class='nav-item {$li_class['usuarios']}'><a href='usuarios/listar' class='nav-link {$li_class['usuarios']}'><i class='fa fa-users nav-icon'></i> <p>Usuarios</p></a></li>";
            $nav .= " <li class='nav-item {$li_class['dispositivo']}'><a href='dispositivo/listar' class='nav-link {$li_class['dispositivo']}'><i class='fa fa-microchip nav-icon'></i> <p>Dispositivos</p></a></li>";
            $nav .= " <li class='nav-item {$li_class['tipo_medicion']}'><a href='tipo_medicion/listar' class='nav-link {$li_class['tipo_medicion']}'><i class='fa fa-thermometer-full nav-icon'></i> <p>Tipos de Medición</p></a></li>";
        }

        return $nav;
    }

}

if (!function_exists('load_permisos_escritorio')) {

    function load_permisos_escritorio($userdata) {
        foreach ($userdata['user_groups'] as $user_group) {
            $grupos[] = $user_group->name;
        }
        $accesos = '';
        $accesos .= '<ul class="ds-btn">';
        $accesos .= '</ul>';

        return $accesos;
    }

}
/* End of file permisos_helper.php */
/* Location: ./application/helpers/permisos_helper.php */
