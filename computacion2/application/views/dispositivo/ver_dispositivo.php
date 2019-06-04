<script>
    var dispositivo_medicion_table;
    function complete_dispositivo_medicion_table() {
        agregar_filtros('dispositivo_medicion_table', dispositivo_medicion_table, 0);
    }
</script>
<style>
    #card-info {
        height: 195px;
    }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">
                        Dispositivo: <?= "$dispositivo->descripcion #$dispositivo->id"; ?>
                        <a class="btn btn-xs btn-primary" href="dispositivo/modal_enviar_notificacion/<?= $dispositivo->id; ?>" data-remote="false" data-toggle="modal" data-target="#remote_modal" title="Notificación">
                            <i class="fa fa-send"></i> Notificación 
                        </a>
                    </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo $controlador; ?>">Dispositivos</a></li>
                        <li class="breadcrumb-item active"><?php echo ucfirst($metodo); ?></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($message)) : ?>
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i> OK!</h4>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <div class="card" id="card-info">
                        <div class="card-header">
                            <h3 class="card-title">Información del Dispositivo</h3> 

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-widget="collapse">
                                    <i class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-widget="remove">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <?php echo $fields['descripcion']['label']; ?>
                                    <?php echo $fields['descripcion']['form']; ?>
                                </div>
                                <div class="col-sm-3">
                                    <?php echo $fields['fecha_alta']['label']; ?>
                                    <?php echo $fields['fecha_alta']['form']; ?>
                                </div>
                                <div class="col-sm-6">
                                    <?php echo $fields_m['tipos_medicion']['label']; ?>
                                    <div class="input-group">
                                        <?php echo $fields_m['tipos_medicion']['form']; ?>
                                        <span class="input-group-btn">
                                            <a class="btn btn-default" title="Agregar tipo medición" data-remote="false" data-toggle="modal" data-target="#remote_modal" href="tipo_medicion/modal_agregar_tipo_medicion/<?= $dispositivo->id ?>"><i class="fa fa-pencil"></i></a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (isset($mediciones) && !empty($mediciones)): ?> 
                    <?php foreach ($mediciones as $medicion): ?>
                        <div class="col-sm-2">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><?= $medicion->descripcion; ?></h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-widget="collapse">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-widget="remove">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <input id="knob_<?= $medicion->id; ?>"type="text" value="0" data-width="90" data-height="100" class="dial_<?= $medicion->id; ?>" data-fgColor="<?= $medicion->color_linea; ?>" data-min="<?= $medicion->min; ?>" data-max="<?= $medicion->max; ?>">
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Mediciones</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-widget="collapse">
                                    <i class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-widget="remove">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body text-sm">
                            <hr style="margin: 10px 0;">
                            <?php echo $js_table; ?>
                            <?php echo $html_table; ?>	
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <?php if (isset($mediciones) && !empty($mediciones)): ?> 
                    <?php foreach ($mediciones as $medicion): ?>
                        <div class="col-sm-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><?= $medicion->descripcion; ?></h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-widget="collapse">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-widget="remove">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body p-0">
                                    <canvas id="line-chart<?= $medicion->id; ?>" width="800" height="450"></canvas>
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </section>
</div>
<script>
    $(document).ready(function () {
        $(function () {
<?php if (isset($mediciones) && !empty($mediciones)): ?>
    <?php foreach ($mediciones as $medicion): ?>
                    $(".dial_<?= $medicion->id; ?>").knob();
    <?php endforeach; ?>
<?php endif; ?>
        });
<?php if (isset($mediciones) && !empty($mediciones)): ?>
    <?php foreach ($mediciones as $medicion): ?>
                new Chart(document.getElementById("line-chart<?= $medicion->id; ?>"), {
                    type: 'line',
                    data: {
                        datasets: [{
                                label: '<?= $medicion->descripcion; ?>',
                                data: [<?= $medicion->valores; ?>],
                                borderColor: '<?= $medicion->color_linea; ?>',
                                backgroundColor: 'transparent',
                                pointBackgroundColor: 'rgba(255,150,0,0.5)',
                            }
                        ],
                        labels: ['Tiempo']
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                    ticks: {
                                        suggestedMin: <?= $medicion->min; ?>,
                                        suggestedMax:<?= $medicion->max; ?>
                                    }
                                }]
                        }
                    }
                });
    <?php endforeach; ?>
<?php endif; ?>
    });
    setTimeout(fetchdata, 10000);
    function fetchdata() {
        $.ajax({
            url: 'dispositivo/ajax_get_ultima_medicion/',
            type: 'POST',
            data: {
                dispositivo_id: <?= $dispositivo->id; ?>,
                tipos: <?= $dispositivo->tipos; ?>
            },
            success: function (data) {
                var mediciones = data;
                $.each(JSON.parse(mediciones), function () {
                    $.each(this, function (name, value) {
                        $("input.dial_" + name).val(value);
                    });
                });
            },
            complete: function (data) {
                setTimeout(fetchdata, 7000);
            }
        });
    }
</script>
