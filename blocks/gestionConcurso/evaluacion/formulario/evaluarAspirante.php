<?php
if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

use gestionConcurso\evaluacion\funcion\redireccion;

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
			$variable.= "&opcion=detalle";
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
                        unset ( $atributos );

			$esteCampo = "marcoSubsistema";
			$atributos ['id'] = $esteCampo;
			$atributos ["estilo"] = "jqueryui";
			$atributos ['tipoEtiqueta'] = 'inicio';
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
				//var_dump($resultadoInscripcion);

				echo "<div class='cell-border'><table id='tablaConsultaAspirantes' class='table table-striped table-bordered'>";
				echo "<thead>
                                        <tr align='center'>
                                            <th>Concurso</th>
                                            <th>Proyecto curricular</th>
                                            <th>Modalidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>";

                                        $mostrarHtml = "<tr align='center'>
                                                        <td align='left' width='25%'>".$resultadoInscripcion[0]['concurso']."</td>
                                                        <td align='left'>".$resultadoInscripcion[0]['perfil']."</td>
                                                        <td align='left'>".$resultadoInscripcion[0]['modalidad']."</td>";

					 $mostrarHtml .= "</tr>";
                                        $mostrarHtml .= "<tr>
                                                            <th>Requisitos</th>
                                                            <td colspan='2'>" . $resultadoInscripcion[0] ['requisitos'] . "</td>
                                                        </tr>"; 
					 echo $mostrarHtml;
					 unset($mostrarHtml);
					 echo "</tbody>";
					 echo "</table></div>";


					 echo "<div class='cell-border'><table id='tablaConsultaAspirantes' class='table table-striped table-bordered'>";
	 				 echo "<thead>
	 						<tr align='center'>
                                                            <th>Inscripción</th>
                                                            <th>Identificación</th>
                                                            <th>Aspirante</th>
                                                            <th>Hoja de Vida</th>
	 						</tr>
	 						</thead>
	 						<tbody>";

					$mostrarHtml = "<tr align='center'>
                                                            <td align='left'>".$resultadoInscripcion[0]['consecutivo_inscrito']."</td>
                                                            <td align='left'>".$resultadoInscripcion[0]['tipo_identificacion'].$resultadoInscripcion[0]['identificacion']."</td>
                                                            <td align='left'>".$resultadoInscripcion[0]['nombre']." ". $resultadoInscripcion[0]['apellido']."</td>";
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


						$esteCampo = "marcoEvaluacionCriterios";
	 					$atributos ['id'] = $esteCampo;
	 					$atributos ["estilo"] = "jqueryui";
	 					$atributos ['tipoEtiqueta'] = 'inicio';
	 					$atributos ["leyenda"] =  $this->lenguaje->getCadena ( $esteCampo );
	 					echo $this->miFormulario->marcoAgrupacion ( 'inicio', $atributos );
	 					unset ( $atributos );
	 					{
							//consultar roles del usuario
                                                        $parametrous=array(
	 						 'usuario'=> $_REQUEST['usuario'],
							 'hoy'=>date("Y-m-d"),
	 					 	);
							$cadena_sql = $this->miSql->getCadenaSql("consultaRolesUsuario",$parametrous);
							$resultadoRoles= $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");
                                                        $rol='';
                                                        foreach($resultadoRoles as $key=>$value ){
								if($resultadoRoles[$key]['rol']=='Jurado'){
                                                                $rol.=$resultadoRoles[$key]['cod_rol'];
								$valorPuntaje="natural";
								}
								else if(($resultadoRoles[$key]['rol']=='Docencia')
                                                                    ||($resultadoRoles[$key]['rol']=='ILUD')
                                                                    ||($resultadoRoles[$key]['rol']=='Personal')){
                                                                    $rol.="'".$resultadoRoles[$key]['cod_rol']."'";
 								    $valorPuntaje="decimal";
								}else{
									$rol.=$resultadoRoles[$key]['cod_rol'];
								}
                                                                
                                                                if(($key+1) < count($resultadoRoles))
                                                                        { $rol.=",";}
							}

							  	
								$cadena_sql = $this->miSql->getCadenaSql("validarPerfilEspecial", $_REQUEST['consecutivo_perfil']);
								$perfilEspecial= $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");
								$parametro=array(
									'rol'=>$rol,
								   'consecutivo_concurso'=>$_REQUEST['consecutivo_concurso'],
								   'factor'=> 'Competencias profesionales y comunicativas',
									'hoy'=>date("Y-m-d"),
									'tipoEvaluacion'=> ($perfilEspecial)?'evaluacion_perfil_especial': 'concurso_evaluar',
									);

								$cadena_sql = $this->miSql->getCadenaSql("consultaCriteriosRol", $parametro);
								$resultadoCriterios= $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");								
								$totalPuntos=0;
								$tab=1;

								if($resultadoCriterios){
									foreach($resultadoCriterios as $key=>$value ){

										$esteCampo = "marcoArticulo".$value["consecutivo_criterio"];
										$atributos ['id'] = $esteCampo;
										$atributos ["estilo"] = "jqueryui";
										$atributos ['tipoEtiqueta'] = 'inicio';
										$atributos ["leyenda"] =  $value["criterio"];
										echo $this->miFormulario->marcoAgrupacion ( 'inicio', $atributos );
										unset ( $atributos );

										$cadena_sql = $this->miSql->getCadenaSql("capturaSubcriterios", $value["consecutivo_criterio"]);
										$resultadoCapturaSubcriterios = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");
										$capturaSubcriterio = $resultadoCapturaSubcriterios[0]["exists"] == "t";
                                                                            
										// ////////////////Hidden////////////
										$esteCampo = 'id_evaluar'.$key;
										$atributos ["id"] = $esteCampo;
										$atributos ["tipo"] = "hidden";
										$atributos ['estilo'] = '';
										$atributos ['validar'] = 'required';
										$atributos ["obligatorio"] = true;
										$atributos ['marco'] = true;
										$atributos ["etiqueta"] = "";
										$atributos ['valor'] = $resultadoCriterios[$key]['id_evaluar'];
										$atributos = array_merge ( $atributos, $atributosGlobales );
										echo $this->miFormulario->campoCuadroTexto ( $atributos );
										unset ( $atributos );

                                                                                // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
										$esteCampo = 'criterio'.$key;
										$atributos ['id'] = $esteCampo;
										$atributos ['nombre'] = $esteCampo;
										$atributos ['tipo'] = 'hidden';
										$atributos ['estilo'] = 'jqueryui';
										$atributos ['marco'] = true;
										$atributos ['estiloMarco'] = '';
										$atributos ['etiqueta'] = "";
										$atributos ['valor'] = $resultadoCriterios[$key]['criterio'];
										// Aplica atributos globales al control
										$atributos = array_merge ( $atributos, $atributosGlobales );
										echo $this->miFormulario->campoCuadroTexto ( $atributos );
										unset ( $atributos );
										// ---------------- FIN CONTROL: Cuadro de Texto --------------------------------------------------------
                                              
										$cadena_sql = $this->miSql->getCadenaSql("consultarArticulosSubcriterios", $value["consecutivo_criterio"]);
										$resultadoArticulos = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

										if ($resultadoArticulos) {
											foreach ($resultadoArticulos AS $articulo) {
												echo "<h4><b>".$articulo["nombre_articulo"]."</b></h4>";
												$parametro = array(
													'consecutivo_criterio' => $value['consecutivo_criterio'],
													'consecutivo_articulo' => $articulo['consecutivo_articulo'],
												);
												$cadena_sql = $this->miSql->getCadenaSql("consultarItemsSubcriterios", $parametro);;
												$resultadoItems = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

												if ($resultadoItems) {
													foreach ($resultadoItems AS $item) {
														echo "<h5>".$item["nombre_item"]."</h5>";

														$parametro = array(
															'consecutivo_criterio' => $value['consecutivo_criterio'],
															'consecutivo_item' => $item['consecutivo_item'],
															'consecutivo_inscrito' => $_REQUEST["consecutivo_inscrito"]
														);
														$cadena_sql = $this->miSql->getCadenaSql("consultarSubcriterios", $parametro);;
					                					$resultadoSubcriterios = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

														if ($resultadoSubcriterios) {
															echo "<div class='cell-border'><table id='tablaSubcriterios".$item["consecutivo_item"]."' class='table table-striped table-bordered' style='width: 80%'>";
															echo "<thead>";
															echo "<tr align='center'>";
															echo "<th style='width: 70%;'>Subcriterio</th>";
															echo "<th style='width: 10%;'>Puntos por cada item</th>";
															echo "<th style='width: 20%;'>Subtotal puntos por subcriterio</th>";
															echo "</tr>";
															echo "</thead>";
															echo "<tbody>";

															foreach ($resultadoSubcriterios AS $subcriterio) {
																$filtroEvaluacion = array (
																	"consecutivo_subcriterio" => $subcriterio["consecutivo_subcriterio"],
																	"consecutivo_inscrito" => $_REQUEST["consecutivo_inscrito"]
																);

																$cadena_sql = $this->miSql->getCadenaSql("consultarEvaluacionDetalle", $filtroEvaluacion);
																$resultadoEvaluacionDetalle = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

																echo "<tr align='center'>";
																echo "<td align='left'>".$subcriterio["nombre_subcriterio"]."</td>";
																echo "<td align='left'>".$subcriterio["puntos"]."</td>";
																echo "<td align='left'>";

																// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
																$esteCampo = 'calificacion'.$subcriterio["consecutivo_subcriterio"];
																$atributos ['id'] = $esteCampo;
																$atributos ['nombre'] = $esteCampo;
																$atributos ['tipo'] = 'text';
																$atributos ['estilo'] = 'jqueryui';
																$atributos ['marco'] = true;
																$atributos ['estiloMarco'] = ' number'.$item["consecutivo_item"];
																$atributos ["etiquetaObligatorio"] = true;
																$atributos ['columnas'] = 2;
																$atributos ['dobleLinea'] = 0;
																$atributos ['tabIndex'] = $tab;
																$atributos ['validar']="custom[number], min[0]";
																$atributos ['valor'] = $resultadoEvaluacionDetalle ? $resultadoEvaluacionDetalle[0]["calificacion"] : '';
																$atributos ['titulo'] = "Total puntos del subcriterio, p.e. si el aspirante cumple con este criterio 2 veces, debera ingresar " . ($subcriterio["puntos"]*2);
																$atributos ['deshabilitado'] = false;
																$atributos ['maximoTamanno'] = '';
																$atributos ['evento'] = 'oninput="calcularTotal('.$item["consecutivo_item"].','.$value["consecutivo_criterio"].')"';
																$tab ++;
																// Aplica atributos globales al control
																$atributos = array_merge ( $atributos, $atributosGlobales );
																echo $this->miFormulario->campoCuadroTexto ( $atributos );
																unset ( $atributos );
																// ---------------- FIN CONTROL: Cuadro de Texto --------------------------------------------------------
							
																echo "</td>";
																echo "</tr>";
															}

															echo "</tbody>";
															echo "<tfoot>";
															echo "<th colspan='2' style='text-align:right;'>Total:</th>";
															// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
															$esteCampo = 'total'.$item["consecutivo_item"];
															$atributos ['id'] = $esteCampo;
															$atributos ['nombre'] = $esteCampo;
															$atributos ['tipo'] = 'text';
															$atributos ['estilo'] = 'jqueryui';
															$atributos ['marco'] = true;
															$atributos ['estiloMarco'] = 'subtotal'.$value["consecutivo_criterio"].' total'.$item["consecutivo_item"];
															$atributos ["etiquetaObligatorio"] = true;
															$atributos ['columnas'] = 2;
															$atributos ['dobleLinea'] = 0;
															$atributos ['tabIndex'] = $tab;
															$atributos ['validar']="required, custom[number], min[0]";
															$atributos ['valor'] = 0;
															$atributos ['deshabilitado'] = true;
															$atributos ['tamanno'] = 8;
															$atributos ['maximoTamanno'] = '';
															$atributos ['anchoEtiqueta'] = 350;
															$tab ++;
															// Aplica atributos globales al control
															$atributos = array_merge ( $atributos, $atributosGlobales );
															echo "<th>".$this->miFormulario->campoCuadroTexto ( $atributos )."</th>";
															unset ( $atributos );
															// ---------------- FIN CONTROL: Cuadro de Texto --------------------------------------------------------

															echo "</tfoot>";
															echo "</table></div>";
														}
													}
												}
											}
										}
										// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
										$esteCampo = 'puntaje'.$key;
										$atributos ['id'] = $esteCampo;
										$atributos ['nombre'] = $esteCampo;
										$atributos ['tipo'] = 'text';
										$atributos ['estilo'] = 'jqueryui';
										$atributos ['marco'] = true;
										$atributos ['estiloMarco'] = '';
										$atributos ["etiquetaObligatorio"] = !$capturaSubcriterio;
										$atributos ['columnas'] = 2;
										$atributos ['dobleLinea'] = 0;
										$atributos ['tabIndex'] = $tab;
										$atributos ['etiqueta'] = "Total puntos (maximo ".number_format($resultadoCriterios[$key]['maximo_puntos'],0)." puntos):";
										if (!$capturaSubcriterio) {
											$atributos ['validar']="required, custom[number], min[0], max[".$resultadoCriterios[$key]['maximo_puntos']."]";
										}
										$atributos ['valor'] = '';
										$atributos ['titulo'] = $capturaSubcriterio ? "Este puntaje se calcula una vez se realice el cierre de la fase y se capturen los totales para todos los aspirantes, el valor asignado sera ponderado de acuerdo con el aspirante que mas puntos acumule para este criterio" : "Puntaje para ".$resultadoCriterios[$key]['criterio'];
										$atributos ['deshabilitado'] = $capturaSubcriterio;
										$atributos ['tamanno'] = 8;
										$atributos ['maximoTamanno'] = '';
										$atributos ['anchoEtiqueta'] = 250;
										$tab ++;
										// Aplica atributos globales al control
										$atributos = array_merge ( $atributos, $atributosGlobales );
										echo $this->miFormulario->campoCuadroTexto ( $atributos );
										unset ( $atributos );
										// ---------------- FIN CONTROL: Cuadro de Texto --------------------------------------------------------

										if ($capturaSubcriterio) {
											echo "<label style='padding: 8px 5px;'>Sumatoria puntos de subcriterios: <span id='totalCriterio".$value["consecutivo_criterio"]."'>0</span> </label>";
											// ////////////////Hidden////////////
											$esteCampo = 'subtotalCriterio' . $value["consecutivo_criterio"];
											$atributos ["id"] = $esteCampo;
											$atributos ["tipo"] = "hidden";
											$atributos ['estilo'] = 'subtotalCriterio' . $value["consecutivo_criterio"];
											$atributos ['validar'] = 'required';
											$atributos ["obligatorio"] = true;
											$atributos ['marco'] = true;
											$atributos ["etiqueta"] = "";
											$atributos ['valor'] = 0;
											$atributos = array_merge ( $atributos, $atributosGlobales );
											echo $this->miFormulario->campoCuadroTexto ( $atributos );
											unset ( $atributos );
										}

										// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
										$esteCampo = 'observaciones'.$key;
										$atributos ['id'] = $esteCampo;
										$atributos ['nombre'] = $esteCampo;
										$atributos ['tipo'] = 'text';
										$atributos ['estilo'] = 'jqueryui';
										$atributos ['marco'] = true;
										$atributos ['estiloMarco'] = '';
										$atributos ["etiquetaObligatorio"] = true;
										$atributos ['columnas'] = 140;
										$atributos ['filas'] = 3;
										$atributos ['dobleLinea'] = 0;
										$atributos ['tabIndex'] = $tab;
										$atributos ['etiqueta'] = "Observaciones";
										$atributos ['validar'] = 'maxSize[3000]';
										$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
										$atributos ['deshabilitado'] = false;
										$atributos ['tamanno'] = 60;
										$atributos ['maximoTamanno'] = '';
										$atributos ['anchoEtiqueta'] = 170;
										$tab ++;

										// Aplica atributos globales al control
										$atributos = array_merge ( $atributos, $atributosGlobales );
										echo $this->miFormulario->campoTextArea ( $atributos );
										unset ( $atributos );
										// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------

                                        echo $this->miFormulario->marcoAgrupacion ( 'fin' );                                         
									}

									echo "</div>";
									//echo count($resultadoCriterios);
									$valor=count($resultadoCriterios);

									// ////////////////Hidden////////////
									$esteCampo = 'numeroCriterios';
									$atributos ["id"] = $esteCampo;
									$atributos ["tipo"] = "hidden";
									$atributos ['estilo'] = '';
									$atributos ['validar'] = 'required';
									$atributos ["obligatorio"] = true;
									$atributos ['marco'] = true;
									$atributos ["etiqueta"] = "";
									$atributos ['valor'] = $valor;
									$atributos = array_merge ( $atributos, $atributosGlobales );
									echo $this->miFormulario->campoCuadroTexto ( $atributos );
									unset ( $atributos );

									// ------------------Division para los botones-------------------------
									$atributos ["id"] = "botones";
									$atributos ["estilo"] = "marcoBotones";
									echo $this->miFormulario->division ( "inicio", $atributos );
									unset  ( $atributos );
									{


										// -----------------CONTROL: Botón ----------------------------------------------------------------
										$esteCampo = 'botonGuardar';
										$atributos ["id"] = $esteCampo;
										$atributos ["tabIndex"] = $tab;
										$atributos ["tipo"] = 'boton';
										// submit: no se coloca si se desea un tipo button genérico
										$atributos ['submit'] = true;
										$atributos ["estiloMarco"] = '';
										$atributos ["estiloBoton"] = 'jqueryui';
										// verificar: true para verificar el formulario antes de pasarlo al servidor.
										$atributos ["verificar"] = '';
										$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
										$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
										$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
										$tab ++;

										// Aplica atributos globales al control
										$atributos = array_merge ( $atributos, $atributosGlobales );
										echo $this->miFormulario->campoBoton ( $atributos );
										unset ( $atributos );
										// -----------------FIN CONTROL: Botón -----------------------------------------------------------


									}
									echo $this->miFormulario->division ( 'fin' );

								}else{
									$atributos["id"]="divNoEncontroInscrito";
									$atributos["estilo"]="";
									//$atributos["estiloEnLinea"]="display:none";
									echo $this->miFormulario->division("inicio",$atributos);

									//-------------Control Boton-----------------------
									$esteCampo = "noEncontroCriteriosParaEvaluar";
									$atributos["id"] = $esteCampo; //Cambiar este nombre y el estilo si no se desea mostrar los mensajes animados
									$atributos["etiqueta"] = "";
									$atributos["estilo"] = "centrar";
									$atributos["tipo"] = 'error';
									$atributos["mensaje"] = $this->lenguaje->getCadena($esteCampo);;
									echo $this->miFormulario->cuadroMensaje($atributos);
									unset($atributos);
									//-------------Fin Control Boton----------------------

								 echo $this->miFormulario->division("fin");
									//------------------Division para los botones-------------------------
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
                        $valorCodificado = "";
			//$valorCodificado = "action=" . $esteBloque ["nombre"];//envia directo a funcion
			$valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
			$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
			$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
			$valorCodificado .= "&opcion=guardarEvaluacion";
			$valorCodificado .= "&consecutivo_inscrito=".$_REQUEST['consecutivo_inscrito'];
			$valorCodificado .= "&consecutivo_perfil=".$_REQUEST['consecutivo_perfil'];
			$valorCodificado .= "&consecutivo_concurso=".$_REQUEST['consecutivo_concurso'];
			$valorCodificado .= "&usuario=".$this->miSesion->getSesionUsuarioId();


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