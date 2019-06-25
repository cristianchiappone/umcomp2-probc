<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Datatables Helper
 *
 * @package    CodeIgniter
 * @subpackage helpers
 * @category   helper
 * @version    1.1.4
 * @author     ZettaSys <info@zettasys.com.ar>
 *
 */
if (!function_exists('buildJS'))
{

	function buildJS($tableData)
	{
		$CI = & get_instance();

		$columns = 'columns: [';
		$columnDefs = 'columnDefs: [';
		$columnCount = 0;
		$filters = '';
		if (isset($tableData['columns']))
		{
			foreach ($tableData['columns'] as $Property)
			{
				$className = isset($Property['class']) ? ', "className": "' . $Property['class'] . '"' : '';
				if (!isset($Property['render']))
				{
					$render = '';
				}
				elseif ($Property['render'] === 'money')
				{
					$render = ', "render": function (data, type, full, meta) { if(type === "display"){if(data){data = "$ " + data.toString().replace(".", ",");}}return data;}';
				}
				elseif ($Property['render'] === 'date')
				{
					$format = isset($Property['format']) ? $Property['format'] : 'DD/MM/YYYY';
					$render = ', "render": function (data, type, full, meta) { if(type === "display"){if(data){var mDate = moment(data);data = (mDate && mDate.isValid()) ? mDate.format("' . $format . '") : "";}}return data;}';
				}
				elseif ($Property['render'] === 'datetime')
				{
					$render = ', "render": function (data, type, full, meta) { if(type === "display"){if(data){var mDate = moment(data);data = (mDate && mDate.isValid()) ? mDate.format("DD/MM/YYYY HH:mm") : "";}}return data;}';
				}
				elseif ($Property['render'] === 'fulldatetime')
				{
					$render = ', "render": function (data, type, full, meta) { if(type === "display"){if(data){var mDate = moment(data);data = (mDate && mDate.isValid()) ? mDate.format("DD/MM/YYYY HH:mm:ss") : "";}}return data;}';
				}
				else
				{
					$render = ', "render": ' . $Property['render'];
				}
				$visible = isset($Property['visible']) ? ', "visible": ' . $Property['visible'] : '';
				$searchable = isset($Property['searchable']) ? ', "searchable": ' . $Property['searchable'] : '';
				$sortable = isset($Property['sortable']) ? ', "sortable": ' . $Property['sortable'] : '';
				$orderData = isset($Property['orderData']) ? ', "orderData": ' . json_encode($Property['orderData']) : '';
				$columns .= '{"data": "' . $Property['data'] . '"},';
				$columnDefs .= '{'
					. '"targets": ' . $columnCount . ', '
					. '"width": "' . $Property['width'] . '%"'
					. $className . $render . $visible . $searchable . $sortable . $orderData
					. '}, ';
				$filters .= isset($Property['filter_name']) ? '$("#' . $Property['filter_name'] . '").change(function() {var key = $(this).find(\'option:selected\').val(); var val = this.options[this.selectedIndex].text; ' . $tableData['table_id'] . '.column(' . $columnCount . ').search(key !== "Todos" ? key : "").draw(); });' . "\n" . '$("#' . $Property['filter_name'] . '").trigger("change");' . "\n" : '';
				$columnCount++;
			}
		}
		$columns .= '],';
		$columnDefs .= '],';

		$tableJS = '<script type="text/javascript">';
		$tableJS .= '$(document).ready(function() {' . "\n";
		$tableJS .= '$.fn.dataTable.moment("DD/MM/YYYY");';
		$tableJS .= (isset($tableData['reuse_var']) && $tableData['reuse_var']) ? '' : 'var ';
		$tableJS .= $tableData['table_id'] . ' = $("#' . $tableData['table_id'] . '").DataTable({';
		if (isset($tableData['paging']))
		{
			$tableJS .= 'paging: ' . $tableData['paging'] . ', ';
		}
		if (isset($tableData['scrollY']))
		{
			$tableJS .= 'scrollY: ' . $tableData['scrollY'] . ', ';
		}
		if (isset($tableData['scrollCollapse']))
		{
			$tableJS .= 'scrollCollapse: \'' . $tableData['scrollCollapse'] . 'px\', ';
		}
		if (isset($tableData['order']))
		{
			$tableJS .= 'order: ' . json_encode($tableData['order']) . ', ';
		}
		if (isset($tableData['fnHeaderCallback']))
		{
			$tableJS .= 'fnHeaderCallback: ' . str_replace('"', '', json_encode($tableData['fnHeaderCallback'], JSON_UNESCAPED_SLASHES)) . ',';
		}
		if (isset($tableData['fnRowCallback']))
		{
			$tableJS .= 'fnRowCallback: ' . str_replace('"', '', json_encode($tableData['fnRowCallback'], JSON_UNESCAPED_SLASHES)) . ',';
		}
		if (isset($tableData['initComplete']))
		{
			$tableJS .= 'initComplete: ' . str_replace('"', '', json_encode($tableData['initComplete'], JSON_UNESCAPED_SLASHES)) . ',';
		}
		if (isset($tableData['disableLengthChange']) && $tableData['disableLengthChange'])
		{
			$tableJS .= 'lengthChange: false,';
		}
		if (isset($tableData['disableSearching']) && $tableData['disableSearching'])
		{
			$tableJS .= 'searching: false,';
		}
		if (isset($tableData['disablePagination']) && $tableData['disablePagination'])
		{
			$tableJS .= 'bPaginate: false, ';
		}
		if (isset($tableData['dom']))
		{
			$tableJS .= 'dom: \'' . $tableData['dom'] . '\', ';
		}
		if (isset($tableData['pageLength']))
		{
			$tableJS .= 'pageLength: ' . $tableData['pageLength'] . ', ';
		}
		else
		{
			$tableJS .= 'pageLength: 10, ';
		}
		if (isset($tableData['aoSearchCols']))
		{
			$tableJS .= 'aoSearchCols: ' . $tableData['aoSearchCols'] . ', ';
		}
		$tableJS .= 'processing: true, '
			. 'stateSave: true, '
			. 'serverSide: true, '
			. 'autoWidth: false, '
			. 'pagingType: "simple_numbers", '
			. 'language: {"url": "plugins/datatables/spanish.json"}, ';

		if (isset($tableData['extraData']))
		{
			$data = 'data: function (d) {' . $tableData['extraData'] . 'd.' . $CI->security->get_csrf_token_name() . '= "' . $CI->security->get_csrf_hash() . '";}';
		}
		else
		{
			$data = 'data: {' . $CI->security->get_csrf_token_name() . ':"' . $CI->security->get_csrf_hash() . '"}';
		}

		$tableJS .= 'ajax: {'
			. 'url: "' . $tableData['source_url'] . '", '
			. 'type: "POST", '
			. $data . '}, ';

		$tableJS .= $columns;
		$tableJS .= $columnDefs;
		$tableJS .= 'colReorder: true';
		$tableJS .= '});' . "\n";
		$tableJS .= $filters;
		if (isset($tableData['footer']) && $tableData['footer'])
		{
			$tableJS .= "
						$('#{$tableData['table_id']} tfoot th').each(function() {
							var title = $(this).text();
							if(title!=='')
								$(this).html('<input style=\"width: 100%;\" type=\"text\" placeholder=\"'+title+'\" />');
						});
						{$tableData['table_id']}.columns().every(function() {
							var that = this;
							$('input', {$tableData['table_id']}.table().footer().children[0].children[this[0][0]]).on('change', function() {
								if (that.search() !== this.value) {
									that.search(this.value).draw();
								}
							});
						});";
		}
		$tableJS .= '});';
		$tableJS .= '</script>';
		return $tableJS;
	}
}

if (!function_exists('buildHTML'))
{

	function buildHTML($tableData)
	{
		$tableHTML = '<table id="' . $tableData['table_id'] . '" class="table table-hover table-bordered table-sm dt-responsive">'; // nowrap
		$tableHTML .= '<thead>';
		$tableHTML .= '<tr>';
		if (isset($tableData['columns']))
		{
			foreach ($tableData['columns'] as $Column)
			{
				$class = empty($Column['responsive_class']) ? '' : ' class="' . $Column['responsive_class'] . '"';
				$priority = empty($Column['priority']) ? '' : ' data-priority="' . $Column['priority'] . '"';
				$tableHTML .= "<th$class$priority>";
				$tableHTML .= $Column['label'];
				$tableHTML .= "</th>";
			}
		}
		$tableHTML .= '</tr>';
		$tableHTML .= '</thead>';
		if (isset($tableData['footer']) && $tableData['footer'])
		{
			$tableHTML .= '<tfoot>';
			$tableHTML .= '<tr>';
			if (isset($tableData['columns']))
			{
				foreach ($tableData['columns'] as $Column)
				{
					$tableHTML .= "<th>";
					$tableHTML .= $Column['label'];
					$tableHTML .= "</th>";
				}
			}
			$tableHTML .= '</tr>';
			$tableHTML .= '</tfoot>';
		}
		$tableHTML .= '</table>';

		return $tableHTML;
	}
}