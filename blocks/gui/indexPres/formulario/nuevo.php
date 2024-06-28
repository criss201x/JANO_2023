<?php
$esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

$rutaBloque = $this->miConfigurador->getVariableConfiguracion("host");
$rutaBloque .= $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/";
$rutaBloque .= $esteBloque ['grupo'] . "/" . $esteBloque ['nombre'];

$directorio = $this->miConfigurador->getVariableConfiguracion("host");
$directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
$directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");
?>


<div id="slider1_container" style="position: relative; top: 0px; left: 0px; width: 1800px; height: 500px; overflow: hidden;">
    <!-- Slides Container -->
    <div u="slides" style="cursor: move; position: absolute; overflow: hidden; left: 0px; top: 0px; width: 1800px; height: 500px; overflow: hidden;">

        <div><img u="image" src="<?php echo $rutaBloque ?>/images/asab.jpg" /></div>
        <div><img u="image" src="<?php echo $rutaBloque ?>/images/cienciased1.jpg" /></div>
        <div><img u="image" src="<?php echo $rutaBloque ?>/images/cienciased2.jpg" /></div>
        <div><img u="image" src="<?php echo $rutaBloque ?>/images/fcmn.jpg" /></div>
        <div><img u="image" src="<?php echo $rutaBloque ?>/images/ingenieria.jpg" /></div>
                <div><img u="image" src="<?php echo $rutaBloque ?>/images/paiba1.jpg" /></div>
        <div><img u="image" src="<?php echo $rutaBloque ?>/images/paiba2.jpg" /></div>
        <div><img u="image" src="<?php echo $rutaBloque ?>/images/porvenir.jpg" /></div>
        <div><img u="image" src="<?php echo $rutaBloque ?>/images/vivero2.jpeg" /></div>
        <div><img u="image" src="<?php echo $rutaBloque ?>/images/tecno.jpg" /></div>

    </div>
</div>