<?php

$indice = 0;

$estilo[$indice++] = "ui.jqgrid.css";
$estilo[$indice++] = "ui.multiselect.css";
$estilo[$indice++] = "timepicker.css";
$estilo[$indice++] = "validationEngine.jquery.css";
$estilo[$indice++] = "jquery.auto-complete.css";
$estilo[$indice++] = "select2.css";

// Tablas
$estilo[$indice++] = "jquery.dataTables.css";
$estilo[$indice++] = "jquery.dataTables_themeroller.css";

// Bootstrap
$estilo[$indice++] = "jquery.dataTables.css";

$estilo[$indice++] = "miestilo.css";

$estilo[$indice++]="accordion.css";
$estilo[$indice++]="animate.min.css";



$rutaBloque = $this->miConfigurador->getVariableConfiguracion("host");
$rutaBloque.=$this->miConfigurador->getVariableConfiguracion("site");

if ($unBloque["grupo"] == "") {
    $rutaBloque.="/blocks/" . $unBloque["nombre"];
} else {
    $rutaBloque.="/blocks/" . $unBloque["grupo"] . "/" . $unBloque["nombre"];
}

foreach ($estilo as $nombre) {
    echo "<link rel='stylesheet' type='text/css' href='" . $rutaBloque . "/css/" . $nombre . "'>\n";
}
?>
