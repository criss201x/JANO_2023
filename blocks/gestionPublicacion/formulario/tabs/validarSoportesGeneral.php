<?php

if (isset($_REQUEST["validarSoportes"]) && $_REQUEST["validarSoportes"] == 'true') {
    array_push($columnas, $this->lenguaje->getCadena ("validacion_soporte"));

	$id = $esteCampo;

	$parametro = array(
		"consecutivo_inscrito" => $_REQUEST["consecutivo_inscrito"],
		"tipo_dato" => $esteCampo
	);
	$cadena_sql = $this->miSql->getCadenaSql("consultaValidacionTipoSoporte", $parametro);
	$resultadoValidacionTipo = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

    echo "<div style ='width: 100%; padding-left: 12%; padding-right: 12%;' class='cell-border'>";
    echo "<table id='tablaValidacion' class='table table-striped table-bordered'>";
    echo "<tbody>";

    $mostrarHtml = "<tr align='center'>".
                    "<th >Validar</th>
                    <td colspan='3'>"."¿El aspirante habilita este criterio?".'<div><br>';
    $mostrarHtml .= '<div id="radioBtn" class="btn-group">';

	$valor = $resultadoValidacionTipo ? $resultadoValidacionTipo[0]["valido"] : 'f';
	$estiloActivo = 'btn btn-primary btn-sm active';
	$estiloInactivo = 'btn btn-primary btn-sm notActive';

    //-------------Enlace-----------------------
    $esteCampo = "enlace1";
    $atributos["id"]=$esteCampo;
    $atributos["toogle"]="validar$id";
    $atributos["toogletitle"]="SI";
    $atributos['enlace']='';
    $atributos['tabIndex']=$esteCampo;
    $atributos['redirLugar']=false;
    $atributos['estilo']= $valor == 't' ? $estiloActivo : $estiloInactivo;
    $atributos['enlaceTexto']='SI';
    $atributos['ancho']='30';
    $atributos['alto']='30';
    $mostrarHtml .= $this->miFormulario->enlace($atributos);
    unset($atributos);
    //----------------------------------------

    //-------------Enlace-----------------------
    $esteCampo = "enlace2";
    $atributos["id"]=$esteCampo;
    $atributos["toogle"]="validar$id";
    $atributos["toogletitle"]="NO";
    //$atributos['enlace']=$variableEditar;
    $atributos['tabIndex']=$esteCampo;
    $atributos['redirLugar']=false;
    $atributos['estilo']=$valor == 'f' ? $estiloActivo : $estiloInactivo;
    $atributos['enlaceTexto']='NO';
    $atributos['ancho']='30';
    $atributos['alto']='30';
    $mostrarHtml .= $this->miFormulario->enlace($atributos);
    unset($atributos);
    //----------------------------------------

    // Hidden para guardar la validacion
    $esteCampo = 'validar'.$id;
    $atributos ["id"] = $esteCampo;
    $atributos ["tipo"] = "hidden";
    $atributos ['estilo'] = '';
    $atributos ['validar'] = 'required';
    $atributos ["obligatorio"] = true;
    $atributos ['marco'] = true;
    $atributos ["etiqueta"] = "";
	$atributos ['valor'] = $resultadoValidacionTipo ? $resultadoValidacionTipo[0]["validacion"] : 'false';
    $atributos = array_merge ( $atributos, $atributosGlobales );
    $mostrarHtml .= $this->miFormulario->campoCuadroTexto ( $atributos );
    unset ( $atributos );

    $mostrarHtml .= '</div>';

    $mostrarHtml .='</div>'."</td>"."</tr>";

    echo $mostrarHtml;
    unset($mostrarHtml);
    echo "</tbody>";
    echo "</table>";

    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
    $esteCampo = 'observaciones';
    $atributos ['id'] = $esteCampo.$id;
    $atributos ['nombre'] = $esteCampo;
    $atributos ['tipo'] = 'text';
    $atributos ['estilo'] = 'jqueryui';
    $atributos ['marco'] = true;
    $atributos ['estiloMarco'] = '';
    $atributos ["etiquetaObligatorio"] = true;
    $atributos ['columnas'] = 130;
    $atributos ['filas'] = 4;
    $atributos ['dobleLinea'] = 0;
    $atributos ['tabIndex'] = $tab;
    $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
    $atributos ['validar'] = 'required, minSize[4], maxSize[3000]';
    $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
    $atributos ['deshabilitado'] = false;
    $atributos ['tamanno'] = 60;
    $atributos ['maximoTamanno'] = '';
    $atributos ['anchoEtiqueta'] = 100;
    $atributos ['valor'] = $resultadoValidacionTipo ? $resultadoValidacionTipo[0]["observacion"] : '';
    $tab ++;
    // Aplica atributos globales al control
    $atributos = array_merge ( $atributos, $atributosGlobales );
    $mostrarHtml = $this->miFormulario->campoTextArea ( $atributos );
    unset ( $atributos );
    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------


    echo $mostrarHtml;
    unset($mostrarHtml);
	echo "</div>";

    // ------------------Division para los botones-------------------------
    $atributos ["id"] = "botones";
    $atributos ["estilo"] = "marcoBotones";
    echo $this->miFormulario->division ( "inicio", $atributos );
    unset  ( $atributos );
    {
        // -----------------CONTROL: Botón ----------------------------------------------------------------
        $esteCampo = 'botonGuardar';
        $atributos ["id"] = $esteCampo.$id;
        $atributos ["tabIndex"] = $tab;
        $atributos ["tipo"] = 'boton';
        // submit: no se coloca si se desea un tipo button genérico
        $atributos ['submit'] = true;
        $atributos ["estiloMarco"] = '';
        $atributos ["estiloBoton"] = 'jqueryui';
        // verificar: true para verificar el formulario antes de pasarlo al servidor.
        $atributos ["verificar"] = '';
        $atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
        $atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
        $atributos ['nombreFormulario'] = $id;
        $tab ++;
        // Aplica atributos globales al control
        $atributos = array_merge ( $atributos, $atributosGlobales );
        echo $this->miFormulario->campoBoton ( $atributos );
        unset ( $atributos );
        // -----------------FIN CONTROL: Botón -----------------------------------------------------------
    }
    echo $this->miFormulario->division ( 'fin' );

    echo '<br><br>';
}

if (isset($_REQUEST["mostrarValidacion"]) && $_REQUEST["mostrarValidacion"] == 'true') {
	array_push($columnas, $this->lenguaje->getCadena ("validacion_soporte"));

	$id = $esteCampo;

	$parametro = array(
		"consecutivo_inscrito" => $_REQUEST["consecutivo_inscrito"],
		"tipo_dato" => $esteCampo
	);
	$cadena_sql = $this->miSql->getCadenaSql("consultaValidacionTipoSoporte", $parametro);
	$resultadoValidacionTipo = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

    echo "<div style ='width: 100%; padding-left: 12%; padding-right: 12%;' class='cell-border'>";
    echo "<table id='tablaValidacion' class='table table-striped table-bordered'>";
    echo "<tbody>";

    $mostrarHtml = "<tr align='center'>".
                    "<th >Validar</th>
                    <td colspan='3'>"."¿El aspirante habilita  este criterio?".'<div><br>';

	$valor = $resultadoValidacionTipo ? $resultadoValidacionTipo[0]["valido"] : 'f';

    $mostrarHtml .= $valor == 'f' ? 'NO' : 'SI';

    $mostrarHtml .='</div>'."</td>"."</tr>";
    $mostrarHtml .= "<tr align='center'>".
                    "<th >Observaciones</th>
                    <td colspan='3'>";
    $mostrarHtml .= $resultadoValidacionTipo[0]["observacion"];
    $mostrarHtml .="</td>"."</tr>";

    echo $mostrarHtml;
    unset($mostrarHtml);
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
}

?>

