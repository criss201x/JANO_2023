<?php

namespace gestionConcurso\detalleConcurso\funcion;

use gestionConcurso\detalleConcurso\funcion\redireccion;

include_once('redireccionar.php');

if (!isset($GLOBALS ["autorizado"])) {
    include("../index.php");
    exit();
}

class RegistradorCriterioConcurso
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

        $arregloDatos = array('consecutivo_evaluar' => $_REQUEST['consecutivo_evaluar'],
            'consecutivo_concurso' => $_REQUEST['consecutivo_concurso'],
            'consecutivo_criterio' => $_REQUEST['consecutivo_criterio'],
            'maximo_puntos' => $_REQUEST['maximo_puntos'],
            'puntos_aprueba' => $_REQUEST['puntos_aprueba'],
            'consecutivo_calendario' => $_REQUEST['consecutivo_calendario'],
            'estado' => isset($_REQUEST['estado']) ? $_REQUEST['estado'] : ''
        );

        if ($arregloDatos['consecutivo_evaluar'] == 0) {
            $cadenaSql = $this->miSql->getCadenaSql('registroCriterioConcurso', $arregloDatos);
            $SQLs[] = $cadenaSql;
            $resultadoConcurso = $esteRecursoDB->ejecutarAcceso($cadenaSql, "registra", $arregloDatos, "registroCriterioConcurso");
            $_REQUEST['consecutivo_evaluar'] = $resultadoConcurso;
        } else {
            $cadenaSql = $this->miSql->getCadenaSql('actualizaCriterioConcurso', $arregloDatos);
            $SQLs[] = $cadenaSql;
            $resultadoConcurso = $esteRecursoDB->ejecutarAcceso($cadenaSql, "actualiza", $arregloDatos, "actualizarCriterioConcurso");
        }

        if ($resultadoConcurso) {   //$_REQUEST['consecutivo']=0;$_REQUEST['consecutivo_persona'];
            //$_REQUEST['consecutivo_dato']=$_REQUEST['consecutivo_concurso'];
            //$this->miArchivo->procesarArchivo('datosConcurso');
            redireccion::redireccionar('actualizoCriterioConcurso', $arregloDatos);
            exit();
        } else {
            $arregloDatos['detalle'] = 'criterio';
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

$miRegistrador = new RegistradorCriterioConcurso($this->lenguaje, $this->sql, $this->funcion, $this->miLogger, $this->miArchivo);
$resultado = $miRegistrador->procesarFormulario();
?>