<?php
if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

use gestionConcurso\reclamacionesEvaluaciones\funcion\redireccion;

class registrarForm {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miSql;
	var $miSesion;
	function __construct($lenguaje, $formulario, $sql) {
		$this->miConfigurador = \Configurador::singleton ();

		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );

		$this->lenguaje = $lenguaje;

		$this->miFormulario = $formulario;

		$this->miSql = $sql;

		$this->miSesion = \Sesion::singleton();
	}
	function miForm() {

		// Rescatar los datos de este bloque
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );

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

		$_REQUEST ['tiempo'] = time ();
		$tiempo = $_REQUEST ['tiempo'];

		// lineas para conectar base de d atos-------------------------------------------------------------------------------------------------
		$conexion = "estructura";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
                $seccion ['tiempo'] = $tiempo;
    
                                
    

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
		$atributos ['marco'] = false;
		$tab = 1;
		// ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
		// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
		$atributos ['tipoEtiqueta'] = 'inicio';
		echo $this->miFormulario->formulario ( $atributos );
		{

			// ---------------- SECCION: Controles del Formulario -----------------------------------------------

			$miPaginaActual = $this->miConfigurador->getVariableConfiguracion ( 'pagina' );

                        $rutaBloque = $this->miConfigurador->getVariableConfiguracion("host");
                        $rutaBloque.=$this->miConfigurador->getVariableConfiguracion("site") . "/blocks/";
                        $rutaBloque.= $esteBloque['grupo'] . "/" . $esteBloque['nombre'];

			$directorio = $this->miConfigurador->getVariableConfiguracion ( "host" );
			$directorio .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
			$directorio .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );

                        $variable = "pagina=" . $miPaginaActual;
                        $variable.= "&opcion=consutarReclamaciones";
                        $variable.= "&usuario=".$_REQUEST['usuario'];
                        $variable.= "&consecutivo_concurso=".$_REQUEST['consecutivo_concurso'];
                        $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $directorio );

                        // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                        $esteCampo = 'botonRegresar';
                        $atributos ['id'] = $esteCampo;
                        $atributos ['enlace'] = $variable;
                        $atributos ['tabIndex'] = 1;
                        $atributos ['enlaceTexto'] = $this->lenguaje->getCadena ( $esteCampo );
                        $atributos ['estilo'] = 'textoPequenno textoGris';
                        $atributos ['enlaceImagen'] = $rutaBloque."/images/player_rew.png";
                        $atributos ['posicionImagen'] = "atras";//"adelante";
                        $atributos ['ancho'] = '30px';
                        $atributos ['alto'] = '30px';
                        $atributos ['redirLugar'] = true;
                        echo $this->miFormulario->enlace ( $atributos );
                        echo "<br><br>";
                        unset ( $atributos );                        
                        
			$esteCampo = "marcoSubsistema";
			$atributos ['id'] = $esteCampo;
			$atributos ["estilo"] = "jqueryui";
			$atributos ['tipoEtiqueta'] = 'inicio';
			//$atributos ["estiloEnLinea"] = "display:none;";
			$atributos ["leyenda"] =  $this->lenguaje->getCadena ( $esteCampo );
			echo $this->miFormulario->marcoAgrupacion ( 'inicio', $atributos );
			unset ( $atributos );
			{

				$parametro=array(
					'consecutivo_inscrito'=>$_REQUEST['consecutivo_inscrito']
				);
				//consultar datos de la inscripción
				$cadena_sql = $this->miSql->getCadenaSql("consultaInscripcion", $parametro);
				$resultadoInscripcion= $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

				//buscar grupo del evaluador
				$parametro=array(
					'reclamacion'=>$_REQUEST['reclamacion'],
					'usuario'=>$_REQUEST['usuario']
				);
				$cadena_sql = $this->miSql->getCadenaSql("consultarDetalleReclamacion2", $parametro);
				$evaluacionParcialReclamacion= $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");
				//var_dump($evaluacionParcialReclamacion);

				$parametro=array(
					'reclamacion'=>$_REQUEST['reclamacion'],
					'grupo'=> $evaluacionParcialReclamacion[0]['id_grupo']
				);
				//consultar evaluación parcial relacionada con la reclamacion
				$cadena_sql = $this->miSql->getCadenaSql("consultaEvaluacionReclamacion", $parametro);
				$resultadoEvaluacionParcialReclamacion= $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");
                //var_dump($resultadoEvaluacionParcialReclamacion);

				echo "<div class='cell-border'><table id='tablaConsultaInscripcion' class='table table-striped table-bordered'>";
				echo "<thead>
								<tr align='center'>
										<th>N° Inscripción</th>
										<th>Identificación</th>
										<th>Aspirante</th>
										<th>Hoja de Vida</th>
								</tr>
						</thead>
						<tbody>";

				$mostrarHtml = "<tr align='center'>
                                                <td align='left'>".$_REQUEST['consecutivo_inscrito']."</td>
                                                <td align='left'>".$resultadoInscripcion[0]['tipo_identificacion'].$resultadoInscripcion[0]['identificacion']."</td>
                                                <td align='left'>".$resultadoInscripcion[0]['nombre'].$resultadoInscripcion[0]['apellido']."</td>";
                                                $mostrarHtml .= "<td>";

                                                $variableVerHoja = "pagina=publicacion";
                                                $variableVerHoja.= "&opcion=hojaVida";
                                                $variableVerHoja.= "&usuario=" . $this->miSesion->getSesionUsuarioId();
                                                $variableVerHoja.= "&id_usuario=" .$_REQUEST['usuario'];
                                                $variableVerHoja.= "&campoSeguro=" . $_REQUEST ['tiempo'];
                                                $variableVerHoja.= "&tiempo=" . time ();
                                                $variableVerHoja .= "&consecutivo_inscrito=".$_REQUEST['consecutivo_inscrito'];
                                                $variableVerHoja .= "&consecutivo_concurso=".$_REQUEST['consecutivo_concurso'];
                                                $variableVerHoja .= "&consecutivo_perfil=".$_REQUEST['consecutivo_perfil'];
                                                $variableVerHoja = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variableVerHoja, $directorio);

                                                                //-------------Enlace-----------------------
                                                                $esteCampo = "verHojaVida";
                                                                $esteCampo = 'enlace_hoja';
                                                                $atributos ['id'] = $esteCampo;
                                                                $atributos ['enlace'] = 'javascript:enlace("ruta_enlace_hoja");';
                                                                $atributos ['tabIndex'] = 0;
                                                                $atributos ['columnas'] = 1;
                                                                $atributos ['enlaceTexto'] = 'Ver Curriculum';
                                                                $atributos ['estilo'] = 'clasico';
                                                                $atributos['enlaceImagen']=$rutaBloque."/images/xmag.png";
                                                                $atributos ['posicionImagen'] ="atras";//"adelante";
                                                                $atributos ['ancho'] = '20px';
                                                                $atributos ['alto'] = '20px';
                                                                $atributos ['redirLugar'] = false;
                                                                $atributos ['valor'] = '';
                                                                $mostrarHtml .= $this->miFormulario->enlace( $atributos );
                                                                unset ( $atributos );
                                                                 // --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
                                                                $esteCampo = 'ruta_enlace_hoja';
                                                                $atributos ['id'] = $esteCampo;
                                                                $atributos ['nombre'] = $esteCampo;
                                                                $atributos ['tipo'] = 'hidden';
                                                                $atributos ['etiqueta'] = "";//$this->lenguaje->getCadena ( $esteCampo );
                                                                $atributos ['obligatorio'] = false;
                                                                $atributos ['valor'] = $variableVerHoja;
                                                                $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
                                                                $atributos ['deshabilitado'] = FALSE;
                                                                $mostrarHtml .= $this->miFormulario->campoCuadroTexto ( $atributos );
                                                                // --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------

                                                 $mostrarHtml .= "</td>";


					 $mostrarHtml .= "</tr>";
					 echo $mostrarHtml;
					 unset($mostrarHtml);
					 echo "</tbody>";
					 echo "</table></div>";

					 $parametro=array(
						'consecutivo_concurso'=>$_REQUEST['consecutivo_concurso'],
						'consecutivo_perfil'=>$_REQUEST['consecutivo_perfil']
					 );
					 $cadena_sql = $this->miSql->getCadenaSql("consultaPerfil", $parametro);
					 $resultadoPerfil= $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

					 $parametro=array(
						 'consecutivo_inscrito'=>$_REQUEST['consecutivo_inscrito'],
						 'reclamacion'=>$_REQUEST['reclamacion']
					 );
					 $cadena_sql = $this->miSql->getCadenaSql("consultaEvaluacionesReclamacion2", $parametro);
					 $validacion = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

					 echo "<div style ='width: 100%; padding-left: 12%; padding-right: 12%;' class='cell-border'><table id='tablaRequisitos' class='table table-striped table-bordered'>";

					echo "<tbody>";

					$mostrarHtml = "<tr >
								<th>Concurso</th>
								<td colspan='1'>".$resultadoPerfil[0]['concurso']."</td>
								<th>Perfil</th>
								<td colspan='1'>".$resultadoPerfil[0]['perfil']."</td>
								</tr>";

								echo $mostrarHtml;
								unset($mostrarHtml);

								echo "</tbody>";
								echo "</table></div>";


                $esteCampo = "marcoNuevaEvaluacion";
                $atributos ['id'] = $esteCampo;
                $atributos ["estilo"] = "jqueryui";
                $atributos ['tipoEtiqueta'] = 'inicio';
                //$atributos ["estiloEnLinea"] = "display:none;";
                $atributos ["leyenda"] =  $this->lenguaje->getCadena ( $esteCampo );
                echo $this->miFormulario->marcoAgrupacion ( 'inicio', $atributos );
                unset ( $atributos );
                {

                //###################################################################

                    $parametro = array ('reclamacion' => $_REQUEST['reclamacion'],'usuario'=>$_REQUEST['usuario']);
                    $cadena_sql = $this->miSql->getCadenaSql ( "consultarDetalleReclamacion", $parametro );
                    $resultadoDetalleReclamacion = $esteRecursoDB->ejecutarAcceso ( $cadena_sql, "busqueda" );

                    $cadena_sql = $this->miSql->getCadenaSql ( "consultarReclamacionConcurso", $parametro );
                    $reclamacionFin = $esteRecursoDB->ejecutarAcceso ( $cadena_sql, "busqueda" );

					if($resultadoDetalleReclamacion){
                    echo "Reclamación # " . $_REQUEST['reclamacion']. " : " . $resultadoDetalleReclamacion [0] ['observacion_reclamacion'] . "<br>";
                    echo "Fecha de la respuesta: " . $resultadoDetalleReclamacion [0] ['evaluacion_fecha'] . "<br><br>";
                    echo "<div style ='padding-left: 5%; padding-right: 5%;' class='cell-border'><table id='tabla' class='table table-striped table-bordered'>";
                    echo "<tbody>";
                        $mostrarHtml = "";
                        $mostrarHtml .= "<tr align='center'>";
                        $mostrarHtml .= "<th colspan='1'>Criterio</th>";
                        $mostrarHtml .= "<th colspan='1'>Calificación Anterior</th>";
                        $mostrarHtml .= "<th colspan='1'>¿Aplica Reclamación?</th>";
                        $mostrarHtml .= "<th colspan='1'>Nueva Calificación</th>";
                        $mostrarHtml .= "<tr>";

                            foreach ( $resultadoDetalleReclamacion as $key => $value ) {

                                $parametro = array ('reclamacion' => $_REQUEST['reclamacion'],'criterio'=>$resultadoDetalleReclamacion[$key]['id_evaluar'],'usuario'=>$_REQUEST['usuario']);
                                $cadena_sql = $this->miSql->getCadenaSql("consultaRespuestaReclamacionCriterio", $parametro);
                                $resultadoRespuestaReclamaciones = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");
                                // buscar datos Inactivos del criterio
                                $cadena_sql = $this->miSql->getCadenaSql("consultaPuntajeInactivo", $parametro);
                                $resultadoPuntajeInactivo = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");
                                //var_dump($resultadoPuntajeInactivo);
                                $puntajeActivo = "NN";
                                if($resultadoPuntajeInactivo){
                                    $puntajeInactivo=$resultadoPuntajeInactivo[0]['puntaje_parcial'];
                                    $puntajeActivo=$resultadoDetalleReclamacion[$key]['puntaje_parcial'];
                                }else{
                                    $puntajeInactivo=$resultadoDetalleReclamacion[$key]['puntaje_parcial'];
                                }
                                $mostrarHtml .= "<tr>
                                <td colspan='1'>" . $resultadoDetalleReclamacion [$key] ['nombre_criterio'] . "</td>
                                <td colspan='1'>" . $puntajeInactivo . "</td>
                                <td colspan='1'>" . $resultadoRespuestaReclamaciones[0]['respuesta'] . "</td>";
                                $mostrarHtml .= "<td colspan='1'>";
                                    if($resultadoRespuestaReclamaciones[0]['respuesta']=='SI'){
                                            $mostrarHtml .= $puntajeActivo;
                                    }else{
                                          $mostrarHtml .= "No Aplica";
                                    }
                                    $mostrarHtml .= "</td>";
                            }
                            echo $mostrarHtml;
                            unset ( $mostrarHtml );
                        //###########################################
                                echo "</tbody>";
                                echo "</table>";
                            echo "</div>";
						}else if($reclamacionFin && $reclamacionFin[0]['consecutivo_actividad']==7){
							echo "Reclamación # " . $_REQUEST['reclamacion']. " : " . $reclamacionFin[0]['reclamo'] . "<br><br>";
							echo "Fecha de la respuesta: " .$reclamacionFin[0]['fecha_registro']. "<br>";
							echo "Respuesta # " . $reclamacionFin[0]['id_rsta']. " : " . $reclamacionFin[0]['observacion'] . "<br>";
						}


                                }
                        echo $this->miFormulario->marcoAgrupacion ( 'fin' );
			}
			echo $this->miFormulario->marcoAgrupacion ( 'fin' );

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

			$valorCodificado = "action=" . $esteBloque ["nombre"];
			$valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
			$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
			$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
			$valorCodificado .= "&opcion=guardarValidacion";
			//$valorCodificado .= "&consecutivo_inscrito=".$_REQUEST['consecutivo_inscrito'];
			//$valorCodificado .= "&consecutivo_concurso=".$_REQUEST['consecutivo_concurso'];
			//$valorCodificado .= "&consecutivo_perfil=".$_REQUEST['consecutivo_perfil'];

			/**
			 * SARA permite que los nombres de los campos sean dinámicos.
			 * Para ello utiliza la hora en que es creado el formulario para
			 * codificar el nombre de cada campo. Si se utiliza esta técnica es necesario pasar dicho tiempo como una variable:
			 * (a) invocando a la variable $_REQUEST ['tiempo'] que se ha declarado en ready.php o
			 * (b) asociando el tiempo en que se está creando el formulario
			 */
			$valorCodificado .= "&campoSeguro=" . $_REQUEST ['tiempo'];
			$valorCodificado .= "&tiempo=" . time ();
			// Paso 2: codificar la cadena resultante
			$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );

			$atributos ["id"] = "formSaraData"; // No cambiar este nombre
			$atributos ["tipo"] = "hidden";
			$atributos ['estilo'] = '';
			$atributos ["obligatorio"] = false;
			$atributos ['marco'] = true;
			$atributos ["etiqueta"] = "";
			$atributos ["valor"] = $valorCodificado;
			echo $this->miFormulario->campoCuadroTexto ( $atributos );
			unset ( $atributos );

			$atributos ['marco'] = true;
			$atributos ['tipoEtiqueta'] = 'fin';
			echo $this->miFormulario->formulario ( $atributos );

			return true;
		}
	}
}

$miSeleccionador = new registrarForm ( $this->lenguaje, $this->miFormulario, $this->sql );

$miSeleccionador->miForm ();
?>
