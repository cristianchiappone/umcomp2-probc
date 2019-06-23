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
        <?php if($txt_btn == "Crear"): ?>
        <div class="form-group col-md-12">
            <?php echo $fields_u['usuario']['label']; ?>
            <?php echo $fields_u['usuario']['form']; ?>
        </div>
        <?php endif; ?>
        <?php if($txt_btn != "Crear"): ?>
        <div class="form-group col-md-12">
            <?php echo $fields['fecha_alta']['label']; ?>
            <?php echo $fields['fecha_alta']['form']; ?>
        </div>
        <?php endif; ?>
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