<?php
namespace gestionConcurso\reclamaciones\funcion;

use gestionConcurso\reclamaciones\funcion\redireccion;

include_once('redireccionar.php');

if (!isset($GLOBALS ["autorizado"])) {
    include("../index.php");
    exit();
}

class RegistradorEvaluacion
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

        if ($_REQUEST['validacion'] == 'SI') {
            //inactivar registro de la validación
            $parametro = array(
                'validacion' => $_REQUEST['evaluar_respuesta']
            );
            $cadena_sql = $this->miSql->getCadenaSql("inactivarValidacion", $parametro);
            $SQLs[] = $cadena_sql;
            $resultado = $esteRecursoDB->ejecutarAcceso($cadena_sql, "actualiza", $parametro, "inactivarValidacion");
        }

        $parametro = array(
            'reclamacion' => $_REQUEST['reclamacion'],
            'respuesta' => $_REQUEST['validacion'],
            'observacion' => $_REQUEST['observaciones'],
            'fecha' => $fecha,
            'evaluar_respuesta' => $_REQUEST['evaluar_respuesta'],
            'evaluador' => $_REQUEST['usuario'],
            'consecutivo_concurso' => $_REQUEST['consecutivo_concurso']
        );

        $cadena_sql = $this->miSql->getCadenaSql("registroEvaluacionReclamacion", $parametro);
        $SQLs[] = $cadena_sql;
        $resultado = $esteRecursoDB->ejecutarAcceso($cadena_sql, "registra", $parametro, "registroEvaluacionReclamacion");

        if ($resultado) {
            redireccion::redireccionar('evaluoReclamacion', $parametro);
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

            $query = json_encode($SQLsDec);
            $error = json_encode(error_get_last());

            $datosLog = array(
                'id_usuario' => $_REQUEST['id_usuario'],
                'fecha_log' => date("Y-m-d H:i:s"),
                'host' => $ip,
                'query' => $query,
                'error' => $error,
            );

            $cadenaSQL = $this->miSql->getCadenaSql("insertarLogError", $datosLog);
            $esteRecursoDB->ejecutarAcceso($cadenaSQL, 'busqueda');
            redireccion::redireccionar('noEvaluoReclamacion', $parametro);
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

$miRegistrador = new RegistradorEvaluacion($this->lenguaje, $this->sql, $this->funcion, $this->miLogger);
$resultado = $miRegistrador->procesarFormulario();
?>
