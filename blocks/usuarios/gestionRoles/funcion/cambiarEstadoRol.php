<?php

namespace usuarios\gestionRoles\funcion;

use usuarios\gestionRoles\funcion\redireccion;

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

        $parametro['estado']=$_REQUEST['estado'];
        $parametro['id_subsistema']=$_REQUEST['id_subsistema'];
        $parametro['rol_id']=$_REQUEST['rol_id'];
        $this->cadena_sql = $this->miSql->getCadenaSql("CambiarEstadoRol", $parametro);
	
        $resultadoEstado = $esteRecursoDB->ejecutarAcceso($this->cadena_sql, "actualizar", $parametro, "CambiarEstadoRol" );
	
        if($resultadoEstado)
	{	
            redireccion::redireccionar($_REQUEST['opcion'],$_REQUEST);
            
	}else
	{
            redireccion::redireccionar('no'.$_REQUEST['opcion'],$_REQUEST);
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