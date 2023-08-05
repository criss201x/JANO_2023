<?php
namespace gestionConcurso\evaluacion\funcion;

use gestionConcurso\evaluacion\funcion\redireccion;

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

        //buscar si existe el grupo para el jurado y el perfil, sino existe hacer el registro
        $parametro = array(
            'jurado' => $_REQUEST['usuario'],
            'perfil' => $_REQUEST['consecutivo_perfil'],
            'fecha' => $fecha
        );

        $cadena_sql = $this->miSql->getCadenaSql("consultarGrupo", $parametro);
        $SQLs[] = $cadena_sql;
        $resultadoGrupo = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

        if ($resultadoGrupo) {
            for ($i = 0; $i < $_REQUEST['numeroCriterios']; $i++) {
                $arregloDatos = array('grupo' => $resultadoGrupo[0][0],
                    'inscrito' => $_REQUEST['consecutivo_inscrito'],
                    'id_evaluar' => $_REQUEST['id_evaluar' . $i],
                    'puntaje' => $_REQUEST['puntaje' . $i],
                    'observacion' => $_REQUEST['observaciones' . $i],
                    'fecha' => $fecha,
                    'consecutivo_concurso' => $_REQUEST['consecutivo_concurso']
                );

                $cadenaSql = $this->miSql->getCadenaSql('registroEvaluacion', $arregloDatos);
                array_push($SQLs, str_replace('RETURNING id', '', $cadenaSql));
                //$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registra", $arregloDatos, "registroEvaluacion" );
            }
            //  var_dump($SQLs);//exit;
            $resultado = $esteRecursoDB->transaccion($SQLs);

            if ($resultado) {
                redireccion::redireccionar('registroEvaluacion', $arregloDatos);
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

                redireccion::redireccionar('noregistroEvaluacion', $arregloDatos);
                exit();
            }
        } else {
            //si no existe el grupo
            $cadena_sql = $this->miSql->getCadenaSql("registrarGrupo", $parametro);
            $SQLs[] = $cadena_sql;
            $registroGrupo = $esteRecursoDB->ejecutarAcceso($cadena_sql, "registra", $parametro, "registroGrupoEvaluacion");

            for ($i = 0; $i < $_REQUEST['numeroCriterios']; $i++) {
                $arregloDatos = array('grupo' => $registroGrupo,
                    'inscrito' => $_REQUEST['consecutivo_inscrito'],
                    'id_evaluar' => $_REQUEST['id_evaluar' . $i],
                    'puntaje' => $_REQUEST['puntaje' . $i],
                    'observacion' => $_REQUEST['observaciones' . $i],
                    'fecha' => $fecha,
                    'consecutivo_concurso' => $_REQUEST['consecutivo_concurso']
                );

                $cadenaSql = $this->miSql->getCadenaSql('registroEvaluacion', $arregloDatos);
                array_push($SQLs, str_replace('RETURNING id', '', $cadenaSql));
                //$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registra", $arregloDatos, "registroEvaluacion" );
            }
            $resultado = $esteRecursoDB->transaccion($SQLs);
            if ($resultado) {
                redireccion::redireccionar('registroEvaluacion', $arregloDatos);
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

                redireccion::redireccionar('noregistroEvaluacion', $arregloDatos);
                exit();
            }
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
