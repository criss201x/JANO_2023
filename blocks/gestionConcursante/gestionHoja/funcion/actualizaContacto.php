<?php

namespace gestionConcursante\gestionHoja\funcion;

use gestionConcursante\gestionHoja\funcion\redireccion;

include_once('redireccionar.php');

if (!isset($GLOBALS ["autorizado"])) {
    include("../index.php");
    exit();
}

class RegistradorContactos
{

    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miFuncion;
    var $miSql;
    var $conexion;
    var $miLogger;
    var $miArchivo;

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

        $arregloDatos = array('id_usuario' => $_REQUEST['id_usuario'],
            'consecutivo_contacto' => $_REQUEST['consecutivo_contacto'],
            'consecutivo_persona' => $_REQUEST['consecutivo_persona'],
            'pais_residencia' => $_REQUEST['pais_residencia'],
            'departamento_residencia' => $_REQUEST['departamento_residencia'],
            'ciudad_residencia' => $_REQUEST['ciudad_residencia'],
            'direccion_residencia' => $_REQUEST['direccion_residencia'],
            'correo' => $_REQUEST['correo'],
            'correo_secundario' => $_REQUEST['correo_secundario'],
            'telefono' => $_REQUEST['telefono'],
            'celular' => $_REQUEST['celular'],
            'nombre' => $_REQUEST['nombre'],
            'apellido' => $_REQUEST['apellido'],
        );

        if ($arregloDatos['consecutivo_contacto'] == 0) {
            $cadenaSql = $this->miSql->getCadenaSql('registroContacto', $arregloDatos);
            $SQLs[] = $cadenaSql;
            $resultadoContacto = $esteRecursoDB->ejecutarAcceso($cadenaSql, "registra", $arregloDatos, "registrarContacto");
        } else {
            $cadenaSql = $this->miSql->getCadenaSql('actualizarContacto', $arregloDatos);
            $SQLs[] = $cadenaSql;
            $resultadoContacto = $esteRecursoDB->ejecutarAcceso($cadenaSql, "actualiza", $arregloDatos, "actualizarContacto");
        }

        if ($resultadoContacto) {
            $cadenaSql = $this->miSql->getCadenaSql('actualizarContactoUsuario', $arregloDatos);
            $resultadoUs = $esteRecursoDB->ejecutarAcceso($cadenaSql, "actualiza", $arregloDatos, "actualizarContactoUsuario");

            redireccion::redireccionar('actualizoContacto', $arregloDatos);
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

$miRegistrador = new RegistradorContactos($this->lenguaje, $this->sql, $this->funcion, $this->miLogger);

$resultado = $miRegistrador->procesarFormulario();
?>