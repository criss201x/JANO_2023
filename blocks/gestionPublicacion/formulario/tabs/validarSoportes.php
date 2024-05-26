<?php

if (isset($_REQUEST["validarSoportes"]) && $_REQUEST["validarSoportes"] == 'true') {
    $cadena_sql = $this->miSql->getCadenaSql("consultaValidacionSoporte", $consecutivo_soporte_ins);
    $resultadoValidacion = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

    $mostrarHtml .= "<td align='left' width='20%'>";
    $mostrarHtml .= $this->lenguaje->getCadena ("pregunta_validacion_soporte");
    $mostrarHtml .= '<br><br><div id="radioBtn" class="btn-group">';

    $valor = $resultadoValidacion ? $resultadoValidacion[0]["valido"] : 'f';
    $estiloActivo = 'btn btn-primary btn-sm active';
    $estiloInactivo = 'btn btn-primary btn-sm notActive';

    //-------------Enlace-----------------------
    $esteCampo = "enlace1";
    $atributos["id"]=$esteCampo;
    $atributos["toogle"]="validacion".$consecutivo_soporte_ins;
    $atributos["toogletitle"]="SI";
    $atributos['enlace']='';
    $atributos['tabIndex']=$esteCampo;
    $atributos['redirLugar']=false;
    $atributos['estilo']=$valor == 't' ? $estiloActivo : $estiloInactivo;
    $atributos['enlaceTexto']='SI';
    $atributos['ancho']='30';
    $atributos['alto']='30';
    $mostrarHtml .= $this->miFormulario->enlace($atributos);
    unset($atributos);
    //----------------------------------------

    //-------------Enlace-----------------------
    $esteCampo = "enlace2";
    $atributos["id"]=$esteCampo;
    $atributos["toogle"]="validacion".$consecutivo_soporte_ins;
    $atributos["toogletitle"]="NO";
    $atributos['enlace']='';
    $atributos['tabIndex']=$esteCampo;
    $atributos['redirLugar']=false;
    $atributos['estilo']=$valor == 'f' ? $estiloActivo : $estiloInactivo;
    $atributos['enlaceTexto']='NO';
    $atributos['ancho']='30';
    $atributos['alto']='30';
    $mostrarHtml .= $this->miFormulario->enlace($atributos);
    unset($atributos);

    //----------------------------------------
    $mostrarHtml .= '</div>';

    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
    $esteCampo = 'observacion';
    $atributos ['id'] = $esteCampo.$consecutivo_soporte_ins;
    $atributos ['nombre'] = $esteCampo;
    $atributos ['tipo'] = 'text';
    //$atributos ['estilo'] = 'jqueryui';
    $atributos ['marco'] = true;
    $atributos ['estiloMarco'] = '';
    $atributos ["etiquetaObligatorio"] = true;
    $atributos ['columnas'] = 20;
    $atributos ['filas'] = 2;
    $atributos ['dobleLinea'] = 0;
    $atributos ['tabIndex'] = $tab;
    $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
    $atributos ['validar'] = 'required, minSize[4], maxSize[3000]';
    $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
    $atributos ['deshabilitado'] = false;
    $atributos ['tamanno'] = 60;
    $atributos ['maximoTamanno'] = '';
    $atributos ['anchoEtiqueta'] = 170;
    $atributos ['valor'] = $resultadoValidacion ? $resultadoValidacion[0]["observacion"] : '';
    $tab ++;
            
    // Aplica atributos globales al control
    $atributos = array_merge ( $atributos, $atributosGlobales );
    $mostrarHtml .= $this->miFormulario->campoTextArea ( $atributos );
    unset ( $atributos );
    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------

    // Hidden para guardar la validacion
    // ////////////////Hidden////////////
    $esteCampo = 'validacion'.$consecutivo_soporte_ins;
    $atributos ["id"] = $esteCampo;
    $atributos ["tipo"] = "hidden";
    $atributos ['estilo'] = '';
    $atributos ['validar'] = 'required';
    $atributos ["obligatorio"] = true;
    $atributos ['marco'] = true;
    $atributos ["etiqueta"] = "";
    $atributos ['valor'] = $resultadoValidacion ? $resultadoValidacion[0]["validacion"] : 'false';
    $atributos = array_merge ( $atributos, $atributosGlobales );
    $mostrarHtml .= $this->miFormulario->campoCuadroTexto ( $atributos );
    unset ( $atributos );

    $mostrarHtml .= "</td>";
}

if (isset($_REQUEST["mostrarValidacion"]) && $_REQUEST["mostrarValidacion"] == 'true') {
    $cadena_sql = $this->miSql->getCadenaSql("consultaValidacionSoporte", $consecutivo_soporte_ins);
    $resultadoValidacion = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");
    $mostrarHtml .= "<td align='left' width='20%'><b>";
    $mostrarHtml .= $this->lenguaje->getCadena ("pregunta_validacion_soporte");
    $mostrarHtml .= ':</b> <br>';

	$valor = $resultadoValidacion ? $resultadoValidacion[0]["valido"] : 'f';

    $mostrarHtml .= $valor == 't' ? 'SI' : 'NO';
    $mostrarHtml .= "<br><br>";
    $mostrarHtml .= "<b>Observaciones: </b><br>" . $resultadoValidacion[0]["observacion"];
    $mostrarHtml .= "</td>";
}

?>

