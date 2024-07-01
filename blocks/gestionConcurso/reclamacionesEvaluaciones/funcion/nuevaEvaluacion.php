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

        $cadena_sql = $this->miSql->getCadenaSql("validarPerfilEspecial2", $_REQUEST['inscrito']);
        $perfilEspecial= $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

        $parametro = array(
            'reclamacion' => $_REQUEST['reclamacion'],
            'usuario' => $_REQUEST['usuario'],
            'tipoEvaluacion'=> ($perfilEspecial)?'evaluacion_perfil_especial': 'concurso_evaluar'
        );
        $cadena_sql = $this->miSql->getCadenaSql("consultarDetalleReclamacion2", $parametro);
        $SQLs[] = $cadena_sql;
        $resultadoDetalleReclamacion = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

        $cadena_sql = $this->miSql->getCadenaSql("consultaEvaluacionesReclamacionInactivas", $parametro);
        $SQLs[] = $cadena_sql;
        $evaluacionesInactivas = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

        foreach ($resultadoDetalleReclamacion as $key => $value) {

            $parametro = array(
                'grupo' => $resultadoDetalleReclamacion[$key]['id_grupo'],
                'inscrito' => $_REQUEST['inscrito'],
                'id_evaluar' => $resultadoDetalleReclamacion[$key]['id_evaluar'],
                'puntaje' => $_REQUEST['puntaje' . $key],
                'observacion' => "Evaluación asociada a la reclamación #" . $_REQUEST['reclamacion'],
                'fecha' => $fecha,
                'reclamacion' => $_REQUEST['reclamacion']
            );

            $cadena_sql = $this->miSql->getCadenaSql("registroNuevaEvaluacion", $parametro);
            $SQLs[] = $cadena_sql;
            $resultado = $esteRecursoDB->ejecutarAcceso($cadena_sql, "registra", $parametro, "registroEvaluacionReclamacion");

        }
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
