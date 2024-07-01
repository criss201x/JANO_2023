<?php
namespace gestionConcursante\concursosInscritos\funcion;

use gestionConcursante\concursosInscritos\funcion\redireccion;

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

        $arregloDatos = array(
            'observaciones' => $_REQUEST['observaciones'],
            'fecha' => $fecha,
            'consecutivo_calendario' => $_REQUEST['consecutivo_calendario'],
            'id_inscrito' => $_REQUEST['consecutivo_inscrito']
        );

        $cadenaSql = $this->miSql->getCadenaSql('registroReclamacion', $arregloDatos);
        $SQLs[] = $cadenaSql;
        $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "registra", $arregloDatos, "registroReclamacion");

        $cadena_sql = $this->miSql->getCadenaSql("validarPerfilEspecial", $_REQUEST['consecutivo_perfil']);
        $perfilEspecial= $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");
        


        if ($resultado) {
            //de acuerdo a la etapa, se debe referenciar la validación o la evaluación

            //Para la Verificación de Requisitos
            if ($_REQUEST['consecutivo_actividad'] == 3) {
                $arregloDatos = array(
                    'consecutivo_perfil=' => $_REQUEST['consecutivo_perfil'],
                    'consecutivo_concurso' => $_REQUEST['consecutivo_concurso'],
                    'consecutivo_calendario' => $_REQUEST['consecutivo_calendario'],
                    'consecutivo_inscrito' => $_REQUEST['consecutivo_inscrito'],
                    'reclamacion' => $resultado,
                    'tipoEvaluacion'=> ($perfilEspecial)?'evaluacion_perfil_especial': 'concurso_evaluar'
                );
                //se referencia la reclamación en la tabla concurso.valida_requisito
                $cadenaSql = $this->miSql->getCadenaSql('actualizaValidacion', $arregloDatos);
                $resultadoActualizacion = $esteRecursoDB->ejecutarAcceso($cadenaSql, "registra", $arregloDatos, "actualizaValidacion");

                if ($resultadoActualizacion) {
                    redireccion::redireccionar('registroReclamacion', $arregloDatos);
                    exit();
                }
            } //Para la Prueba idiomas
            else if ($_REQUEST['consecutivo_actividad'] == 6) {
                $arregloDatos = array(
                    'consecutivo_perfil=' => $_REQUEST['consecutivo_perfil'],
                    'consecutivo_concurso' => $_REQUEST['consecutivo_concurso'],
                    'consecutivo_calendario' => $_REQUEST['consecutivo_calendario'],
                    'consecutivo_inscrito' => $_REQUEST['consecutivo_inscrito'],
                    'reclamacion' => $resultado,
                    'tipoEvaluacion'=> ($perfilEspecial)?'evaluacion_perfil_especial': 'concurso_evaluar'
                );
                //se referencia la reclamación en la tabla concurso.evaluacion_parcial
                $cadenaSql = $this->miSql->getCadenaSql('actualizaEvaluacion', $arregloDatos);
                $resultadoActualizacion = $esteRecursoDB->ejecutarAcceso($cadenaSql, "registra", $arregloDatos, "actualizaEvaluacion");

                if ($resultadoActualizacion) {
                    redireccion::redireccionar('registroReclamacion', $arregloDatos);
                    exit();
                }
            } //Para Competencias
            else if ($_REQUEST['consecutivo_actividad'] == 5) {
                $arregloDatos = array(
                    'consecutivo_perfil=' => $_REQUEST['consecutivo_perfil'],
                    'consecutivo_concurso' => $_REQUEST['consecutivo_concurso'],
                    'consecutivo_calendario' => $_REQUEST['consecutivo_calendario'],
                    'consecutivo_inscrito' => $_REQUEST['consecutivo_inscrito'],
                    'reclamacion' => $resultado,
                    'tipoEvaluacion'=> ($perfilEspecial)?'evaluacion_perfil_especial': 'concurso_evaluar'
                );
                //se referencia la reclamación en la tabla concurso.evaluacion_parcial
                $cadenaSql = $this->miSql->getCadenaSql('actualizaEvaluacionCompetencias', $arregloDatos);
                $resultadoActualizacion = $esteRecursoDB->ejecutarAcceso($cadenaSql, "registra", $arregloDatos, "actualizaEvaluacionCompetencias");

                if ($resultadoActualizacion) {
                    redireccion::redireccionar('registroReclamacion', $arregloDatos);
                    exit();
                }
            } //Para Hoja de Vida
            else if ($_REQUEST['consecutivo_actividad'] == 4) {
                $arregloDatos = array(
                    'consecutivo_perfil=' => $_REQUEST['consecutivo_perfil'],
                    'consecutivo_concurso' => $_REQUEST['consecutivo_concurso'],
                    'consecutivo_calendario' => $_REQUEST['consecutivo_calendario'],
                    'consecutivo_inscrito' => $_REQUEST['consecutivo_inscrito'],
                    'reclamacion' => $resultado,
                    'tipoEvaluacion'=> ($perfilEspecial)?'evaluacion_perfil_especial': 'concurso_evaluar'
                );
                //se referencia la reclamación en la tabla concurso.evaluacion_parcial
                $cadenaSql = $this->miSql->getCadenaSql('actualizaEvaluacionHojaVida', $arregloDatos);
                $resultadoActualizacion = $esteRecursoDB->ejecutarAcceso($cadenaSql, "registra", $arregloDatos, "actualizaEvaluacionHojaVida");

                if ($resultadoActualizacion) {
                    redireccion::redireccionar('registroReclamacion', $arregloDatos);
                    exit();
                }
            }//para resultados finales 
            else if ($_REQUEST['consecutivo_actividad'] == 7) {
                $arregloDatos = array(
                    'consecutivo_perfil=' => $_REQUEST['consecutivo_perfil'],
                    'consecutivo_concurso' => $_REQUEST['consecutivo_concurso'],
                    'consecutivo_calendario' => $_REQUEST['consecutivo_calendario'],
                    'consecutivo_inscrito' => $_REQUEST['consecutivo_inscrito'],
                    'reclamacion' => $resultado
                );
                //unicamente guarda la reclamacion no se referencia en las anteriores evaluaciones 
                redireccion::redireccionar('registroReclamacion', $arregloDatos);
                exit();
            }

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

            redireccion::redireccionar('noRegistroReclamacion', $arregloDatos);
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

$miRegistrador = new RegistradorValidacion($this->lenguaje, $this->sql, $this->funcion, $this->miLogger);
$resultado = $miRegistrador->procesarFormulario();
?>
