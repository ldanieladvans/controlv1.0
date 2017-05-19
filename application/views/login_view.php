<?php
header("Content-type: text/html; charset=utf-8");
if ($this->session->flashdata('informacion')) {
    $msg = '<div class="alert alert-' . ($this->session->flashdata('class') ? $this->session->flashdata('class') : 'danger') . '" role="alert">' . $this->session->flashdata('informacion') . '</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= $titulo; ?></title>
        <script src="<?= base_url(); ?>resources/controln/jquery/jquery-1.11.0.min.js"></script>
        <script src="<?= base_url(); ?>resources/controln/bootstrap-3.3.5/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="<?= base_url(); ?>resources/controln/bootstrap-3.3.5/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?= base_url(); ?>resources/controln/css/advans.css">
        <link rel="stylesheet" href="<?= base_url(); ?>resources/controln/css/advans2.css">
    </head>
    <body>
        <div id="loginModal" class="modal show" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="text-center">Iniciar sesión</h1>
                    </div>
                    <div class="modal-body">
                        <?= isset($msg) ? $msg : ""; ?>
                        <form method="post" action="<?= base_url(); ?>login/iniciar_sesion" class="form col-md-12 center-block" style="float:none !important;">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div class="form-group">
                                <?php echo form_error('rfc'); ?>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-star"></i></span>
                                    <input type="text" name="rfc" class="form-control input-lg" placeholder="RFC de la Cuenta" value="<?= isset($r) ? $r['rfc'] : ''; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo form_error('usuario'); ?>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                                    <input type="text" name="usuario" class="form-control input-lg" placeholder="Correo electrónico" value="<?= isset($r) ? $r['usuario'] : ''; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo form_error('contrasena'); ?>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-asterisk"></i></span>
                                    <input type="password" name="contrasena" class="form-control input-lg" placeholder="Contraseña">
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Iniciar</button>
                                <input type="hidden" name="token" value="<?= $token; ?>">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-6">
                            <span class="pull-left"><a href="<?= base_url() ?>login/recuperarContrasena">¿Olvidó su contraseña?</a></span>
                        </div>
                        <div class="col-md-6">
                            <span><a href="#">¿Necesitas ayuda?</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>