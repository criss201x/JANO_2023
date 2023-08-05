<?php
namespace gestionConcurso\reclamacionesEvaluaciones\funcion;

use gestionConcurso\reclamacionesEvaluaciones\funcion\redireccion;

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

        $parametro = array(
            'reclamacion' => $_REQUEST['reclamacion'],
            'usuario' => $_REQUEST['usuario']
        );
        $cadena_sql = $this->miSql->getCadenaSql("consultarDetalleReclamacion2", $parametro);
        $SQLs[] = $cadena_sql;
        $resultadoDetalleReclamacion = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");
        $SQLs = [];
        $cadena_sql = $this->miSql->getCadenaSql("ConsultaReclamacionFinal", $parametro);
        
        $resultadoF = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

        if($resultadoF && $resultadoF[0]['consecutivo_actividad'] == 7){
            $parametro = array(
                'reclamacion' => $_REQUEST['reclamacion'],
                'respuesta' => 'NO',
                'observacion' => $_REQUEST['observaciones_reclamos'],
                'fecha' => $fecha,
                'evaluar_respuesta' => 0,
                'evaluador' => $_REQUEST['usuario']
            );
            $cadena_sql = $this->miSql->getCadenaSql("registroEvaluacionReclamacion", $parametro);
            array_push($SQLs, str_replace('RETURNING id', '', $cadena_sql));            
        }

        foreach ($resultadoDetalleReclamacion as $key => $value) {

            if ($_REQUEST['validacion' . $key] == 'SI') {
                //inactivar registro de la evaluación
                $parametro = array(
                    'evaluacion' => $resultadoDetalleReclamacion[$key]['evaluacion_id']
                );
                $cadena_sql = $this->miSql->getCadenaSql("inactivarValidacion", $parametro);
                array_push($SQLs, str_replace('RETURNING id', '', $cadena_sql));
                //$resultado = $esteRecursoDB->ejecutarAcceso($cadena_sql, "actualiza", $parametro, "inactivarValidacion");

            }

            $parametro = array(
                'reclamacion' => $_REQUEST['reclamacion'],
                'respuesta' => $_REQUEST['validacion' . $key],
                'observacion' => $_REQUEST['observaciones' . $key],
                'fecha' => $fecha,
                'evaluar_respuesta' => $resultadoDetalleReclamacion[$key]['evaluacion_id'],
                'evaluador' => $_REQUEST['usuario']
            );

            $cadena_sql = $this->miSql->getCadenaSql("registroEvaluacionReclamacion", $parametro);
            array_push($SQLs, str_replace('RETURNING id', '', $cadena_sql));
            //$resultado = $esteRecursoDB->ejecutarAcceso($cadena_sql, "registra", $parametro, "registroEvaluacionReclamacion");

            if ($_REQUEST['validacion' . $key] == "SI") {
                $parametro = array(
                    'grupo' => $resultadoDetalleReclamacion[$key]['id_grupo'],
                    'inscrito' => $_REQUEST['consecutivo_inscrito'],
                    'id_evaluar' => $resultadoDetalleReclamacion[$key]['id_evaluar'],
                    'puntaje' => $_REQUEST['puntaje' . $key],
                    'observacion' => "Evaluación asociada a la reclamación #" . $_REQUEST['reclamacion'],
                    'fecha' => $fecha,
                    'reclamacion' => $_REQUEST['reclamacion'],
                    'consecutivo_concurso=' . $_REQUEST['consecutivo_concurso']
                );

                $cadena_sql = $this->miSql->getCadenaSql("registroNuevaEvaluacion", $parametro);
                array_push($SQLs, str_replace('RETURNING id', '', $cadena_sql));
                //$resultado = $esteRecursoDB->ejecutarAcceso($cadena_sql, "registra", $parametro, "registroEvaluacionReclamacion");
            }

        }
        $resultado = $esteRecursoDB->transaccion($SQLs);
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
