<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public $dias_semana = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
    protected $login = true;

    public function __construct()
    {
        parent::__construct();
        setlocale(LC_TIME, 'Spanish');
        $this->load->library(['ion_auth', 'form_validation']);

        if ($this->login) {
            if (!$this->ion_auth->logged_in()) {
                redirect('auth/login/'.str_replace('/', '%20', uri_string()));
            }
        }
        $this->grupos = groups_names($this->ion_auth->get_users_groups()->result_array());
        $this->nav_route = 'esc';
    }

    public function index()
    {
        if (in_groups($this->grupos_permitidos, $this->grupos)) {
            redirect(uri_string().'/listar', 'refresh');
        } else {
            show_404();
        }
    }

    protected function set_filtro_datos_listar($post_name, $all_string, $column_name, $user_data, &$where_array)
    {
        if (!empty($_POST[$post_name]) && $this->input->post($post_name) != $all_string) {
            $where['column'] = $column_name;
            $where['value'] = $this->input->post($post_name);
            $where_array[] = $where;
            $this->session->set_userdata($user_data, $this->input->post($post_name));
        } elseif (empty($_POST[$post_name]) && $this->session->userdata($user_data) !== false) {
            $where['column'] = $column_name;
            $where['value'] = $this->session->userdata($user_data);
            $where_array[] = $where;
        } else {
            $this->session->unset_userdata($user_data);
        }
    }

    public function control_combo($opt, $type)
    {
        $array_name = 'array_'.$type.'_control';
        if (array_key_exists($opt, $this->$array_name)) {
            return true;
        } else {
            return false;
        }
    }

    public function get_array($model, $desc = 'descripcion', $id = 'id', $options = array(), $array_registros = array())
    {
        if (empty($options)) {
            $options['sort_by'] = $desc;
        }

        $registros = $this->{"{$model}_model"}->get($options);
        if (!empty($registros)) {
            foreach ($registros as $Registro) {
                $array_registros[$Registro->{$id}] = $Registro->{$desc};
            }
        }

        return $array_registros;
    }

    public function set_model_validation_rules($model)
    {
        foreach ($model->fields as $name => $field) {
            if (empty($field['name'])) {
                $field['name'] = $name;
            }
            if (empty($field['input_type'])) {
                $this->add_input_validation_rules($field);
            } elseif ($field['input_type'] === 'combo') {
                $this->add_combo_validation_rules($field);
            }
        }
    }

    public function add_input_validation_rules($field_opts)
    {
        $name = $field_opts['name'];
        if (!isset($field_opts['label'])) {
            $label = ucfirst($name);
        } else {
            $label = $field_opts['label'];
        }
        $rules = ''; // xss_clean no se controla mas aca

        if (isset($field_opts['required']) && $field_opts['required']) {
            $rules .= '|required';
        }
        if (isset($field_opts['minlength'])) {
            $rules .= '|min_length['.$field_opts['minlength'].']';
        }
        if (isset($field_opts['maxlength'])) {
            $rules .= '|max_length['.$field_opts['maxlength'].']';
        }
        if (isset($field_opts['maxvalue'])) {
            $rules .= '|less_than_equal_to['.$field_opts['maxvalue'].']';
        }
        if (isset($field_opts['greater_than'])) {
            $rules .= '|greater_than['.$field_opts['greater_than'].']';
        }
        if (isset($field_opts['matches'])) {
            $rules .= '|matches['.$field_opts['matches'].']';
        }

        if (isset($field_opts['type'])) {
            switch ($field_opts['type']) {
                case 'integer':
                case 'integer_neg_pos':
                    $rules .= '|integer';
                    break;
                case 'numeric':
                    $rules .= '|numeric';
                    break;
                case 'decimal':
                    $rules .= '|decimal';
                    break;
                case 'money':
                    $rules .= '|money';
                    break;
                case 'money3':
                    $rules .= '|money3';
                    break;
                case 'date':
                    $rules .= '|validate_date';
                    break;
                case 'time':
                    $rules .= '|validate_time';
                    break;
                case 'datetime':
                    $rules .= '|validate_datetime';
                    break;
                case 'cbu':
                    $rules .= '|validate_cbu';
                    break;
                case 'email':
                    $rules .= '|valid_email';
                    break;
                default:
                    break;
            }
        }
        if (empty($rules)) {
            $rules = 'trim';
        }

        $this->form_validation->set_rules($name, $label, trim($rules, '|'));
    }

    public function add_combo_validation_rules($field_opts)
    {
        $name = $field_opts['name'];
        if (!isset($field_opts['arr_name'])) {
            $arr_name = $field_opts['name'];
        } else {
            $arr_name = $field_opts['arr_name'];
        }

        if (!isset($field_opts['label'])) {
            $label = ucfirst($name);
        } else {
            $label = $field_opts['label'];
        }

        $rules = "callback_control_combo[$arr_name]";
        if (isset($field_opts['type']) && $field_opts['type'] === 'multiple') {
            $this->form_validation->set_rules($name.'[]', $label, $rules);
        } else {
            $this->form_validation->set_rules($name, $label, $rules);
        }
    }

    public function add_input_field(&$field_array, $field_opts, $def_value = null)
    {
        if ($def_value === null) {
            $field['value'] = $this->form_validation->set_value($field_opts['name']);
        } else {
            $field['value'] = $this->form_validation->set_value($field_opts['name'], $def_value);
        }

        foreach ($field_opts as $key => $field_opt) {
            $field[$key] = $field_opt;
        }

        $field['id'] = $field_opts['name'];
        $field['class'] = 'form-control'.(empty($field_opts['class']) ? '' : " {$field_opts['class']}");
        if (isset($field_opts['type'])) {
            switch ($field_opts['type']) {
                case 'cbu':
                    $field['pattern'] = '[0-9]*';
                    $field['title'] = 'Debe ingresar sólo números';
                    $field['type'] = 'text';
                    break;
                case 'integer':
                    $field['pattern'] = '^(0|[1-9][0-9]*)$';
                    $field['title'] = 'Debe ingresar sólo números';
                    $field['type'] = 'text';
                    if (empty($field_opts['class'])) {
                        $field['class'] .= ' integerFormat';
                    }
                    break;
                case 'integer_neg_pos':
                    $field['pattern'] = '^[-]?(0|[1-9][0-9]*)$';
                    $field['title'] = 'Debe ingresar sólo números';
                    $field['type'] = 'text';
                    if (empty($field_opts['class'])) {
                        $field['class'] .= ' integerFormat';
                    }
                    break;
                case 'numeric':
                    $field['pattern'] = '[0-9]*[.,]?[0-9]+';
                    $field['title'] = 'Debe ingresar sólo números decimales';
                    $field['type'] = 'text';
                    if (empty($field_opts['class'])) {
                        $field['class'] .= ' numberFormat';
                    }
                    break;
                case 'money':
                    $field['pattern'] = '[-]?[0-9]+([,\.][0-9]{1,2})?';
                    $field['title'] = 'Debe ingresar un importe';
                    $field['type'] = 'text';
                    if (empty($field_opts['class'])) {
                        $field['class'] .= ' precioFormat';
                    }
                    if ($def_value !== null) {
                        $field['value'] = $this->form_validation->set_value($field_opts['name'], str_replace('.', ',', $def_value));
                    }
                    break;
                case 'money3':
                    $field['pattern'] = '[-]?[0-9]+([,\.][0-9]{1,3})?';
                    $field['title'] = 'Debe ingresar un importe';
                    $field['type'] = 'text';
                    if (empty($field_opts['class'])) {
                        $field['class'] .= ' precio3Format';
                    }
                    if ($def_value !== null) {
                        $field['value'] = $this->form_validation->set_value($field_opts['name'], str_replace('.', ',', $def_value));
                    }
                    break;
                case 'date':
                    if (empty($field_opts['class'])) {
                        $field['class'] .= ' dateFormat';
                    }
                    $field['type'] = 'text';
                    if ($def_value !== null) {
                        $field['value'] = $this->form_validation->set_value($field_opts['name'], date_format(new DateTime($def_value), 'd/m/Y'));
                    }
                    break;
                case 'time':
                    if (empty($field_opts['class'])) {
                        $field['class'] .= ' timeFormat';
                    }
                    $field['type'] = 'text';
                    if ($def_value !== null) {
                        $field['value'] = $this->form_validation->set_value($field_opts['name'], date_format(new DateTime($def_value), 'H:i'));
                    }
                    break;
                case 'datetime':
                    if (empty($field_opts['class'])) {
                        $field['class'] .= ' dateTimeFormat';
                    }
                    $field['type'] = 'text';
                    if ($def_value !== null) {
                        $field['value'] = $this->form_validation->set_value($field_opts['name'], date_format(new DateTime($def_value), 'd/m/Y H:i'));
                    }
                    break;
                case 'password':
                    $field['type'] = 'password';
                    break;
                default:
                    break;
            }
        }

        $field['label'] = form_label($field_opts['label'], $field_opts['name']).'<div style="float:right;" class="help-block with-errors"></div>';
        $field_array[$field_opts['name']] = $field;
        $form_type = empty($field['form_type']) ? 'input' : $field['form_type'];
        unset($field['disabled']);
        unset($field['form_type']);
        unset($field['label']);
        unset($field['required']);
        unset($field['minlength']);
        unset($field['matches']);

        if (!empty($field_opts['disabled']) && $field_opts['disabled']) {
            $field['disabled'] = '';
        }

        if (!empty($field_opts['required']) && $field_opts['required']) {
            $field['required'] = '';
        }

        if (!empty($field_opts['error_text'])) {
            $field['data-error'] = $field_opts['error_text'];
        }

        if (!empty($field_opts['minlength'])) {
            $field['data-minlength'] = $field_opts['minlength'];
        }

        if (!empty($field_opts['val_match'])) {
            if (!empty($field_opts['val_match_text'])) {
                $field['data-match-error'] = $field_opts['val_match_text'];
            }
            $field['data-match'] = '#'.$field_opts['val_match'];
        }

        if ($form_type === 'input') {
            $form = form_input($field);
        } elseif ($form_type === 'textarea') {
            $form = form_textarea($field);
        }

        if (!empty($field_opts['required']) && $field_opts['required']) {
            if (isset($field_opts['type']) && ($field_opts['type'] === 'money' || $field_opts['type'] === 'money3')) {
                $form = '<div class="input-group"><span class="input-group-text"><i class="fa fa-dollar"></i></span>'.$form.'<span title="Requerido" class="input-group-text" style="color:red"><i class="fa fa-exclamation"></i></span></div>';
            } else {
                $form = '<div class="input-group">'.$form.'<span title="Requerido" class="input-group-text" style="color:red"><i class="fa fa-exclamation"></i></span></div>';
            }
        } else {
            if (isset($field_opts['type']) && ($field_opts['type'] === 'money' || $field_opts['type'] === 'money3')) {
                $form = '<div class="input-group"><span class="input-group-text"><i class="fa fa-dollar"></i></span>'.$form.'</div>';
            }
        }
        $field_array[$field_opts['name']]['form'] = $form;
    }

    public function add_combo_field(&$field_array, $field_opts, $def_value = null)
    {
        $values = $field_opts['array'];
        if ($def_value == null) {
            if (isset($field_opts['type']) && ($field_opts['type'] === 'multiple')) {
                $anterior = null;
                $field['value'][] = $existe = $this->form_validation->set_value($field_opts['name'].'[]', isset($field_opts['value']) ? $field_opts['value'] : null);
                while (!empty($existe) && $anterior !== $existe) {
                    $anterior = $existe;
                    $field['value'][] = $existe = $this->form_validation->set_value($field_opts['name'].'[]', isset($field_opts['value']) ? $field_opts['value'] : null);
                }
            } else {
                $field['value'] = $this->form_validation->set_value($field_opts['name'], isset($field_opts['value']) ? $field_opts['value'] : null);
            }
        } else {
            if (isset($field_opts['type']) && ($field_opts['type'] === 'multiple')) {
                $anterior = null;
                $field['value'][] = $existe = $this->form_validation->set_value($field_opts['name'].'[]', $def_value, isset($field_opts['value']) ? $field_opts['value'] : null);
                if (is_array($existe)) {
                    $field['value'] = $existe;
                } else {
                    $field['value'][] = $existe;
                    while (!empty($existe) && $anterior !== $existe) {
                        $anterior = $existe;
                        $existe = $this->form_validation->set_value($field_opts['name'].'[]', $def_value, isset($field_opts['value']) ? $field_opts['value'] : null);
                        if (!is_array($existe)) {
                            $field['value'][] = $existe;
                        }
                    }
                }
            } else {
                $field['value'] = $this->form_validation->set_value($field_opts['name'], $def_value);
            }
        }

        $field_array[$field_opts['name']]['required'] = empty($field_opts['required']) ? false : $field_opts['required'];
        if (!isset($field_opts['label'])) {
            $field_opts['label'] = ucfirst($field_opts['name']);
        }

        unset($field['disabled']);

        $label = form_label($field_opts['label'], $field_opts['name']).'<div style="float:right;" class="help-block with-errors"></div>';

        $extras = '';
        if (!empty($field_opts['disabled']) && $field_opts['disabled']) {
            $extras .= ' disabled';
        }

        if (!empty($field_opts['required']) && $field_opts['required']) {
            $extras .= ' required';
        }

        if (!empty($field_opts['style'])) {
            $extras .= ' style="'.$field_opts['style'].'"';
        }

        if (!empty($field_opts['class'])) {
            $extraClass = ' '.$field_opts['class'];
        } else {
            $extraClass = ' select_selectize';
        }

        if (!empty($field_opts['error_text'])) {
            $extras .= ' data-error="'.$field_opts['error_text'].'"';
        }

        $script = '';
        if (isset($field_opts['type']) && $field_opts['type'] === 'multiple') {
            if (isset($field_opts['plugin'])) {
                if ($field_opts['plugin'] === 'duallistbox') {
                    if (!isset($field_opts['disabled'])) {
                        $script = '';
                        $form = form_dropdown($field_opts['name'].'[]', $values, $field['value'], 'class="form-control duallistbox" id="'.$field_opts['name'].'" multiple="" tabindex="-1" aria-hidden="true"'.$extras);
                    } else {
                        $script = '<script>
							$(document).ready(function() {
								$("#'.$field_opts['name'].'").find("option:not(:selected)").remove().end();
							});
						</script>';
                        $form = form_dropdown($field_opts['name'].'[]', $values, $field['value'], 'class="form-control" id="'.$field_opts['name'].'" multiple="" tabindex="-1" aria-hidden="true"'.$extras);
                    }
                } elseif ($field_opts['plugin'] === 'select2') {
                    if (!empty($def_value)) {
                        $set_value = '
								$("#'.$field_opts['name'].'").val(['.$def_value.']).trigger("change");';
                    } elseif (!empty($field['value'])) {
                        $set_value = '
								$("#'.$field_opts['name'].'").val(['.$field['value'].']).trigger("change");';
                    } else {
                        $set_value = '';
                    }
                    $script = '<script>
							$(document).ready(function() {
								$("#'.$field_opts['name'].'").select2({
									placeholder: "Seleccione '.$field_opts['label'].'"
								});'.$set_value.'
							});
						</script>';
                    $form = form_dropdown($field_opts['name'].'[]', $values, $field['value'], 'class="form-control select2" id="'.$field_opts['name'].'" multiple tabindex="-1" aria-hidden="true"'.$extras);
                }
            } else {
                $form = form_dropdown($field_opts['name'].'[]', $values, $field['value'], 'class="form-control'.$extraClass.'" id="'.$field_opts['name'].'" multiple tabindex="-1" aria-hidden="true"'.$extras);
            }
        } else {
            if (isset($field_opts['plugin'])) {
                switch ($field_opts['plugin']) {
                    case 'select2':
                        $script = '<script>
							$(document).ready(function() {
								$("#'.$field_opts['name'].'").select2({
									placeholder: "-- Seleccionar '.$field_opts['label'].' --"
								});
							});
						</script>';
                        break;
                    case 'select2_noreq':
                        $script = '<script>
							$(document).ready(function() {
								$("#'.$field_opts['name'].'").select2();
							});
						</script>';
                        break;
                }
            }
            $form = form_dropdown($field_opts['name'], $values, $field['value'], 'class="form-control'.$extraClass.'" id="'.$field_opts['name'].'"'.$extras);
        }

        $field_array[$field_opts['name']]['label'] = $script.$label;
        $field_array[$field_opts['name']]['form'] = $form;
    }

    protected function build_fields($model_fields, $registro = null, $readonly = false)
    {
        $fields = array();
        foreach ($model_fields as $name => $field) {
            if ($readonly) {
                $field['disabled'] = true;
                if (!isset($field['type']) || $field['type'] !== 'multiple') {
                    unset($field['input_type']);
                    unset($field['array']);
                    unset($field['value']);
                }
                unset($field['required']);
            }
            $field['name'] = $name;
            if (empty($field['input_type'])) {
                $this->add_input_field($fields, $field, isset($registro) ? $registro->{$name} : null);
            } elseif ($field['input_type'] == 'combo') {
                if (isset($field['id_name'])) {
                    $this->add_combo_field($fields, $field, isset($registro) ? $registro->{$field['id_name']} : null);
                } else {
                    $this->add_combo_field($fields, $field, isset($registro) ? $registro->{"{$name}_id"} : null);
                }
            }
        }

        return $fields;
    }

    protected function get_date_sql($post = 'fecha', $src_format = 'd/m/Y', $dst_format = 'Y-m-d')
    {
        if ($this->input->post($post)) {
            $fecha = DateTime::createFromFormat($src_format, $this->input->post($post));
            $fecha_sql = date_format($fecha, $dst_format);
        } else {
            $fecha_sql = 'NULL';
        }

        return $fecha_sql;
    }

    protected function get_datetime_sql($post = 'fecha', $src_format = 'd/m/Y H:i', $dst_format = 'Y-m-d H:i:s')
    {
        return $this->get_date_sql($post, $src_format, $dst_format);
    }

    protected function load_template($view = 'general_content', $view_data = null, $data = array())
    {
        $view_data['controlador'] = $this->router->fetch_class();
        $view_data['metodo'] = $this->router->fetch_method();
        $usuario = array(
            'first_name' => $this->session->userdata('first_name'),
            'last_name' => $this->session->userdata('last_name'),
            'last_login' => $this->session->userdata('last_login'),
            'group_selected' => $this->user_groups_asociative($this->session->userdata('user_groups')),
            'user_groups' => $this->session->userdata('user_groups'),
        );
        $data['menu_collapse'] = $this->session->userdata('menu_collapse');
        $data['header'] = $this->load->view('general_header', $usuario, true);
        $data['sidebar'] = $this->load->view('general_sidebar', $usuario, true);
        $data['content'] = $this->load->view($view, $view_data, true);
        $data['footer'] = $this->load->view('general_footer', '', true);
        $this->load->view('general_template', $data);
    }

    protected function user_groups_asociative($userdata_groups)
    {
        foreach ($userdata_groups as $user_group) {
            $grupos[] = $user_group;
        }

        return $grupos[0];
    }

    protected function modal_error($error_msg = '', $error_title = 'Error general')
    {
        $data['error_msg'] = $error_msg;
        $data['error_title'] = $error_title;
        $this->load->view('errors/html/error_modal', $data);
    }

    protected function exportar_excel($atributos, $campos, $registros)
    {
        $this->load->library('PHPExcel');
        $this->phpexcel->getProperties()->setTitle($atributos['title'])->setDescription('');
        $this->phpexcel->setActiveSheetIndex(0);

        $sheet = $this->phpexcel->getActiveSheet();
        $sheet->setTitle(substr($atributos['title'], 0, 30));
        $encabezado = array();
        $ultima_columna = 'A';
        foreach ($campos as $columna => $campo) {
            $encabezado[] = $campo[0];
            $sheet->getColumnDimension($columna)->setWidth($campo[1]);
            $ultima_columna = $columna;
        }

        $sheet->getStyle('A1:'.$ultima_columna.'1')->getFont()->setBold(true);

        $sheet->fromArray(array($encabezado), null, 'A1');
        $sheet->fromArray($registros, null, 'A2');

        header('Content-Type: application/vnd.ms-excel');
        $nombreArchivo = $atributos['title'];
        header("Content-Disposition: attachment; filename=\"$nombreArchivo.xls\"");
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $writer->save('php://output');
        exit;
    }
}
/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */
