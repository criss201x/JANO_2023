<?php

namespace gestionConcurso\caracterizaConcurso\funcion;

use gestionConcurso\caracterizaConcurso\funcion\redireccion;

include_once('redireccionar.php');

if (!isset($GLOBALS ["autorizado"])) {
    include("../index.php");
    exit();
}

class RegistradorPerfil
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

        $arregloDatos = array('id_actividad' => $_REQUEST['id_actividad'],
            'nombreActividad' => $_REQUEST['nombreActividad'],
            'descripcionActividad' => $_REQUEST['descripcionActividad']

        );

        $cadena_sql = $this->miSql->getCadenaSql("actividadEnConsurso", $arregloDatos);
        $SQLs[] = $cadena_sql;
        $resultado = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

        if ($resultado) {
            //La actividad tiene relación con un consurso
            redireccion::redireccionar('actividadEnConsurso', $arregloDatos);
            exit();
        } else {

            $this->cadena_sql = $this->miSql->getCadenaSql("editarActividad", $arregloDatos);
            $SQLs[] = $this->cadena_sql;
            $resultadoActividad = $esteRecursoDB->ejecutarAcceso($this->cadena_sql, "acceso", $arregloDatos, "editarActividad");

            if ($resultadoActividad) {
                redireccion::redireccionar('editoActividad', $arregloDatos);
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

                redireccion::redireccionar('noEditoActividad', $arregloDatos);
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

$miRegistrador = new RegistradorPerfil($this->lenguaje, $this->sql, $this->funcion, $this->miLogger);

$resultado = $miRegistrador->procesarFormulario();
?>