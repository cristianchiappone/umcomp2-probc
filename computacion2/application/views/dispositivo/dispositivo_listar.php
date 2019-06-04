<script>
    var dispositivo_table;
    function complete_dispositivo_table() {
        agregar_filtros('dispositivo_table', dispositivo_table, 5);
    }
</script>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Dispositivos</h1>
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <a class="btn bg-blue btn-app btn-app-zetta" href="dispositivo/modal_agregar" data-remote="false" data-toggle="modal" data-target="#remote_modal" title="Agregar dispositivo">
                                        <i class="fa fa-plus"></i>&nbsp; Agregar Dispositivo &nbsp;
                                    </a>
                                </div>
                            </div>
                            <hr style="margin: 10px 0;">
                            <?php echo $js_table; ?>
                            <?php echo $html_table; ?>	
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>