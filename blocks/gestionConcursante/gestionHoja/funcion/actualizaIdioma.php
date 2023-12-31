<?php

namespace gestionConcursante\gestionHoja\funcion;

use gestionConcursante\gestionHoja\funcion\redireccion;

include_once('redireccionar.php');

if (!isset($GLOBALS ["autorizado"])) {
    include("../index.php");
    exit();
}

class RegistradorIdioma
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

        $arregloDatos = array('id_usuario' => $_REQUEST['id_usuario'],
            'consecutivo_conocimiento' => $_REQUEST['consecutivo_conocimiento'],
            'consecutivo_persona' => $_REQUEST['consecutivo_persona'],
            'codigo_idioma' => (isset($_REQUEST['codigo_idioma']) ? $_REQUEST['codigo_idioma'] : ''),
            'nivel_lee' => (isset($_REQUEST['nivel_lee']) ? $_REQUEST['nivel_lee'] : ''),
            'nivel_escribe' => (isset($_REQUEST['nivel_escribe']) ? $_REQUEST['nivel_escribe'] : ''),
            'nivel_habla' => (isset($_REQUEST['nivel_habla']) ? $_REQUEST['nivel_habla'] : ''),
            'certificacion' => (isset($_REQUEST['certificacion']) ? $_REQUEST['certificacion'] : ''),
            'institucion_certificacion' => (isset($_REQUEST['institucion_certificacion']) ? $_REQUEST['institucion_certificacion'] : ''),
            'idioma_concurso' => (isset($_REQUEST['idioma_concurso']) ? $_REQUEST['idioma_concurso'] : ''),
            'nombre' => $_REQUEST['nombre'],
            'apellido' => $_REQUEST['apellido'],

        );
        if ($arregloDatos['consecutivo_conocimiento'] == 0) {
            $cadenaSql = $this->miSql->getCadenaSql('registroIdioma', $arregloDatos);
            $SQLs[] = $cadenaSql;
            $resultadoIdioma = $esteRecursoDB->ejecutarAcceso($cadenaSql, "registra", $arregloDatos, "registrarConocimientoIdioma");
            $_REQUEST['consecutivo_conocimiento'] = $resultadoIdioma;
        } else {
            $cadenaSql = $this->miSql->getCadenaSql('actualizarIdioma', $arregloDatos);
            $SQLs[] = $cadenaSql;
            $resultadoIdioma = $esteRecursoDB->ejecutarAcceso($cadenaSql, "actualiza", $arregloDatos, "actualizarConocimientoIdioma");
        }

        if ($resultadoIdioma) {
            $datosSoporte = array('consecutivo_persona' => $_REQUEST['consecutivo_persona'],
                'consecutivo_dato' => $_REQUEST['consecutivo_conocimiento'],
                'id_usuario' => $_REQUEST['id_usuario']);
            $this->miArchivo->procesarArchivo($datosSoporte);

            redireccion::redireccionar('actualizoIdioma', $arregloDatos);
            exit();
        } else {
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

            redireccion::redireccionar('noActualizo', $arregloDatos);
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

$miRegistrador = new RegistradorIdioma($this->lenguaje, $this->sql, $this->funcion, $this->miLogger, $this->miArchivo);

$resultado = $miRegistrador->procesarFormulario();
?>