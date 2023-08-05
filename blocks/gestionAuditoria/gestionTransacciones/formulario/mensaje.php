<?php

if (!isset($GLOBALS ["autorizado"])) {
    include ("../index.php");
    exit();
}

class registrarForm {

    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miSql;

    function __construct($lenguaje, $formulario, $sql) {
        $this->miConfigurador = \Configurador::singleton();

        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;

        $this->miSql = $sql;
    }

    function miForm() {
        // Rescatar los datos de este bloque
        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

        // ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
        /**
         * Atributos que deben ser aplicados a todos los controles de este formulario.
         * Se utiliza un arreglo
         * independiente debido a que los atributos individuales se reinician cada vez que se declara un campo.
         *
         * Si se utiliza esta técnica es necesario realizar un mezcla entre este arreglo y el específico en cada control:
         * $atributos= array_merge($atributos,$atributosGlobales);
         */
        $atributosGlobales ['campoSeguro'] = 'true';

        // -------------------------------------------------------------------------------------------------
        // Limpia Items Tabla temporal
        // $cadenaSql = $this->miSql->getCadenaSql ( 'limpiar_tabla_items' );
        // $resultado_secuancia = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
        // ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
        $esteCampo = $esteBloque ['nombre'];
        $atributos ['id'] = $esteCampo;
        $atributos ['nombre'] = $esteCampo;
        // Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
        $atributos ['tipoFormulario'] = 'multipart/form-data';
        // Si no se coloca, entonces toma el valor predeterminado 'POST'
        $atributos ['metodo'] = 'POST';
        // Si no se coloca, entonces toma el valor predeterminado 'index.php' (Recomendado)
        $atributos ['action'] = 'index.php';
        // $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo );
        // Si no se coloca, entonces toma el valor predeterminado.
        $atributos ['estilo'] = '';
        $atributos ['marco'] = true;
        $tab = 1;
        // ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
        // ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
        $atributos ['tipoEtiqueta'] = 'inicio';
        echo $this->miFormulario->formulario($atributos); {
            // ---------------- SECCION: Controles del Formulario -----------------------------------------------

            $miPaginaActual = $this->miConfigurador->getVariableConfiguracion('pagina');

            $directorio = $this->miConfigurador->getVariableConfiguracion("host");
            $directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
            $directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");



            $esteCampo = "marcoDatosBasicos";
            $atributos ['id'] = $esteCampo;
            $atributos ["estilo"] = "jqueryui";
            $atributos ['tipoEtiqueta'] = 'inicio';
            echo $this->miFormulario->marcoAgrupacion('inicio', $atributos);

            // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
            {


                if ($_REQUEST ['mensaje'] == 'registroActa') {

                    $mensaje = "<h3>SE REGISTRO EL ACTA DE INICO</h3> <br>"
                            . " <h4>NUMERO  DE CONTRATO: " . $_REQUEST['numero_contrato_suscrito'] . "<br>"
                            . " VIGENCIA: " . $_REQUEST['vigencia'] . "<br>"
                            . " FECHA DE REGISTRO: " . date("Y-m-d") . "<br>"
                            . " USUARIO QUE REALIZO EL REGISTRO: " . $_REQUEST['usuario'] . "</h4><br>";

                    $mensaje .="<h2> EL ESTADO DEL CONTRATO SE ACTUALIZO A 'EN EJECUCIÓN'<br>";


                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'mensajeRegistro';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['tipo'] = 'success';
                    $atributos ['estilo'] = 'textoCentrar';
                    $atributos ['mensaje'] = $mensaje;

                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->cuadroMensaje($atributos);


                    $variable = "pagina=" . $miPaginaActual; // pendiente la pagina para modificar parametro
                    $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $directorio);


                    // --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
                }
                if ($_REQUEST ['mensaje'] == 'noRegistroActa') {

                    $mensaje = "<h3>NO SE REGISTRO EL ACTA DE INICO</h3> <br>"
                            . " <h4>NUMERO  DE CONTRATO: " . $_REQUEST['numero_contrato_suscrito'] . "<br>"
                            . " VIGENCIA: " . $_REQUEST['vigencia'] . "<br>";


                    $mensaje .="<h2> VERIFIQUE LA INFORMACIÓN E INTENTE DE NUEVO<br>";

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'mensajeRegistro';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['tipo'] = 'error';
                    $atributos ['estilo'] = 'textoCentrar';
                    $atributos ['mensaje'] = $mensaje;

                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->cuadroMensaje($atributos);

                    $variable = "pagina=" . $miPaginaActual; // pendiente la pagina para modificar parametro
                    $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $directorio);

                    // --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
                }if ($_REQUEST ['mensaje'] == 'registroRP') {

                    $mensaje = "<h3>SE REGISTRO EL REGISTRO PRESUPUESTAL " . $_REQUEST['rp'] . " AL CONTRATO.</h3> <br>"
                            . " <h4>NUMERO  DE CONTRATO: " . $_REQUEST['numero_contrato_suscrito'] . "<br>"
                            . " VIGENCIA: " . $_REQUEST['vigencia'] . "<br>"
                            . " FECHA DE REGISTRO: " . date("Y-m-d") . "<br>"
                            . " USUARIO QUE REALIZO EL REGISTRO: " . $_REQUEST['usuario'] . "</h4><br>";

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'mensajeRegistro';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['tipo'] = 'success';
                    $atributos ['estilo'] = 'textoCentrar';
                    $atributos ['mensaje'] = $mensaje;

                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->cuadroMensaje($atributos);

                    $variable = "pagina=" . $miPaginaActual; // pendiente la pagina para modificar parametro
                    $variable .= "&opcion=resgitroRp";
                    $variable .= "&usuario=" . $_REQUEST ['usuario'];
                    $variable .= "&vigencia=" . $_REQUEST ['vigencia'];
                    $variable .= "&numero_contrato=" . $_REQUEST ['numero_contrato'];
                    $variable .= "&numerocontrato=" . $_REQUEST ['numero_contrato'];
                    $variable .= "&arreglo=" . $_REQUEST ['arreglo'];
                    $variable .= "&numero_contrato_suscrito=" . $_REQUEST ['numero_contrato_suscrito'];
                    $variable .= "&mensaje_titulo=" . $_REQUEST['mensaje_titulo'];
                    $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $directorio);

                    // --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
                }
                if ($_REQUEST ['mensaje'] == 'noregistroRP') {

                    $mensaje = "<h3>NO SE REGISTRO REGISTRO PRESUPUESTAL</h3> <br>"
                            . " <h4>NUMERO  DE CONTRATO: " . $_REQUEST['numero_contrato_suscrito'] . "<br>"
                            . " VIGENCIA: " . $_REQUEST['vigencia'] . "<br>";


                    $mensaje .="<h2> VERIFIQUE LA INFORMACIÓN E INTENTE DE NUEVO<br>";

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'mensajeRegistro';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['tipo'] = 'error';
                    $atributos ['estilo'] = 'textoCentrar';
                    $atributos ['mensaje'] = $mensaje;

                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->cuadroMensaje($atributos);

                    $variable = "pagina=" . $miPaginaActual; // pendiente la pagina para modificar parametro
                    $variable .= "&opcion=resgitroRp";
                    $variable .= "&usuario=" . $_REQUEST ['usuario'];
                    $variable .= "&vigencia=" . $_REQUEST ['vigencia'];
                    $variable .= "&numero_contrato=" . $_REQUEST ['numero_contrato'];
                    $variable .= "&numerocontrato=" . $_REQUEST ['numero_contrato'];
                    $variable .= "&arreglo=" . $_REQUEST ['arreglo'];
                    $variable .= "&numero_contrato_suscrito=" . $_REQUEST ['numero_contrato_suscrito'];
                    $variable .= "&mensaje_titulo=" . $_REQUEST['mensaje_titulo'];
                    $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $directorio);


                    // --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
                }

                if ($_REQUEST ['mensaje'] == 'registroCancelacion') {

                    $mensaje = "<h3>SE LLEVO A CABO LA CANCELACIÓN DEL CONTRATO </h3> <br>"
                            . " <h4>NUMERO  DE CONTRATO: " . $_REQUEST['numero_contrato_suscrito'] . "<br>"
                            . " VIGENCIA: " . $_REQUEST['vigencia'] . "<br>"
                            . " FECHA DE CANCELACION: " . $_REQUEST['fecha_cancelacion'] . "<br>"
                            . " MOTIVO DE CANCELACION: " . $_REQUEST['motivo_cancelacion'] . "<br>"
                            . " USUARIO QUE REALIZO LA CANCELACIÓN: " . $_REQUEST['usuario'] . "</h4><br>";

                    $mensaje .="<h2> EL ESTADO DEL CONTRATO SE ACTUALIZO A 'CANCELADO'<br>";


                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'mensajeRegistro';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['tipo'] = 'success';
                    $atributos ['estilo'] = 'textoCentrar';
                    $atributos ['mensaje'] = $mensaje;

                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->cuadroMensaje($atributos);

                    $variable = "pagina=" . $miPaginaActual; // pendiente la pagina para modificar parametro
                    $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $directorio);

                    // --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
                }
                if ($_REQUEST ['mensaje'] == 'noregistroCancelacion') {

                    $mensaje = "<h3>NO SE PUDO REGISTRAR LA CANCELACION</h3> <br>"
                            . " <h4>NUMERO  DE CONTRATO: " . $_REQUEST['numero_contrato_suscrito'] . "<br>"
                            . " VIGENCIA: " . $_REQUEST['vigencia'] . "<br>";


                    $mensaje .="<h2> VERIFIQUE LA INFORMACIÓN E INTENTE DE NUEVO<br>";

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'mensajeRegistro';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['tipo'] = 'error';
                    $atributos ['estilo'] = 'textoCentrar';
                    $atributos ['mensaje'] = $mensaje;

                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->cuadroMensaje($atributos);

                    $variable = "pagina=" . $miPaginaActual; // pendiente la pagina para modificar parametro
                    $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $directorio);

                    // --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
                }
            }

            // ------------------Division para los botones-------------------------
            $atributos ["id"] = "botones";
            $atributos ["estilo"] = "marcoBotones";
            echo $this->miFormulario->division("inicio", $atributos);

            // -----------------CONTROL: Botón ----------------------------------------------------------------
            $esteCampo = 'botonContinuar';
            $atributos ['id'] = $esteCampo;
            $atributos ['enlace'] = $variable;
            $atributos ['tabIndex'] = 1;
            $atributos ['estilo'] = 'jqueryui';
            $atributos ['enlaceTexto'] = $this->lenguaje->getCadena($esteCampo);
            $atributos ['ancho'] = '10%';
            $atributos ['alto'] = '10%';
            $atributos ['redirLugar'] = true;
            echo $this->miFormulario->enlace($atributos);
            // -----------------FIN CONTROL: Botón -----------------------------------------------------------

            echo $this->miFormulario->marcoAgrupacion('fin');

            // ---------------- SECCION: División ----------------------------------------------------------
            $esteCampo = 'division1';
            $atributos ['id'] = $esteCampo;
            $atributos ['estilo'] = 'general';
            echo $this->miFormulario->division("inicio", $atributos);

            // ---------------- FIN SECCION: División ----------------------------------------------------------
            echo $this->miFormulario->division('fin');

            // ---------------- FIN SECCION: Controles del Formulario -------------------------------------------
            // ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
            // Se debe declarar el mismo atributo de marco con que se inició el formulario.
        }

        // -----------------FIN CONTROL: Botón -----------------------------------------------------------
        // ------------------Fin Division para los botones-------------------------
        echo $this->miFormulario->division("fin");

        // ------------------- SECCION: Paso de variables ------------------------------------------------

        /**
         * En algunas ocasiones es útil pasar variables entre las diferentes páginas.
         * SARA permite realizar esto a través de tres
         * mecanismos:
         * (a). Registrando las variables como variables de sesión. Estarán disponibles durante toda la sesión de usuario. Requiere acceso a
         * la base de datos.
         * (b). Incluirlas de manera codificada como campos de los formularios. Para ello se utiliza un campo especial denominado
         * formsara, cuyo valor será una cadena codificada que contiene las variables.
         * (c) a través de campos ocultos en los formularios. (deprecated)
         */
        // En este formulario se utiliza el mecanismo (b) para pasar las siguientes variables:
        // Paso 1: crear el listado de variables

        $valorCodificado = "action=" . $esteBloque ["nombre"];
        $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
        $valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
        $valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];

        /**
         * SARA permite que los nombres de los campos sean dinámicos.
         * Para ello utiliza la hora en que es creado el formulario para
         * codificar el nombre de cada campo. Si se utiliza esta técnica es necesario pasar dicho tiempo como una variable:
         * (a) invocando a la variable $_REQUEST ['tiempo'] que se ha declarado en ready.php o
         * (b) asociando el tiempo en que se está creando el formulario
         */
        $valorCodificado .= "&campoSeguro=" . $_REQUEST ['tiempo'];
        $valorCodificado .= "&tiempo=" . time();
        // Paso 2: codificar la cadena resultante
        $valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar($valorCodificado);

        $atributos ["id"] = "formSaraData"; // No cambiar este nombre
        $atributos ["tipo"] = "hidden";
        $atributos ['estilo'] = '';
        $atributos ["obligatorio"] = false;
        $atributos ['marco'] = true;
        $atributos ["etiqueta"] = "";
        $atributos ["valor"] = $valorCodificado;
        echo $this->miFormulario->campoCuadroTexto($atributos);
        unset($atributos);

        $atributos ['marco'] = true;
        $atributos ['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->formulario($atributos);
    }

}

$miSeleccionador = new registrarForm($this->lenguaje, $this->miFormulario, $this->sql);

$miSeleccionador->miForm();
?>
