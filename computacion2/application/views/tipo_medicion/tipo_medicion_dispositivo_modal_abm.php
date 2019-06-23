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
            <?php echo $fields['descripcion']['label']; ?>
            <?php echo $fields['descripcion']['form']; ?>
        </div>
        <div class="form-group col-md-12">
            <?php echo $fields['tipos_medicion']['label']; ?>
            <?php echo $fields['tipos_medicion']['form']; ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo isset($txt_btn) ? 'Cancelar' : 'Cerrar'; ?></button>
    <?php echo (!empty($txt_btn)) ? form_submit(array('class' => "btn $btn_color  pull-right", 'title' => $txt_btn), $txt_btn) : ''; ?>
    <?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar' || $txt_btn === 'Dar de baja') ? form_hidden('id', $dispositivo->id) : ''; ?>
</div>
<?php echo form_close(); ?>
<script>
    $(document).ready(function () {
        agregar_eventos($('#remote_modal form'));
    });
</script>