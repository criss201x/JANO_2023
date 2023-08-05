<?php

if (!isset($GLOBALS ["autorizado"])) {
    include("../index.php");
    exit();
}

class registrarForm
{

    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miSql;

    function __construct($lenguaje, $formulario, $sql)
    {
        $this->miConfigurador = \Configurador::singleton();

        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;

        $this->miSql = $sql;
    }

    function miForm()
    {

        // Rescatar los datos de este bloque
        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");
        $miPaginaActual = $this->miConfigurador->getVariableConfiguracion('pagina');

        $directorio = $this->miConfigurador->getVariableConfiguracion("host");
        $directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
        $directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");

        $rutaBloque = $this->miConfigurador->getVariableConfiguracion("host");
        $rutaBloque .= $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/";
        $rutaBloque .= $esteBloque ['grupo'] . "/" . $esteBloque ['nombre'];

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
        echo $this->miFormulario->formulario($atributos);

        /*
         * PROCESAR VARIABLES DE CONSULTA
         */ {

        $conexion = "contractual";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $conexionFrameWork = "estructura";
        $DBFrameWork = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexionFrameWork);

        if (isset($_REQUEST ['id_usuario']) && $_REQUEST ['id_usuario'] != '') {
            $id_usuario = $_REQUEST['id_usuario'];
        } else {
            $id_usuario = "";
        }

        if (isset($_REQUEST ['accion']) && $_REQUEST ['accion'] != '') {
            $accion = $_REQUEST['accion'];
        } else {
            $accion = "";
        }

        if (isset($_REQUEST ['ingreso']) && $_REQUEST ['ingreso'] != '') {
            $ingreso = $_REQUEST['ingreso'];
        } else {
            $ingreso = "";
        }

        if (isset($_REQUEST ['consulta']) && $_REQUEST ['consulta'] != '') {
            $consulta = $_REQUEST['consulta'];
        } else {
            $consulta = "";
        }

        if (isset($_REQUEST ['eliminacion']) && $_REQUEST ['eliminacion'] != '') {
            $eliminacion = $_REQUEST['eliminacion'];
        } else {
            $eliminacion = "";
        }

        if (isset($_REQUEST ['registro']) && $_REQUEST ['registro'] != '') {
            $registro = $_REQUEST['registro'];
        } else {
            $registro = "";
        }

        if (isset($_REQUEST ['actualizacion']) && $_REQUEST ['actualizacion'] != '') {
            $actualizacion = $_REQUEST['actualizacion'];
        } else {
            $actualizacion = "";
        }

        if (isset($_REQUEST ['salida']) && $_REQUEST ['salida'] != '') {
            $salida = $_REQUEST['salida'];
        } else {
            $salida = "";
        }

        if (isset($_REQUEST ['solicitud']) && $_REQUEST ['solicitud'] != '') {
            $solicitud = $_REQUEST['solicitud'];
        } else {
            $solicitud = "";
        }

        $arreglo = array(
            'id_usuario' => $id_usuario,
            'accion' => $accion,
            'ingreso' => $ingreso,
            'consulta' => $consulta,
            'eliminacion' => $eliminacion,
            'registro' => $registro,
            'actualizacion' => $actualizacion,
            'salida' => $salida,
            'solicitud' => $solicitud,
        );

        $cadenaSql = $this->miSql->getCadenaSql('consultarGeneralAudit', $arreglo);
        $usuarios = $DBFrameWork->ejecutarAcceso($cadenaSql, "busqueda");
    }


        $arreglo = serialize($arreglo);


        $miPaginaActual = $this->miConfigurador->getVariableConfiguracion('pagina');

        $directorio = $this->miConfigurador->getVariableConfiguracion("host");
        $directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
        $directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");

        $variable = "pagina=" . $miPaginaActual;
        $variable .= "&usuario=" . $_REQUEST ['usuario'];
        $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $directorio);

        // ---------------- SECCION: Controles del Formulario -----------------------------------------------

        $esteCampo = "marcoDatosBasicos";
        $atributos ['id'] = $esteCampo;
        $atributos ["estilo"] = "jqueryui";
        $atributos ['tipoEtiqueta'] = 'inicio';
        $atributos ["leyenda"] = "Log de Usuarios";
        echo $this->miFormulario->marcoAgrupacion('inicio', $atributos);

        $variable = "pagina=" . $miPaginaActual;
        $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $directorio);
        $esteCampo = 'botonRegresar';
        $atributos ['id'] = $esteCampo;
        $atributos ['enlace'] = $variable;
        $atributos ['tabIndex'] = 1;
        $atributos ['estilo'] = 'textoSubtitulo';
        $atributos ['enlaceTexto'] = $this->lenguaje->getCadena($esteCampo);
        $atributos ['ancho'] = '10%';
        $atributos ['alto'] = '10%';
        $atributos ['redirLugar'] = true;
        echo $this->miFormulario->enlace($atributos);
        unset($atributos);
        echo "<br>";

        if ($usuarios) {

            echo "<table id='tablaLog'>";

            echo "<thead>
                             <tr>
                                <th style=\"text-align: center;\">Acción</th>
                                <th style=\"text-align: center;\">ID Registro</th>
                                <th style=\"text-align: center;\">Tipo Log</th>            
                                <th style=\"text-align: center;\">Fecha Evento</th>                       
                                <th style=\"text-align: center;\">Descripción</th>   
                                <th style=\"text-align: center;\">Host</th>
                                <th style=\"text-align: center;\">Consultar Evento</th>
                             </tr>
            </thead>
            <tbody>";

            foreach ($usuarios as $valor) {
                $variable = "pagina=" . $miPaginaActual; // pendiente la pagina para modificar parametro
                $variable .= "&opcion=consultarAudit";
                $variable .= "&id_log=" . $valor ['id_log'];
                $variable .= "&mensaje_titulo=  Usuario: " . $valor ['id_usuario'];
                $variable .= "&responsable=" . $valor ['id_usuario'];
                $variable .= "&usuario=" . $_REQUEST ['usuario'];
                $variable .= "&tiempo=" . $_REQUEST['tiempo'];
                $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $directorio);


                $mostrarHtml = "<tr>
                    <td style=\"text-align: center;\">" . $valor ['accion'] . "</td>";

                $mostrarHtml .= "<td style=\"text-align: center;\">" . $valor ['id_registro'] . "</td>";

                $mostrarHtml .= "<td style=\"text-align: center;\">" . $valor ['tipo_registro'] . "</td>";

                $mostrarHtml .= "<td style=\"text-align: center;\">" . $valor ['fecha_log'] . "</td>";

                $mostrarHtml .= "<td style=\"text-align: center;\">" . $valor ['descripcion'] . "</td>";

                $mostrarHtml .= "<td style=\"text-align: center;\">" . $valor ['host'] . "</td>";

                $mostrarHtml .= "
                    <td><center>
                    	<a href='" . $variable . "'>
                            <img src='" . $rutaBloque . "/css/images/consulta.png' width='15px'>
                        </a>
                  	</center> </td>";

                $mostrarHtml .= "</tr>";
                echo $mostrarHtml;
                unset($mostrarHtml);
                unset($variable);
            }

            echo "</tbody>";

            echo "</table>";


        } else {

            $mensaje = "No Se Encontraron Usuarios<br>Verifique los Parametros de Busqueda";

            // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
            $esteCampo = 'mensajeRegistro';
            $atributos ['id'] = $esteCampo;
            $atributos ['tipo'] = 'error';
            $atributos ['estilo'] = 'textoCentrar';
            $atributos ['mensaje'] = $mensaje;

            $tab++;

            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->cuadroMensaje($atributos);
            // --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
        }

        echo $this->miFormulario->marcoAgrupacion('fin');

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

        $valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
        $valorCodificado .= "&opcion=aprobarContratoMultiple";
        /**
         * SARA permite que los nombres de los campos sean dinámicos.
         * Para ello utiliza la hora en que es creado el formulario para
         * codificar el nombre de cada campo. Si se utiliza esta técnica es necesario pasar dicho tiempo como una variable:
         * (a) invocando a la variable $_REQUEST ['tiempo'] que se ha declarado en ready.php o
         * (b) asociando el tiempo en que se está creando el formulario
         */
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
