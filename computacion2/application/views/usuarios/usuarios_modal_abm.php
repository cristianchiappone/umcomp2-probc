<?php echo form_open(uri_string(), array('data-toggle' => 'validator')); ?>
<div class="modal-header">
	<h4 class="modal-title" id="myModalLabel"><?php echo $title; ?></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
<div class="modal-body">
	<div class="row">
		<div class="form-group col-md-12">
			<?php echo $fields['username']['label']; ?>
			<?php echo $fields['username']['form']; ?>
		</div>
		<div class="form-group col-md-12">
			<?php echo $fields['email']['label']; ?>
			<?php echo $fields['email']['form']; ?>
		</div>
		<div class="form-group col-md-6">
			<?php echo $fields['first_name']['label']; ?>
			<?php echo $fields['first_name']['form']; ?>
		</div>
		<div class="form-group col-md-6">
			<?php echo $fields['last_name']['label']; ?>
			<?php echo $fields['last_name']['form']; ?>
		</div>
		<div class="form-group col-md-12">
			<?php echo $fields['password']['label']; ?>
			<?php echo $fields['password']['form']; ?>
		</div>
		<?php if (isset($fields_m)):?>
		<div class="form-group col-md-12">
			<?php echo $fields_m['group']['label']; ?>
			<?php echo $fields_m['group']['form']; ?>
		</div>
		<?php endif; ?>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo isset($txt_btn) ? 'Cancelar' : 'Cerrar'; ?></button>
	<?php echo (!empty($txt_btn)) ? form_submit(array('class' => "btn $btn_color  pull-right", 'title' => $txt_btn), $txt_btn) : ''; ?>
	<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar' || $txt_btn === 'Dar de baja') ? form_hidden('id', $usuario->id) : ''; ?>
</div>
<?php echo form_close(); ?>
<script>
	$(document).ready(function() {
		agregar_eventos($('#remote_modal form'));
	});
</script>