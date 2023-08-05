<?php

$conexion = "contractual";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

$conexionFrameWork = "estructura";
$DBFrameWork = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexionFrameWork);

$conexionAgora = "agora";
$esteRecursoDBAgora = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexionAgora);

$conexionSICA = "sicapital";
$DBSICA = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexionSICA);

class EnLetras {

    var $Void = "";
    var $SP = " ";
    var $Dot = ".";
    var $Zero = "0";
    var $Neg = "Menos";

    function ValorEnLetras($x, $Moneda) {
        $s = "";
        $Ent = "";
        $Frc = "";
        $Signo = "";

        if (floatVal($x) < 0)
            $Signo = $this->Neg . " ";
        else
            $Signo = "";

        if (intval(number_format($x, 2, '.', '')) != $x) // <- averiguar si tiene decimales
            $s = number_format($x, 2, '.', '');
        else
            $s = number_format($x, 0, '.', '');

        $Pto = strpos($s, $this->Dot);

        if ($Pto === false) {
            $Ent = $s;
            $Frc = $this->Void;
        } else {
            $Ent = substr($s, 0, $Pto);
            $Frc = substr($s, $Pto + 1);
        }

        if ($Ent == $this->Zero || $Ent == $this->Void)
            $s = "Cero ";
        elseif (strlen($Ent) > 7) {
            $s = $this->SubValLetra(intval(substr($Ent, 0, strlen($Ent) - 6))) . "Millones " . $this->SubValLetra(intval(substr($Ent, - 6, 6)));
        } else {
            $s = $this->SubValLetra(intval($Ent));
        }

        if (substr($s, - 9, 9) == "Millones " || substr($s, - 7, 7) == "Millón ")
            $s = $s . "de ";

        $s = $s . $Moneda;

        if ($Frc != $this->Void) {
            $s = $s . " Con " . $this->SubValLetra(intval($Frc)) . "Centavos";
            // $s = $s . " " . $Frc . "/100";
        }
        return ($Signo . $s . "");
    }

    function SubValLetra($numero) {
        $Ptr = "";
        $n = 0;
        $i = 0;
        $x = "";
        $Rtn = "";
        $Tem = "";

        $x = trim("$numero");
        $n = strlen($x);

        $Tem = $this->Void;
        $i = $n;

        while ($i > 0) {
            $Tem = $this->Parte(intval(substr($x, $n - $i, 1) . str_repeat($this->Zero, $i - 1)));
            If ($Tem != "Cero")
                $Rtn .= $Tem . $this->SP;
            $i = $i - 1;
        }

        // --------------------- GoSub FiltroMil ------------------------------
        $Rtn = str_replace(" Mil Mil", " Un Mil", $Rtn);
        while (1) {
            $Ptr = strpos($Rtn, "Mil ");
            If (!($Ptr === false)) {
                If (!(strpos($Rtn, "Mil ", $Ptr + 1) === false))
                    $this->ReplaceStringFrom($Rtn, "Mil ", "", $Ptr);
                else
                    break;
            } else
                break;
        }

        // --------------------- GoSub FiltroCiento ------------------------------
        $Ptr = - 1;
        do {
            $Ptr = strpos($Rtn, "Cien ", $Ptr + 1);
            if (!($Ptr === false)) {
                $Tem = substr($Rtn, $Ptr + 5, 1);
                if ($Tem == "M" || $Tem == $this->Void)
                    ;
                else
                    $this->ReplaceStringFrom($Rtn, "Cien", "Ciento", $Ptr);
            }
        } while (!($Ptr === false));

        // --------------------- FiltroEspeciales ------------------------------
        $Rtn = str_replace("Diez Un", "Once", $Rtn);
        $Rtn = str_replace("Diez Dos", "Doce", $Rtn);
        $Rtn = str_replace("Diez Tres", "Trece", $Rtn);
        $Rtn = str_replace("Diez Cuatro", "Catorce", $Rtn);
        $Rtn = str_replace("Diez Cinco", "Quince", $Rtn);
        $Rtn = str_replace("Diez Seis", "Dieciseis", $Rtn);
        $Rtn = str_replace("Diez Siete", "Diecisiete", $Rtn);
        $Rtn = str_replace("Diez Ocho", "Dieciocho", $Rtn);
        $Rtn = str_replace("Diez Nueve", "Diecinueve", $Rtn);
        $Rtn = str_replace("Veinte Un", "Veintiun", $Rtn);
        $Rtn = str_replace("Veinte Dos", "Veintidos", $Rtn);
        $Rtn = str_replace("Veinte Tres", "Veintitres", $Rtn);
        $Rtn = str_replace("Veinte Cuatro", "Veinticuatro", $Rtn);
        $Rtn = str_replace("Veinte Cinco", "Veinticinco", $Rtn);
        $Rtn = str_replace("Veinte Seis", "Veintiseís", $Rtn);
        $Rtn = str_replace("Veinte Siete", "Veintisiete", $Rtn);
        $Rtn = str_replace("Veinte Ocho", "Veintiocho", $Rtn);
        $Rtn = str_replace("Veinte Nueve", "Veintinueve", $Rtn);

        // --------------------- FiltroUn ------------------------------
        If (substr($Rtn, 0, 1) == "M")
            $Rtn = "Un " . $Rtn;
        // --------------------- Adicionar Y ------------------------------
        for ($i = 65; $i <= 88; $i ++) {
            If ($i != 77)
                $Rtn = str_replace("a " . Chr($i), "* y " . Chr($i), $Rtn);
        }
        $Rtn = str_replace("*", "a", $Rtn);
        return ($Rtn);
    }

    function ReplaceStringFrom(&$x, $OldWrd, $NewWrd, $Ptr) {
        $x = substr($x, 0, $Ptr) . $NewWrd . substr($x, strlen($OldWrd) + $Ptr);
    }

    function Parte($x) {
        $Rtn = '';
        $t = '';
        $i = '';
        Do {
            switch ($x) {
                Case 0 :
                    $t = "Cero";
                    break;
                Case 1 :
                    $t = "Un";
                    break;
                Case 2 :
                    $t = "Dos";
                    break;
                Case 3 :
                    $t = "Tres";
                    break;
                Case 4 :
                    $t = "Cuatro";
                    break;
                Case 5 :
                    $t = "Cinco";
                    break;
                Case 6 :
                    $t = "Seis";
                    break;
                Case 7 :
                    $t = "Siete";
                    break;
                Case 8 :
                    $t = "Ocho";
                    break;
                Case 9 :
                    $t = "Nueve";
                    break;
                Case 10 :
                    $t = "Diez";
                    break;
                Case 20 :
                    $t = "Veinte";
                    break;
                Case 30 :
                    $t = "Treinta";
                    break;
                Case 40 :
                    $t = "Cuarenta";
                    break;
                Case 50 :
                    $t = "Cincuenta";
                    break;
                Case 60 :
                    $t = "Sesenta";
                    break;
                Case 70 :
                    $t = "Setenta";
                    break;
                Case 80 :
                    $t = "Ochenta";
                    break;
                Case 90 :
                    $t = "Noventa";
                    break;
                Case 100 :
                    $t = "Cien";
                    break;
                Case 200 :
                    $t = "Doscientos";
                    break;
                Case 300 :
                    $t = "Trescientos";
                    break;
                Case 400 :
                    $t = "Cuatrocientos";
                    break;
                Case 500 :
                    $t = "Quinientos";
                    break;
                Case 600 :
                    $t = "Seiscientos";
                    break;
                Case 700 :
                    $t = "Setecientos";
                    break;
                Case 800 :
                    $t = "Ochocientos";
                    break;
                Case 900 :
                    $t = "Novecientos";
                    break;
                Case 1000 :
                    $t = "Mil";
                    break;
                Case 1000000 :
                    $t = "Millón";
                    break;
            }

            If ($t == $this->Void) {
                $i = $i + 1;
                $x = $x / 1000;
                If ($x == 0)
                    $i = 0;
            } else
                break;
        } while ($i != 0);

        $Rtn = $t;
        Switch ($i) {
            Case 0 :
                $t = $this->Void;
                break;
            Case 1 :
                $t = " Mil";
                break;
            Case 2 :
                $t = " Millones";
                break;
            Case 3 :
                $t = " Billones";
                break;
        }
        return ($Rtn . $t);
    }

}

if ($_REQUEST ['funcion'] == 'letrasNumeros') {

    $funcionLetras = new EnLetras ();

    $Letras = $funcionLetras->ValorEnLetras($_REQUEST ['valor'], '( $Pesos ).');

    $Letras = json_encode($Letras);

    echo $Letras;
}

if ($_REQUEST ['funcion'] == 'consultarRegistroDisponibilidad') {


    if ($_REQUEST['rpseleccion'] != "") {
        $seleccionados = "";
        $disponibilidades = explode(",", substr($_REQUEST['rpseleccion'], 1));
        for ($i = 0; $i < count($disponibilidades); $i++) {
            if (explode("-", $_REQUEST['cdp'])[2] == explode("-", $disponibilidades[$i])[1]) {
                $seleccionados .= "," . explode("-", $disponibilidades[$i])[0];
            }
        }
        if ($seleccionados != "") {
            $seleccionados = substr($seleccionados, 1);
        } else {
            $seleccionados = 0;
        }
    } else {
        $seleccionados = 0;
    }


    $datos = array(0 => explode("-", $_REQUEST['cdp'])[1], 1 => explode("-", $_REQUEST['cdp'])[2], 2 => $_REQUEST['unidad'], 3 => $seleccionados);
    $cadenaSql = $this->sql->getCadenaSql('consultarRegistroDisponibilidad', $datos);
    $resultadoItems = $DBSICA->ejecutarAcceso($cadenaSql, "busqueda");
    $resultado = json_encode($resultadoItems);
    echo $resultado;
}
if ($_REQUEST ['funcion'] == 'consultarInfoRP') {

    $datosRp = array(0 => explode("-", $_REQUEST['cdp'])[1], 1 => explode("-", $_REQUEST['cdp'])[2], 2 => $_REQUEST['unidad'], 3 => $_REQUEST['rp']);
    $cadenaSql = $this->sql->getCadenaSql('Consultar_Rubros', $datosRp);
    $resultadoItems = $DBSICA->ejecutarAcceso($cadenaSql, "busqueda");
    $resultado = json_encode($resultadoItems);
    echo $resultado;
}
if ($_REQUEST ['funcion'] == 'consultarRp') {


    $miSesion = Sesion::singleton();
    $_REQUEST['usuario'] = trim($miSesion->idUsuario());
    $cadenaSqlUnidad = $this->sql->getCadenaSql("obtenerInfoUsuario", $_REQUEST['usuario']);
    $unidad = $DBFrameWork->ejecutarAcceso($cadenaSqlUnidad, "busqueda");

    $cadenaSql = $this->sql->getCadenaSql('ConsultarRP', $_REQUEST['id']);

    $datosRp = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
    $datoSicapital = array(
        0 => $datosRp[0]['numero_cdp'],
        1 => $datosRp[0]['vigencia_rp'],
        2 => $unidad[0]['unidad_ejecutora'],
        3 => $datosRp[0]['registro_presupuestal']
    );

    $cadenaSql = $this->sql->getCadenaSql('Consultar_Rubros', $datoSicapital);


    $resultadoItems = $DBSICA->ejecutarAcceso($cadenaSql, "busqueda");
    $resultado = json_encode($resultadoItems);
    echo $resultado;
}

if ($_REQUEST ['funcion'] == 'inactivarrp') {


    $cadenaSql = $this->sql->getCadenaSql('inactivarrp', $_REQUEST['id']);
    $resultadoItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");
    if ($resultadoItems == true) {
        $resultado = "true";
    } {
        $resultado = "false";
    }
    $resultado = json_encode($resultadoItems);
    echo $resultado;
}

if ($_REQUEST ['funcion'] == 'SeleccionOrdenador') {

    $cadenaSql = $this->sql->getCadenaSql('informacion_ordenador', $_REQUEST ['ordenador']);

    $resultadoItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
    $resultado = json_encode($resultadoItems [0]);

    echo $resultado;
}
if ($_REQUEST ['funcion'] == 'consultarInfoConvenio') {

    $cadenaSql = $this->sql->getCadenaSql('informacion_convenio', $_REQUEST['codigo']);
    $resultadoItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

    $resultado = json_encode($resultadoItems [0]);

    echo $resultado;
}
if ($_REQUEST ['funcion'] == 'consultarInfoContratistaUnico') {

    $cadenaSql = $this->sql->getCadenaSql('informacion_contratista_unico', $_REQUEST['id']);
    $resultadoItems = $esteRecursoDBAgora->ejecutarAcceso($cadenaSql, "busqueda");
    $resultado = json_encode($resultadoItems [0]);

    echo $resultado;
}

if ($_REQUEST ['funcion'] == 'consultarInfoSociedadTemporal') {

    $cadenaSql = $this->sql->getCadenaSql('informacion_sociedad_temporal_consulta', $_REQUEST['id']);
    $resultadoItems = $esteRecursoDBAgora->ejecutarAcceso($cadenaSql, "busqueda");
    $sqlParticipantes = $this->sql->getCadenaSql("obtener_participantes", $_REQUEST['id']);
    $participantes = $esteRecursoDBAgora->ejecutarAcceso($sqlParticipantes, "busqueda");
    array_push($resultadoItems, $participantes);
    $resultado = json_encode($resultadoItems);
    echo $resultado;
}




//------------------------obtener Numero de Ordenes a partit de la calsificacion tipo de orden -------------------------
if ($_REQUEST ['funcion'] == 'consultarNumeroOrden') {
    $datos = array('tipo_orden' => $_REQUEST ['valor1'], 'unidad' => $_REQUEST ['valor2']);
    $cadenaSql = $this->sql->getCadenaSql('buscar_numero_orden', $datos);

    $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

    $resultado = json_encode($resultado);

    echo $resultado;
}
if ($_REQUEST ['funcion'] == 'consultarProveedorFiltro') {

    $cadenaSql = $this->sql->getCadenaSql('buscarProveedoresFiltro', $_GET ['query']);
    $resultadoItems = $esteRecursoDBAgora->ejecutarAcceso($cadenaSql, "busqueda");
    foreach ($resultadoItems as $key => $values) {
        $keys = array(
            'value',
            'data'
        );
        $resultado [$key] = array_intersect_key($resultadoItems [$key], array_flip($keys));
    }

    echo '{"suggestions":' . json_encode($resultado) . '}';
}
if ($_REQUEST ['funcion'] == 'consultaProveedor') {
    $parametro = $_REQUEST ['proveedor'];
    $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
    $url = "http://10.20.0.127/agora/index.php?data=";
    $data = "pagina=servicio&servicios=true&servicio=servicioArgoProveedor&Parametro1=$parametro";
    $url_servicio = $url . $this->miConfigurador->fabricaConexiones->crypto->codificar($data, $enlace);
    $cliente = curl_init();
    curl_setopt($cliente, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($cliente, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cliente, CURLOPT_URL, $url_servicio);
    $repuestaWeb = curl_exec($cliente);
    curl_close($cliente);
    $repuestaWeb = explode("<json>", $repuestaWeb);
    echo $repuestaWeb[1];
}

//------------------------obtener Dependencias de acuerdo al identificador de la sede -------------------------
if ($_REQUEST ['funcion'] == 'consultarDependencia') {

    $cadenaSql = $this->sql->getCadenaSql('dependenciasConsultadas', $_REQUEST ['valor']);
    $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
    $resultado = json_encode($resultado);
    echo $resultado;
}
if ($_REQUEST ['funcion'] == 'consultaContrato') {

    $cadenaSql = $this->sql->getCadenaSql('buscar_contrato_audit', $_GET ['query']);
    $resultadoItems = $DBFrameWork->ejecutarAcceso($cadenaSql, "busqueda");

    foreach ($resultadoItems as $key => $values) {
        $keys = array(
            'value',
            'data'
        );
        $resultado [$key] = array_intersect_key($resultadoItems [$key], array_flip($keys));
    }

    echo '{"suggestions":' . json_encode($resultado) . '}';
}


if ($_REQUEST ['funcion'] == 'generarDocumento') {


    $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");
    $miPaginaActual = $this->miConfigurador->getVariableConfiguracion('pagina');
    $directorio = $this->miConfigurador->getVariableConfiguracion("host");
    $directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
    $directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");

    $rutaBloque = $this->miConfigurador->getVariableConfiguracion("host");
    $rutaBloque .= $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/";
    $rutaBloque .= $esteBloque ['grupo'] . "/" . $esteBloque ['nombre'];

    $numeroContrato = substr($_REQUEST ['numerocontrato'], 4);
    $vigencia = substr($_REQUEST ['numerocontrato'], 0, 4);

    $datosContraro = array(1 => $numeroContrato, 2 => $vigencia);
    $cadenaSql = $this->sql->getCadenaSql('consultarContratoProcesarAjax', $datosContraro);
    $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

    $variable_documento = "action=modificarContrato";
    $variable_documento .= "&pagina=modificarContrato";
    $variable_documento .= "&bloque=modificarContrato";
    $variable_documento .= "&bloqueGrupo=gestionContractual/contratos/";
    $variable_documento .= "&opcion=generarDocumento";
    $variable_documento .= "&numero_contrato=" . $numeroContrato;
    $variable_documento .= "&tipo_contrato=" . $resultado[0] ['tipo_contrato'];
    $variable_documento .= "&unidad=" . $resultado[0]['unidad_ejecutora'];
    $variable_documento .= "&tamanoletra=" . $_REQUEST['fuentedocumento'];
    $variable_documento .= "&vigencia=" . $vigencia;

    $variable_documento = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable_documento, $directorio);
    $indice = strpos($variable_documento, "index");
    $variable_documento = substr($variable_documento, $indice);
    $resultado = json_encode($variable_documento);

    echo $resultado;
}
?>
