<?php
use gestionPublicacion\funcion\redireccion;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class consultarProfesional {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miSql;
        var $miSesion;
        var $rutaSoporte;   
	function __construct($lenguaje, $formulario, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miFormulario = $formulario;
		$this->miSql = $sql;
                $this->miSesion = \Sesion::singleton();
                $this->rutaSoporte = $this->miConfigurador->getVariableConfiguracion ( "raizSoportes" );                  
                
	}
	function miForm() {
		
            // Rescatar los datos de este bloque
            $esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
            $rutaBloque = $this->miConfigurador->getVariableConfiguracion("host");
            $rutaBloque.=$this->miConfigurador->getVariableConfiguracion("site") . "/blocks/";
            $rutaBloque.= $esteBloque['grupo'] . "/" . $esteBloque['nombre'];
            $directorio = $this->miConfigurador->getVariableConfiguracion("host");
            $directorio.= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
            $directorio.=$this->miConfigurador->getVariableConfiguracion("enlace");
            $atributosGlobales ['campoSeguro'] = 'true';
            $_REQUEST ['tiempo'] = time ();
            // -------------------------------------------------------------------------------------------------
            //$conexion="estructura";
            $conexion="reportes";
            $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
                //identifca lo roles para la busqueda de subsistemas
            $parametro=array('consecutivo_inscrito'=>$_REQUEST['consecutivo_inscrito'],
                             'tipo_dato'=>'datosExperiencia');    
            $cadena_sql = $this->miSql->getCadenaSql("consultaSoportesInscripcion", $parametro);
            $resultadoProfesional = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");
            //-----BUSCA LOS TIPOS DE SOPORTES PARA EL FORMUALRIO, SEGÚN LOS RELACIONADO EN LA TABLA
            $parametroTipoSop = array('dato_relaciona'=>'datosExperiencia',);
            $cadenaSalud_sql = $this->miSql->getCadenaSql("buscarTipoSoporte", $parametroTipoSop);
            $resultadoTiposop = $esteRecursoDB->ejecutarAcceso($cadenaSalud_sql, "busqueda");
            // ---------------- SECCION: Enlace para soporte -----------------------------------------------
            $variableSoporte = "pagina=gestionarSoportes"; //pendiente la pagina para modificar parametro                                                        
            $variableSoporte.= "&action=gestionarSoportes";
            $variableSoporte.= "&bloque=" . $esteBloque["id_bloque"];
            $variableSoporte.= "&bloqueGrupo=";
            //----
            $esteCampo = "marcoProfesional";
            $atributos ['id'] = $esteCampo;
            $atributos ["estilo"] = "jqueryui";
            $atributos ['tipoEtiqueta'] = 'inicio';
            $atributos ["leyenda"] = "".$this->lenguaje->getCadena ( $esteCampo )."";
            
            echo $this->miFormulario->marcoAgrupacion ( 'inicio', $atributos );
            unset ( $atributos );

            // ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
            $esteCampo = 'datosProfesional';
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
                    if($resultadoProfesional)
                        {   //define las cabeceras de la tablas
                            $columnas = array( 
                                                $this->lenguaje->getCadena ("consecutivo"),
                                                $this->lenguaje->getCadena ("pais_experiencia"),
                                                $this->lenguaje->getCadena ("fecha_inicio"),
                                                $this->lenguaje->getCadena ("fecha_fin"),
                                                $this->lenguaje->getCadena ("tiempo_experiencia"),
                                                $this->lenguaje->getCadena ("cargo"),
                                                $this->lenguaje->getCadena ("descripcion_cargo"),
                                                $this->lenguaje->getCadena ("nombre_institucion_experiencia"),
                                                $this->lenguaje->getCadena ("nivel_institucion"),
                                                $this->lenguaje->getCadena ("telefono_institucion"),
                                                $this->lenguaje->getCadena ("correo_institucion"));
                            foreach ($resultadoTiposop as $tipokey => $value) 
                                {array_push($columnas, $resultadoTiposop[$tipokey]['alias']);} 
                            include('validarSoportesGeneral.php');
                            //-----------------Inicio de Conjunto de Controles----------------------------------------
                                $esteCampo = "marcoProfesional";
                                $atributos["estilo"] = "jqueryui";
                                $atributos["leyenda"] = $this->lenguaje->getCadena($esteCampo);
                                //echo $this->miFormulario->marcoAgrupacion("inicio", $atributos);
                                unset($atributos);
                                echo "<div class='cell-border'><table id='tablaProcesos' class='table table-striped table-bordered'>";
                                echo "<thead>
                                        <tr align='center'>";
                                             foreach ($columnas AS $col)
                                                {echo " <th>$col</th>";}
                                echo "  </tr>
                                    </thead>
                                    <tbody>";
                                foreach($resultadoProfesional as $key=>$value )
                                    {   
                                        $consecutivo_soporte_ins = $value["consecutivo_soporte_ins"];
                                        $datos=json_decode ($resultadoProfesional[$key]['valor_dato']);	
                                        //calcula el tiempo de experiencia
                                        $date1 = new DateTime( $datos->fecha_inicio);
                                        if( $datos->fecha_fin!='')
                                             {$date2 = new DateTime( $datos->fecha_fin);}   
                                        else {$date2 = new DateTime("now");}
                                        $diff[$key] = $date1->diff($date2);                                        
                                    
                                        $mostrarHtml = "<tr align='center'>
                                                <td align='left'>".$consecutivo_soporte_ins."</td>
                                                <td align='left'>".$datos->pais."</td>
                                                <td align='left'>".$datos->fecha_inicio."</td>
                                                <td align='left'>".$datos->fecha_fin."</td>
                                                <td align='left'>".$diff[$key]->days."</td>
                                                <td align='left'>".$datos->cargo."</td>
                                                <td align='left' width='20%'>".$datos->descripcion_cargo."</td>
                                                <td align='left' width='10%'>".$datos->nombre_institucion."</td>
                                                <td align='left'>".$datos->nivel_institucion."</td>
                                                <td align='left'>".$datos->telefono_institucion."</td>
                                                <td align='left'>".str_replace('\\','', $datos->correo_institucion)."</td>";
                                                // --------------- INICIO CONTROLES : Visualizar SOPORTES SEGUN LOS RELACIONADOS --------------------------------------------------
                                                foreach ($resultadoTiposop as $tipokey => $value) 
                                                    {//valida si existen soportes para el tipo
                                                    //se arman las celdas con los soportes existentes
                                                    $mostrarHtml .= "<td> ";
                                                            if(isset($datos->soportes) && $datos->soportes!='')
                                                              {foreach ($datos->soportes as $key => $value)
                                                                 { if(isset($value->tipo_soporte) && $value->tipo_soporte==$resultadoTiposop[$tipokey]['nombre'])
                                                                    {
                                                                    $arrayFile = explode(",",strtolower( $resultadoTiposop[$tipokey]['extencion_permitida']));     
                                                                    if(isset($value->tipo_soporte) && 
                                                                        (in_array(strtolower("png"), $arrayFile) || 
                                                                         in_array(strtolower("jpg"), $arrayFile) ||
                                                                         in_array(strtolower("jpeg"), $arrayFile) ||
                                                                         in_array(strtolower("bmp"), $arrayFile)))
                                                                        {   //Se codifica la imagen
                                                                            $rutaImagen= "file://".$this->rutaSoporte.$value->nombre_soporte;
                                                                            $imagen = file_get_contents ( $rutaImagen );
                                                                            $imagenEncriptada = base64_encode ( $imagen );
                                                                            $url_Image= "data:image;base64," . $imagenEncriptada;
                                                                             // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                                                                            $esteCampo = 'archivoImagen';
                                                                            $atributos ['id'] = $esteCampo;
                                                                            $atributos['imagen']= $url_Image;
                                                                            $atributos['estilo']='campoImagen anchoColumna2';
                                                                            $atributos['etiqueta']='imagen';
                                                                            $atributos['borde']='';
                                                                            $atributos ['ancho'] = '100px';
                                                                            $atributos ['alto'] = '120px';
                                                                            $atributos = array_merge ( $atributos, $atributosGlobales );
                                                                            $mostrarHtml.= $this->miFormulario->campoImagen( $atributos );
                                                                            unset ( $atributos );
                                                                          // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------  
                                                                        }
                                                                    else {      
                                                                            // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                                                                           $esteCampo = 'archivo'.$value->consecutivo_soporte;
                                                                           $atributos ['id'] = $esteCampo;
                                                                           $atributos ['enlace'] = 'javascript:enlaceSop("ruta'.$value->consecutivo_soporte.'");';
                                                                           $atributos ['tabIndex'] = 0;
                                                                           $atributos ['marco'] = true;
                                                                           $atributos ['columnas'] = 2;
                                                                           $atributos ['enlaceTexto'] = $value->alias_soporte;
                                                                           $atributos ['estilo'] = 'textoPequenno textoGris ';
                                                                           $atributos ['enlaceImagen'] = $rutaBloque."/images/pdfImage.png";
                                                                           $atributos ['posicionImagen'] ="atras";//"adelante";
                                                                           $atributos ['ancho'] = '25px';
                                                                           $atributos ['alto'] = '25px';
                                                                           $atributos ['redirLugar'] = false;
                                                                           $atributos ['valor'] = '';
                                                                           $atributos = array_merge ( $atributos, $atributosGlobales );
                                                                           $mostrarHtml.=$this->miFormulario->enlace( $atributos );
                                                                           unset ( $atributos );
                                                                          // --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------  
                                                                             //-------------Inicio preparar enlace soporte-------
                                                                             $verSoporte = $variableSoporte;
                                                                             $verSoporte .= "&opcion=verPdf";
                                                                             $verSoporte .= "&raiz=".$this->rutaSoporte;
                                                                             $verSoporte .= "&ruta=".$value->nombre_soporte;
                                                                             $verSoporte .= "&archivo=";
                                                                             $verSoporte .= "&alias=".$value->alias_soporte;
                                                                             $verSoporte = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $verSoporte, $directorio );
                                                                             //-------------Fin preparar enlace soporte-------
                                                                           $esteCampo = 'ruta'.$value->consecutivo_soporte;
                                                                           $atributos ['id'] = $esteCampo;
                                                                           $atributos ['nombre'] = $esteCampo;
                                                                           $atributos ['tipo'] = 'hidden';
                                                                           $atributos ['estilo'] = '';//jqueryui';
                                                                           $atributos ['marco'] = true;
                                                                           $atributos ['columnas'] = 1;
                                                                           $atributos ['dobleLinea'] = false;
                                                                           $atributos ['tabIndex'] = $tab=0;
                                                                           $atributos ['etiqueta'] = "";//$this->lenguaje->getCadena ( $esteCampo );
                                                                           $atributos ['obligatorio'] = false;
                                                                           $atributos ['etiquetaObligatorio'] = false;
                                                                           $atributos ['validar'] = '';
                                                                           $atributos ['valor'] = $verSoporte;
                                                                           $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
                                                                           $atributos ['deshabilitado'] = FALSE;
                                                                           $atributos ['tamanno'] = 30;
                                                                           $atributos ['anchoCaja'] = 60;
                                                                           $atributos ['maximoTamanno'] = '';
                                                                           $atributos ['anchoEtiqueta'] = 120;
                                                                           //$atributos = array_merge ( $atributos, $atributosGlobales );
                                                                           $mostrarHtml.= $this->miFormulario->campoCuadroTexto ( $atributos );
                                                                           // --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
                                                                           unset ( $atributos );
                                                                        }   

                                                                     }
                                                                }
                                                              }  
                                                        $mostrarHtml .= "</td> ";               
                                                     } 
                                                // --------------- FIN CONTROLES : ver SOPORTES --------------------------------------------------                                              
                                        include('validarSoportes.php');
                                       $mostrarHtml .= "</tr>";
                                       echo $mostrarHtml;
                                       unset($mostrarHtml);
                                       unset($variable);
                                       unset($resultadoDip);
                                       unset($resultadoTarjeta);
                                    }
                                echo "</tbody>";
                                echo "</table></div>";
                                //Fin de Conjunto de Controles

                        }else
                        {
                                include('validarSoportesGeneral.php');                            
                                $atributos["id"]="divNoEncontroProfesional";
                                $atributos["estilo"]="";
                           //$atributos["estiloEnLinea"]="display:none"; 
                                echo $this->miFormulario->division("inicio",$atributos);

                                //-------------Control Boton-----------------------
                                $esteCampo = "noEncontroProfesional";
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

			$valorCodificado = "action=" . $esteBloque ['nombre'];
			$valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
			$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
			$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
			$valorCodificado .= "&opcion=guardarValidacion";
			$valorCodificado .= "&consecutivo_inscrito=".$_REQUEST['consecutivo_inscrito'];
			$valorCodificado .= "&consecutivo_concurso=".$_REQUEST['consecutivo_concurso'];
			$valorCodificado .= "&consecutivo_perfil=".$_REQUEST['consecutivo_perfil'];
			$valorCodificado .= "&tab=".$id;

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
			echo "</form>";

            echo $this->miFormulario->marcoAgrupacion ( 'fin');
            unset ( $atributos );
    }
}

$miSeleccionador = new consultarProfesional ( $this->lenguaje, $this->miFormulario, $this->sql );

$miSeleccionador->miForm ();
?>
