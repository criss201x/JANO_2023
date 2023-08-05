<?php

namespace gestionContractual\consultaContratosAprobados\funcion;

if (!isset($GLOBALS ["autorizado"])) {
    include ("index.php");
    exit();
}

class redireccion {

    public static function redireccionar($opcion, $valor = "") {
        $miConfigurador = \Configurador::singleton();
        $miPaginaActual = $miConfigurador->getVariableConfiguracion("pagina");

        switch ($opcion) {


            case "registroActa" :
                $variable = "pagina=" . $miPaginaActual;
                $variable .= "&opcion=mensaje";
                $variable .= "&mensaje=registroActa";
                $variable .= "&numero_contrato=" . $valor ['numero_contrato'];
                $variable .= "&numero_contrato_suscrito=" . $valor ['numero_contrato_suscrito'];
                $variable .= "&vigencia=" . $valor ['vigencia'];
                $variable .= "&usuario=" . $valor ['usuario'];
                break;

            case "noRegistroActa" :
                $variable = "pagina=" . $miPaginaActual;
                $variable .= "&opcion=mensaje";
                $variable .= "&mensaje=noRegistroActa";
                $variable .= "&numero_contrato=" . $valor ['numero_contrato'];
                $variable .= "&numero_contrato_suscrito=" . $valor ['numero_contrato_suscrito'];
                $variable .= "&vigencia=" . $valor ['vigencia'];
                $variable .= "&usuario=" . $valor ['usuario'];
                break;

            case "registroRP" :
                $variable = "pagina=" . $miPaginaActual;
                $variable .= "&opcion=mensaje";
                $variable .= "&mensaje=registroRP";
                $variable .= "&numero_contrato=" . $valor ['numero_contrato'];
                $variable .= "&numero_contrato_suscrito=" . $valor ['numero_contrato_suscrito'];
                $variable .= "&vigencia=" . $valor ['vigencia'];
                $variable .= "&rp=" . $valor ['rp'];
                $variable .= "&usuario=" . $valor ['usuario'];
                $variable .= "&numero_cdp=" . $valor ['numero_cdp'];
                $variable .= "&mensaje_titulo=" . $valor ['mensaje_titulo'];
                $variable .= "&arreglo=" . $valor ['arreglo'];
               
                break;

            case "noregistroRP" :
                $variable = "pagina=" . $miPaginaActual;
                $variable .= "&opcion=mensaje";
                $variable .= "&mensaje=noregistroRP";
                $variable .= "&numero_contrato=" . $valor ['numero_contrato'];
                $variable .= "&numero_contrato_suscrito=" . $valor ['numero_contrato_suscrito'];
                $variable .= "&vigencia=" . $valor ['vigencia'];
                $variable .= "&usuario=" . $valor ['usuario'];
                $variable .= "&numero_cdp=" . $valor ['numero_cdp'];
                $variable .= "&mensaje_titulo=" . $valor ['mensaje_titulo'];
                $variable .= "&arreglo=" . $valor ['arreglo'];
                break;
            case "registroCancelacion" :
                $variable = "pagina=" . $miPaginaActual;
                $variable .= "&opcion=mensaje";
                $variable .= "&mensaje=registroCancelacion";
                $variable .= "&numero_contrato=" . $valor ['numero_contrato'];
                $variable .= "&numero_contrato_suscrito=" . $valor ['numero_contrato_suscrito'];
                $variable .= "&vigencia=" . $valor ['vigencia'];
                $variable .= "&motivo_cancelacion=" . $valor ['motivo_cancelacion'];
                $variable .= "&fecha_cancelacion=" . $valor ['fecha_cancelacion'];
                $variable .= "&usuario=" . $valor ['usuario'];
                break;

            case "noregistroCancelacion" :
                $variable = "pagina=" . $miPaginaActual;
                $variable .= "&opcion=mensaje";
                $variable .= "&mensaje=noregistroCancelacion";
                $variable .= "&numero_contrato=" . $valor ['numero_contrato'];
                $variable .= "&numero_contrato_suscrito=" . $valor ['numero_contrato_suscrito'];
                $variable .= "&vigencia=" . $valor ['vigencia'];
                $variable .= "&motivo_cancelacion=" . $valor ['motivo_cancelacion'];
                $variable .= "&fecha_cancelacion=" . $valor ['fecha_cancelacion'];
                $variable .= "&usuario=" . $valor ['usuario'];
                break;


            case "regresar" :
                $variable = "pagina=" . $miPaginaActual;
                break;

            case "paginaPrincipal" :
                $variable = "pagina=" . $miPaginaActual;
                break;
        }

        foreach ($_REQUEST as $clave => $valor) {
            unset($_REQUEST [$clave]);
        }

        $url = $miConfigurador->configuracion ["host"] . $miConfigurador->configuracion ["site"] . "/index.php?";
        $enlace = $miConfigurador->configuracion ['enlace'];
        $variable = $miConfigurador->fabricaConexiones->crypto->codificar($variable);
        $_REQUEST [$enlace] = $enlace . '=' . $variable;
        $redireccion = $url . $_REQUEST [$enlace];

        echo "<script>location.replace('" . $redireccion . "')</script>";
    }

}

?>