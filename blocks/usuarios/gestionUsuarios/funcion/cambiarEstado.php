<?php

namespace usuarios\gestionUsuarios\funcion;

use usuarios\gestionUsuarios\funcion\redireccion;

include_once ('redireccionar.php');

if (!isset($GLOBALS ["autorizado"])) {
    include ("../index.php");
    exit();
}

class CambiarEstado {

    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miFuncion;
    var $miSql;
    var $conexion;
    var $miLogger;

    function __construct($lenguaje, $sql, $funcion, $miLogger) {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;
        $this->miFuncion = $funcion;
        $this->miLogger= $miLogger;
    }

    function procesarFormulario() {
// 		var_dump ( $_REQUEST );
        $conexion="estructura";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        //$miSesion = Sesion::singleton();
	//$usuarioSoporte = $miSesion->getSesionUsuarioId(); 
	
        $parametro['usuario']=$_REQUEST['id_usuario'];
	$parametro['estado']=$_REQUEST['estado'];
        $this->cadena_sql = $this->miSql->getCadenaSql("CambiarEstadoUsuario", $parametro);
	$resultadoEstado = $esteRecursoDB->ejecutarAcceso($this->cadena_sql, "actualizar",  $parametro, "CambiarEstadoUsuario" );
	
        if($resultadoEstado)
	{	
            redireccion::redireccionar($_REQUEST['opcion'],$_REQUEST['id_usuario']);
            
	}else
	{
            redireccion::redireccionar('no'.$_REQUEST['opcion'],$_REQUEST['id_usuario']);
            exit();
        }
    }

    function resetForm() {
        foreach ($_REQUEST as $clave => $valor) {

            if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
                unset($_REQUEST [$clave]);
            }
        }
    }

}

$miRegistrador = new CambiarEstado($this->lenguaje, $this->sql, $this->funcion,$this->miLogger);

$resultado = $miRegistrador->procesarFormulario();
?>