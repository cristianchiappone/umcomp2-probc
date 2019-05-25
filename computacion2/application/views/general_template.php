<!DOCTYPE html>
<html lang="es">
	<head>
		<base href="<?php echo base_url(); ?>" />
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title><?php echo empty($title) ? TITLE : $title; ?></title>
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<meta name="description" content="">
		<meta name="theme-color" content="#5490cd">
		<link rel="shortcut icon" href="favicon.png" type="image/x-icon" />
		<!-- Font -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
		<!-- Bootstrap -->
		<link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">
		<!-- Font Awesome Icons -->
		<link rel="stylesheet" href="plugins/font-awesome/css/font-awesome.min.css" />
		<!-- Selectize -->		<!-- DatePicker -->
		<link rel="stylesheet" href="plugins/datepicker/datepicker3.css" />
		<!-- Theme style -->
		<link rel="stylesheet" href="dist/css/adminlte.min.css" />
		<!-- Ionicons -->
		<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
		<!-- DATA TABLES -->
		<link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap4.css" />
		<link rel="stylesheet" href="plugins/datatables/extensions/Responsive/css/dataTables.responsive.css" />
		<link rel="stylesheet" href="plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css" />
		  <!-- Date Picker -->
		<link rel="stylesheet" href="plugins/datepicker/datepicker3.css">
		<!-- Daterange picker -->
		<link rel="stylesheet" href="plugins/daterangepicker/daterangepicker-bs3.css">
		  <!-- iCheck -->
		<link rel="stylesheet" href="plugins/iCheck/flat/blue.css">
		<!-- Google Font: Source Sans Pro -->
		<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

		<?php
		if (!empty($css)) {
			if (is_array($css)) {
				foreach ($css as $C) {
					if (substr($C, 0, 4) !== 'http') {
						echo '<link rel="stylesheet" href="' . auto_ver($C) . '">';
					} else {
						echo '<link rel="stylesheet" href="' . $C . '">';
					}
				}
			} else {
				if (substr($css, 0, 4) !== 'http') {
					echo '<link rel="stylesheet" href="' . auto_ver($css) . '">';
				} else {
					echo '<link rel="stylesheet" href="' . $css . '">';
				}
			}
		}
		?>

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

		<!-- jQuery -->
		<script src="plugins/jquery/jquery.min.js"></script>
		<!-- jQuery UI 1.11.4 -->
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
		<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
		<script>
			var CI = {'base_url': '<?php echo base_url(); ?>'};
		</script>
	</head>
	<body class="hold-transition skin-red sidebar-mini <?php echo empty($menu_collapse) ? '' : 'sidebar-collapse'; ?>">
		<div class="wrapper">
			<?php echo $header; ?>
			<?php echo $sidebar; ?>
			<?php echo $content; ?>
			<div class="modal fade" id="remote_modal" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
					</div>
				</div>
			</div>
			<?php echo $footer; ?>
		</div>
		<!-- Bootstrap 4 -->
		<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
		<script>
			moment.updateLocale('es', {
				months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
				monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"]
			});
		</script>
		<!-- daterangepicker -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
		<script src="plugins/daterangepicker/daterangepicker.js"></script>
		<!-- datepicker -->
		<script src="plugins/datepicker/bootstrap-datepicker.js"></script><!-- AdminLTE App -->
		<script src="dist/js/adminlte.js"></script>
		<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
		<script src="dist/js/pages/dashboard.js"></script>
		<!-- AdminLTE for demo purposes -->
		<script src="dist/js/demo.js"></script>
		<?php
		if (!empty($js)) {
			if (is_array($js)) {
				foreach ($js as $J) {
					if (substr($J, 0, 4) !== 'http') {
						echo '<script src="' . auto_ver($J) . '"></script>';
					} else {
						echo '<script src="' . $J . '"></script>';
					}
				}
			} else {
				if (substr($js, 0, 4) !== 'http') {
					echo '<script src="' . auto_ver($js) . '"></script>';
				} else {
					echo '<script src="' . $js . '"></script>';
				}
			}
		}
		?>
		<script>
			$(document).ready(function() {
				$(this).find('.box-body').find('input:not([readonly]), textarea, select')
								.not('input[type=hidden],input[type=button],input[type=submit],input[type=reset],input[type=image],button')
								.filter(':enabled:visible:first:not(.no-autofocus):not(.dateFormat):not(.dateTimeFormat)')
								.focus();
				$(this).find('.box-body').find('input:not([readonly]), textarea, select')
								.not('input[type=hidden],input[type=button],input[type=submit],input[type=reset],input[type=image],button')
								.filter(':enabled:visible:first:not(.no-autofocus):not(.dateFormat):not(.dateTimeFormat)')
								.select();
				$("body").on('collapsed.pushMenu', function(e) {
					set_menu_collapse(1);
				});
				$("body").on('expanded.pushMenu', function(e) {
					set_menu_collapse(0);
				});
				$("#remote_modal").on("show.bs.modal", function(e) {
					if (typeof e.relatedTarget !== 'undefined') {
						var link = $(e.relatedTarget);
						$(this).find(".modal-content").load(link.attr("href"));
					}
				});
				$('#remote_modal').on("hidden.bs.modal", function(e) {
					$(this).find(".modal-content").empty();
				});
			});
			function set_menu_collapse(val) {
				$.ajax({
					type: 'POST',
					url: 'ajax/set_menu_collapse',
					data: {value: val, <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'},
					dataType: 'json'
				});
			}
		</script>
		<script src="<?= auto_ver('js/main.js'); ?>" type="text/javascript"></script>
	</body>
</html>
