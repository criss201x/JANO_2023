<?php

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

/**
 * Este script está incluido en el método html de la clase Frontera.class.php.
 *
 * La ruta absoluta del bloque está definida en $this->ruta
 */

$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );

$nombreFormulario = $esteBloque ["nombre"];

include_once ("core/crypto/Encriptador.class.php");
$cripto = Encriptador::singleton ();
$valorCodificado = "action=" . $esteBloque ["nombre"];
$valorCodificado .= "&bloque=" . $esteBloque ["id_bloque"];
$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$valorCodificado = $cripto->codificar ( $valorCodificado );
$directorio = $this->miConfigurador->getVariableConfiguracion ( "rutaUrlBloque" ) . "/imagen/";

// ------------------Division para las pestañas-------------------------
$atributos ["id"] = "tabs";
$atributos ["estilo"] = "";
echo $this->miFormulario->division ( "inicio", $atributos );
// unset ( $atributos );
{
	// -------------------- Listado de Pestañas (Como lista No Ordenada) -------------------------------
	$items = array (
			"tabCriterios" => $this->lenguaje->getCadena ( "tabCriterios" ),
			//"tabRegistrarMasivo" => $this->lenguaje->getCadena ( "tabRegistrarMasivo" ) 
	);
	$atributos ["items"] = $items;
	$atributos ["estilo"] = "jqueryui";
	$atributos ["pestañas"] = "true";
	echo $this->miFormulario->listaNoOrdenada ( $atributos );
	// unset ( $atributos );
	
	$atributos ["id"] = "tabPerfil";
	$atributos ["estilo"] = "";
	echo $this->miFormulario->division ( "inicio", $atributos );
	{
		switch ($_REQUEST ['opcion']){       
			case "nuevo":
				include ($this->ruta . "formulario/tabs/registro.php");
				break;
			case "nuevoCriterio":
				include ($this->ruta . "formulario/tabs/registroCriterio.php");
				break;
			case "nuevoFactor":
				include ($this->ruta . "formulario/tabs/registroFactor.php");
                                break; 
			case "nuevoRol":
				include ($this->ruta . "formulario/tabs/registroCriterioRol.php");
                                break; 
                        case "editar":
                            include ($this->ruta . "formulario/tabs/edita.php");
                            break;
        }

		// -----------------Fin Division para la pestaña 1-------------------------
	}
	echo $this->miFormulario->division ( "fin" );
	
	// ------------------Division para la pestaña 2-------------------------

	
	// -----------------Fin Division para la pestaña 2-------------------------
	echo $this->miFormulario->division ( "fin" );
	
}

echo $this->miFormulario->division ( "fin" );

?>
