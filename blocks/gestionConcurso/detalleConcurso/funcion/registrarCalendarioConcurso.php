<?php

namespace gestionConcurso\detalleConcurso\funcion;

use gestionConcurso\detalleConcurso\funcion\redireccion;

include_once('redireccionar.php');

if (!isset($GLOBALS ["autorizado"])) {
    include("../index.php");
    exit();
}

class RegistradorCalendarioConcurso
{

    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miFuncion;
    var $miSql;
    var $conexion;
    var $miLogger;
    var $miArchivo;

    function __construct($lenguaje, $sql, $funcion, $miLogger, $miArchivo)
    {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;
        $this->miFuncion = $funcion;
        $this->miLogger = $miLogger;
        $this->miArchivo = $miArchivo;
    }

    function procesarFormulario()
    {
        $SQLs = [];
        $conexion = "estructura";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
        $arregloDatos = array('consecutivo_calendario' => $_REQUEST['consecutivo_calendario'],
            'consecutivo_concurso' => $_REQUEST['consecutivo_concurso'],
            'consecutivo_actividad' => $_REQUEST['consecutivo_actividad'],
            'descripcion' => $_REQUEST['descripcion'],
            'fecha_inicio' => $_REQUEST['fecha_inicio_calendario'],
            'fecha_fin' => $_REQUEST['fecha_fin_calendario'],
            'estado' => isset($_REQUEST['estado']) ? $_REQUEST['estado'] : '',
            'porcentaje_aprueba' => $_REQUEST['porc_aprueba_fase'],
            'fecha_fin_reclamacion' => isset($_REQUEST['fecha_fin_reclamacion']) ? $_REQUEST['fecha_fin_reclamacion'] : '',
            'fecha_fin_resolver' => isset($_REQUEST['fecha_fin_resolver']) ? $_REQUEST['fecha_fin_resolver'] : '',
        );
        if ($arregloDatos['consecutivo_calendario'] == 0) {
            $cadenaSql = $this->miSql->getCadenaSql('registroCalendarioConcurso', $arregloDatos);
            $SQLs[] = $cadenaSql;
            $resultadoCalendario = $esteRecursoDB->ejecutarAcceso($cadenaSql, "registra", $arregloDatos, "registroCalendarioConcurso");
            $_REQUEST['consecutivo_calendario'] = $resultadoCalendario;
        } else {
            $cadenaSql = $this->miSql->getCadenaSql('actualizaCalendarioConcurso', $arregloDatos);
            $SQLs[] = $cadenaSql;
            $resultadoCalendario = $esteRecursoDB->ejecutarAcceso($cadenaSql, "actualiza", $arregloDatos, "actualizarCalendarioConcurso");
        }
        if ($resultadoCalendario) {
            $datosSoporte = array('consecutivo_persona' => 0,
                'consecutivo_dato' => $_REQUEST['consecutivo_calendario'],
                'id_usuario' => $_REQUEST['id_usuario']);
            $this->miArchivo->procesarArchivo($datosSoporte);
            redireccion::redireccionar('actualizoCalendarioConcurso', $arregloDatos);
            exit();
        } else {
            $arregloDatos['detalle'] = 'calendario';
            if (!empty($_SERVER['HTTP_CLIENT_IP'])){
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }else{
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            $c = 0;
            while ($c < count($SQLs)) {
                $SQLsDec[$c] = $this->miConfigurador->fabricaConexiones->crypto->codificar($SQLs[$c]);
                $c++;
            }

            $query = json_encode($SQLsDec);
            $error = json_encode(error_get_last());

            $datosLog = array (
                'id_usuario' => $_REQUEST['id_usuario'],
                'fecha_log' => date("Y-m-d H:i:s"),
                'host' => $ip,
                'query' => $query,
                'error' => $error,
            );

            $cadenaSQL = $this->miSql->getCadenaSql("insertarLogError", $datosLog);
            $esteRecursoDB->ejecutarAcceso($cadenaSQL, 'busqueda');

            redireccion::redireccionar('noActualizoDetalle', $arregloDatos);
            exit();
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

$miRegistrador = new RegistradorCalendarioConcurso($this->lenguaje, $this->sql, $this->funcion, $this->miLogger, $this->miArchivo);
$resultado = $miRegistrador->procesarFormulario();
?>