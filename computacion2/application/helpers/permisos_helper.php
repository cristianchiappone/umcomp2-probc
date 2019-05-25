<?php

defined('BASEPATH') OR exit('No direct script access allowed');

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
		return (!empty($result));
	}
}

if (!function_exists('load_permisos_nav')) {

	function load_permisos_nav($userdata_groups, $route) {
		foreach ($userdata_groups as $user_group) {
			$grupos[] = $user_group->name;
		}
		$li_class = array('usuarios' => '', 'oferta' => '', 'sucursal' => '', 'producto' => '', 'pedido' => '', 'fidelidad' => '', 'cupon' => '');
		$li_class[$route] = 'active';
		$nav = '';
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