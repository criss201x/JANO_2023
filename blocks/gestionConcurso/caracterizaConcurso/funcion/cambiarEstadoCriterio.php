<?php

namespace gestionConcurso\caracterizaConcurso\funcion;

use gestionConcurso\caracterizaConcurso\funcion\redireccion;

include_once('redireccionar.php');

if (!isset($GLOBALS ["autorizado"])) {
    include("../index.php");
    exit();
}

class CambiarEstado
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

        //$miSesion = Sesion::singleton();
        //$usuarioSoporte = $miSesion->getSesionUsuarioId();

        $parametro['estado'] = $_REQUEST['estado'];
        $parametro['id_criterio'] = $_REQUEST['id_criterio'];
        $this->cadena_sql = $this->miSql->getCadenaSql("cambiarEstadoCriterio", $parametro);
        $SQLs[] = $this->cadena_sql;
        $resultadoEstado = $esteRecursoDB->ejecutarAcceso($this->cadena_sql, "acceso", $parametro, "actualizarEstadoCriterio");

        if ($resultadoEstado) {
            redireccion::redireccionar($_REQUEST['opcion'], $_REQUEST);
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

            redireccion::redireccionar('no' . $_REQUEST['opcion'], $_REQUEST);
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

$miRegistrador = new CambiarEstado($this->lenguaje, $this->sql, $this->funcion, $this->miLogger);

$resultado = $miRegistrador->procesarFormulario();
?>