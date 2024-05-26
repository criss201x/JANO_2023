<?php

namespace gestionPublicacion\funcion;

use gestionPublicacion\funcion\redireccion;

include_once('redireccionar.php');

if (!isset($GLOBALS ["autorizado"])) {
    include("../index.php");
    exit();
}

class RegistradorValidacion
{
    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miFuncion;
    var $miSql;
    var $conexion;
    var $miLogger;

    function __construct($lenguaje, $sql, $funcion, $miLogger)
    {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;
        $this->miFuncion = $funcion;
        $this->miLogger = $miLogger;
    }

    function procesarFormulario()
    {
        $SQLs = [];

        $conexion = "estructura";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $fecha = date("Y-m-d H:i:s");

        $arregloDatos = array('consecutivo_inscrito' => $_REQUEST['consecutivo_inscrito'],
            'fecha' => $fecha,
            'consecutivo_concurso' => $_REQUEST['consecutivo_concurso'],
            'consecutivo_perfil' => $_REQUEST['consecutivo_perfil'],
            'consecutivo_inscrito' => $_REQUEST['consecutivo_inscrito'],
            'version_validacion' => '1',
            'tipo_dato' => $_REQUEST['tab'],
            'observacion' => $_REQUEST['observaciones'.$_REQUEST['tab']],
            'validacion' => $_REQUEST['validar'.$_REQUEST['tab']]
        );

        $idSoportes = array();

        foreach ($_REQUEST as $clave => $valor) {
            if (strpos($clave, "validacion") !== false) {
                array_push($idSoportes, substr($clave, 10));
            }
        }

        $errorObs = $arregloDatos['observacion'] == '';

        $soportes = [];
        foreach ($idSoportes as $idSoporte) {
            $soportes[$idSoporte] = array(
                "validacion" => $_REQUEST['validacion'.$idSoporte],
                "observacion" => $_REQUEST['observacion'.$idSoporte]
            );

            if ($_REQUEST['observacion'.$idSoporte] == '') {
                $errorObs = true;
                break;
            }
        }

        $arregloDatos["soportes"] = $soportes;
        if ($errorObs) {
            redireccion::redireccionar('errorObservaciones', $arregloDatos);
            exit();
        }

        $SQLs = array();

        $resultado = true;

        foreach ($soportes as $idSoporte => $resultado) {
            $cadenaSqlExist = $this->miSql->getCadenaSql('existValidacionSoporte', $idSoporte);
            $resultadoExist = $esteRecursoDB->ejecutarAcceso($cadenaSqlExist, "busqueda");

            array_push($SQLs, $cadenaSqlExist);

            $parametros = array (
                'idSoporte' => $idSoporte,
                'validacion' => $resultado["validacion"],
                'observacion' => $resultado["observacion"]
            );

            $cadenaSqlValSoporte = '';
            if ($resultadoExist[0]['exists'] == 'f') {
                $cadenaSqlValSoporte = $this->miSql->getCadenaSql('insertValidacionSoporte', $parametros);
            } else {
                $cadenaSqlValSoporte = $this->miSql->getCadenaSql('actualizaValidacionSoporte', $parametros);
            }

            array_push($SQLs, $cadenaSqlValSoporte);

            $resultadoValSoporte = $esteRecursoDB->ejecutarAcceso($cadenaSqlValSoporte, "registra", $parametros, "insertValidacionSoporte");
            $resultado = $resultado && $resultadoValSoporte;
        }

        $cadenaSqlExist = $this->miSql->getCadenaSql('existValidacionTipoSoporte', $arregloDatos);
        $resultadoExist = $esteRecursoDB->ejecutarAcceso($cadenaSqlExist, "busqueda");

        array_push($SQLs, $cadenaSqlExist);

        $cadenaSqlValTipoSoporte = '';
        if ($resultadoExist[0]['exists'] == 'f') {
            $cadenaSqlValTipoSoporte = $this->miSql->getCadenaSql('insertValidacionTipoSoporte', $arregloDatos);
        } else {
            $cadenaSqlValTipoSoporte = $this->miSql->getCadenaSql('actualizaValidacionTipoSoporte', $arregloDatos);
        }

        array_push($SQLs, $cadenaSqlValTipoSoporte);
        $resultadoValTipoSoporte = $esteRecursoDB->ejecutarAcceso($cadenaSqlValTipoSoporte, "registra", $arregloDatos, "insertValidacionTipoSoporte");

        if ($resultado && $resultadoValTipoSoporte) {
            redireccion::redireccionar('validoSoportes', $arregloDatos);
            exit();
        } else {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            $c = 0;
            while ($c < count($SQLs)) {
                $SQLsDec[$c] = $this->miConfigurador->fabricaConexiones->crypto->codificar($SQLs[$c]);
                $c++;
            }

            redireccion::redireccionar('noValidoSoportes', $arregloDatos);
        }

    }

    function resetForm()
    {
        foreach ($_REQUEST as $clave => $valor) {
            if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
                unset($_REQUEST [$clave]);
            }
        }
    }

}

$miRegistrador = new RegistradorValidacion($this->lenguaje, $this->sql, $this->funcion, $this->miLogger);
$resultado = $miRegistrador->procesarFormulario();
?>