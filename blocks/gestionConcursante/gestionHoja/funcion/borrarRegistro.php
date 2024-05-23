<?php

namespace gestionConcursante\gestionHoja\funcion;

use gestionConcursante\gestionHoja\funcion\redireccion;

include_once('redireccionar.php');


if (!isset($GLOBALS ["autorizado"])) {
    include("../index.php");
    exit();
}

class borrarRegistro
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
        if (isset($_REQUEST['botonCancelar2']) && $_REQUEST['botonCancelar2'] == 'true') {
            redireccion::redireccionar('devolver', $_REQUEST['tipo']);
            exit ();
        }

        $SQLs = [];

        $conexion = "estructura";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $arregloDatos = array('id_usuario' => $_REQUEST['id_usuario'],
            'consecutivo' => $_REQUEST['consecutivo'],
            'persona' => $_REQUEST['persona'],
            'tipo' => $_REQUEST['tipo'],
        );

        switch ($_REQUEST['accion']) {
            case 'borrarFormacion':
                $arregloDatos['dato'] = ' Formación Académica ';
                $cadenaSql = $this->miSql->getCadenaSql('borrarFormacion', $arregloDatos);
                $SQLs[] = $cadenaSql;
                break;
            case 'borrarProfesional':
                $arregloDatos['dato'] = ' Experiencia Profesional equivalente';
                $cadenaSql = $this->miSql->getCadenaSql('borrarProfesional', $arregloDatos);
                $SQLs[] = $cadenaSql;
                break;
            case 'borrarDocencia':
                $arregloDatos['dato'] = ' Experiencia Docente Universitaria';
                $cadenaSql = $this->miSql->getCadenaSql('borrarDocencia', $arregloDatos);
                $SQLs[] = $cadenaSql;
                break;
            case 'borrarActividad':
                $arregloDatos['dato'] = ' Producto de Investigación y/o Investigación - Creación ';
                $cadenaSql = $this->miSql->getCadenaSql('borrarActividad', $arregloDatos);
                $SQLs[] = $cadenaSql;
                break;
            case 'borrarInvestigacion':
                $arregloDatos['dato'] = ' grupo de investigación ';
                $cadenaSql = $this->miSql->getCadenaSql('borrarInvestigacion', $arregloDatos);
                $SQLs[] = $cadenaSql;
                break;
            case 'borrarProduccion':
                $arregloDatos['dato'] = ' Publicación - Obras Artística ';
                $cadenaSql = $this->miSql->getCadenaSql('borrarProduccion', $arregloDatos);
                $SQLs[] = $cadenaSql;
                break;
            case 'borrarIdiomas':
                $arregloDatos['dato'] = ' Lengua Extranjera ';
                $cadenaSql = $this->miSql->getCadenaSql('borrarIdiomas', $arregloDatos);
                $SQLs[] = $cadenaSql;
                break;

        }
        $resultadoBorrar = $esteRecursoDB->ejecutarAcceso($cadenaSql, "eliminar", $arregloDatos, $_REQUEST['accion']);
        if ($resultadoBorrar) {
            redireccion::redireccionar('borro', $arregloDatos);
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

            redireccion::redireccionar('noBorro', $arregloDatos);
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

$miRegistrador = new borrarRegistro($this->lenguaje, $this->sql, $this->funcion, $this->miLogger, $this->miArchivo);

$resultado = $miRegistrador->procesarFormulario();
?>