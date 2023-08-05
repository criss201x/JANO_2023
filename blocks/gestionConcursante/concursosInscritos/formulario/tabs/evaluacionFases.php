<?php
use gestionConcursante\concursosInscritos;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class fasesEvaluacion {
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
            $this->rutaSoporte = $this->miConfigurador->getVariableConfiguracion ( "raizSoportes" );
            $atributosGlobales ['campoSeguro'] = 'true';
            $_REQUEST ['tiempo'] = time ();
            // -------------------------------------------------------------------------------------------------
            //$conexion="estructura";
            $conexion="reportes";
            $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
            
            $hoy=date("Y-m-d");
            //identifca lo roles para la busqueda de subsistemas
            $parametro=array('consecutivo_concurso'=>$_REQUEST['consecutivo_concurso'],
                             'consecutivo_inscrito'=>$_REQUEST['consecutivo_inscrito'],
                             'tipo_dato'=>'evaluacionFases',
                             'fase'=>'evaluacion');    
            $cadena_sql = $this->miSql->getCadenaSql("consultarFasesEvaluacion", $parametro);
            $resultadoFases = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");
            
            
        if($resultadoFases)    
            {
            foreach($resultadoFases as $fase => $value)    
                {
                                
                //identifca lo roles para la busqueda de subsistemas
                $parametro['consecutivo_calendario']=$resultadoFases[$fase]['consecutivo_calendario'];
                //if($hoy<=$resultadoFases[$fase]['fecha_fin_resolver'])
                                $parametro['reclamo']='0'; 
                $cadena_sql = $this->miSql->getCadenaSql("listadoCierreEvaluacion", $parametro);
                $resultadoListaFase= $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");
                //fases de evaluacion
                $cadena_sql = $this->miSql->getCadenaSql("consultaCriterioFase", $parametro);
                $criterioFase= $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");
               // var_dump($criterioFase);
                $maximo_puntos=0;
                foreach ($criterioFase as $crt => $criterio)
                        {
                         $maximo_puntos+=$criterioFase[$crt]['maximo_puntos'];
                        }
                    $esteCampo = "marcoEvaluacion".$fase;
                    $atributos ['id'] = $esteCampo;
                    $atributos ["estilo"] = "jqueryui";
                    $atributos ['tipoEtiqueta'] = 'inicio';
                    $atributos ["leyenda"] =  $resultadoFases[$fase]['nombre'] != 'Resultados finales' ? "Resultados ".$resultadoFases[$fase]['nombre']."" : $resultadoFases[$fase]['nombre'];

                    echo $this->miFormulario->marcoAgrupacion ( 'inicio', $atributos );
                    unset ( $atributos );
                        {   if(isset($resultadoFases[$fase]['fecha_fin_resolver']) && $hoy>$resultadoFases[$fase]['fecha_fin'] )
                                {   
                                    $reclamo=0;
                                    //-----------------Inicio de Conjunto de Controles----------------------------------------
                                    $esteCampo = "marcoEvalua";
                                    $atributos["estilo"] = "jqueryui";
                                    $atributos["leyenda"] = $this->lenguaje->getCadena($esteCampo);
                                    //echo $this->miFormulario->marcoAgrupacion("inicio", $atributos);
                                    unset($atributos);
                                    $mostrarHtml =  "<div class='cell-border'><table id='tablaEvaluaciones' class='table table-striped table-bordered'>";
                                    $mostrarHtml .= "<thead>
                                            <tr align='center' class='textoAzul'>";
                                            foreach ($criterioFase as $crt => $criterio)
                                                {
                                                 if($criterioFase[$crt]['nombre'] != 'reclamaciones finales')   
                                                  $mostrarHtml.="<th>".$criterioFase[$crt]['nombre']."</th>";
                                                }
                                   $mostrarHtml.="  <th>Total</th> ";
                                   //$mostrarHtml.="  <th>Resultado</th>";
                                   $mostrarHtml.="<th>Estado Evaluación</th>
                                                    <th>Reclamación</th>
                                                    <th>Fecha Reclamación</th>
                                                    <th>Respuesta  Reclamación</th>
                                                </tr>
                                            </thead>
                                            <tbody>";
                                        foreach($resultadoListaFase as $key=>$value )
                                            {   $estadoEvaluacion=$resultadoListaFase[$key]['estado_prom'];
                                                if($hoy<=$resultadoFases[$fase]['fecha_fin_resolver'])
                                                    { $estadoEvaluacion='Activo'; }
                                                $reclamo=$resultadoListaFase[$key]['id_reclamacion'];    
                                                //buscar reclamaciones
                                                $puntajes=json_decode($resultadoListaFase[$key]['evaluaciones']);
                                                //calcula los puntos de aprobacion
                                                $puntos_aprueba=($maximo_puntos*$resultadoFases[$fase]['porcentaje_aprueba'])/100;

                                                $parametro['id_reclamacion']=$resultadoListaFase[$key]['id_reclamacion'];
                                                $cadena_sql = $this->miSql->getCadenaSql("consultarReclamacionConcurso", $parametro);
                                                $resultadoReclamo = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");
                                                if($resultadoReclamo){   
                                                  foreach ($resultadoReclamo as $recl => $value) {                                                

                                                    $valorRequisitoBin = $resultadoListaFase[$key]['cumple_requisito'] == "Cumple" ? true : false;
                                                    $valorReclamoBin = $resultadoReclamo[$$recl]['resultado'] == "Aplica" ? true : false;
                                                    if($valorRequisitoBin == $valorReclamoBin){
                                                $mostrarHtml.= "<tr align='center'>";
                                                if($resultadoFases[$fase] ['consecutivo_actividad'] != 7){
                                                    foreach ($puntajes as $pts => $puntos)
                                                        {
                                                         $mostrarHtml.="<td align='center' width='8%'>";
                                                         foreach ($criterioFase as $crt => $criterio)
                                                            {if($criterioFase[$crt]['codigo']==$puntajes[$pts]->id_evaluar)
                                                                {$mostrarHtml.=$puntajes[$pts]->puntaje_final;}
                                                            }
                                                         $mostrarHtml.="</td>";
                                                        }
                                                }       
                                                        //unset($puntajes);
                                                $mostrarHtml.= "    <td align='right' width='6%'>".number_format($resultadoListaFase[$key]['puntaje_promedio'],2)."</td>";
                                               // $mostrarHtml.= "    <td align='left'>".(($resultadoListaFase[$key]['puntaje_promedio']>=$puntos_aprueba)?'Aprobó':'No aprobó');
                                                $mostrarHtml.= "    <td align='center'>".$estadoEvaluacion."</td>";
                                                $mostrarHtml.= "    <td align='left'>".$resultadoListaFase[$key]['id_reclamacion']." - ".$resultadoListaFase[$key]['observacion']."</td>
                                                                    <td align='left'>".substr($resultadoListaFase[$key]['fecha_reclamo'],0,10)."</td>";
                                                //oculta los resultados de la reclamación hasta la fecha de publicación.
                                                //busca reclamaciones


                                                    

                                                    $mostrarHtml.= " <td align='left' width='20%' >";
                                                                $mostrarHtml.=  "<table id='tablaReclamos' class='table table-striped table-bordered'>
                                                                                        <tr align='center'>
                                                                                                <td>Código</td>
                                                                                                <td>Respuesta</td>
                                                                                                <td>Observación</td>
                                                                                            </tr>";

                                                                                
                                                                                               $mostrarHtml.= "<tr>";
                                                                                               $mostrarHtml.= "<td>".$resultadoReclamo[$recl]['id_rsta']."</td> ";
                                                                                               $mostrarHtml.= "<td>".$resultadoReclamo[$recl]['resultado']."</td> ";
                                                                                               $mostrarHtml.= "<td>".$resultadoReclamo[$recl]['observacion']."</td> ";
                                                                                               $mostrarHtml.= "</tr>";
                                                                                                
                                                                                               
                                                                    $mostrarHtml.= "  </table>"; 

                                                        $mostrarHtml.= "  </td>";      
                                                        //unset($resultadoReclamo);
                                                        $mostrarHtml .= "</tr>";
                                                    }
                                                }
                                                    }else{
                                                        $mostrarHtml.= "<tr align='center'>";
                                                        if($resultadoFases[$fase] ['consecutivo_actividad'] != 7){
                                                            foreach ($puntajes as $pts => $puntos)
                                                                {
                                                                 $mostrarHtml.="<td align='center' width='8%'>";
                                                                 foreach ($criterioFase as $crt => $criterio)
                                                                    {if($criterioFase[$crt]['codigo']==$puntajes[$pts]->id_evaluar)
                                                                        {$mostrarHtml.=$puntajes[$pts]->puntaje_final;}
                                                                    }
                                                                 $mostrarHtml.="</td>";
                                                                }
                                                        }                                                                 
                                                        //$mostrarHtml.= "<tr align='center'>";
                                                        $mostrarHtml.= "    <td align='right' width='6%'>".number_format($resultadoListaFase[$key]['puntaje_promedio'],2)."</td>";
                                                        // $mostrarHtml.= "    <td align='left'>".(($resultadoListaFase[$key]['puntaje_promedio']>=$puntos_aprueba)?'Aprobó':'No aprobó');
                                                         $mostrarHtml.= "    <td align='center'>".$estadoEvaluacion."</td>";
                                                         $mostrarHtml.= "    <td align='left'>".$resultadoListaFase[$key]['id_reclamacion']." - ".$resultadoListaFase[$key]['observacion']."</td>
                                                                             <td align='left'>".substr($resultadoListaFase[$key]['fecha_reclamo'],0,10)."</td>";                                                             
                                                    }
                                                $mostrarHtml .= "</tr>";
                                                //unset($resultadoReclamo);                                                 
                                            }
                                            $mostrarHtml .= "</tbody>";
                                            $mostrarHtml .= "</table></div>";
                                            //Fin de Conjunto de Controles
                                        echo $mostrarHtml;
                                        unset($mostrarHtml);          

                                        if($resultadoFases[$fase]['consecutivo_actividad'] == 7 ){
                                            $parametro['id_reclamacion']='';
                                            $cadena_sql = $this->miSql->getCadenaSql("consultarReclamacionConcurso", $parametro);                                            
                                            
                                            $resultadoReclamo = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");
                                            if($resultadoReclamo){
                                                foreach ($resultadoReclamo as $recl => $value){
                                                    $mostrarHtml.= "<tr align='center'>";
                                                        $mostrarHtml.="<td align='center' width='8%'>".''."</td>";
                                                        $mostrarHtml.="<td align='center' width='8%'>".''."</td>";   
                                                        $mostrarHtml.= "    <td align='left'>".$resultadoReclamo[$recl]['id']." - ".$resultadoReclamo[$recl]['reclamo']."</td>
                                                        <td align='left'>".substr($resultadoReclamo[$recl]['fecha_registro'],0,10)."</td>";
                                        
                                                        
                                                        $mostrarHtml.= " <td align='left' width='20%' >";
                                                                        $mostrarHtml.=  "<table id='tablaReclamos' class='table table-striped table-bordered'>
                                                                                                <tr align='center'>
                                                                                                        <td>Código</td>
                                                                                                        <td>Respuesta</td>
                                                                                                        <td>Observación</td>
                                                                                                </tr>";
                                                                                                    $mostrarHtml.= "<tr>";
                                                                                                    $mostrarHtml.= "<td>".$resultadoReclamo[$recl]['id_rsta']."</td> ";
                                                                                                    $mostrarHtml.= "<td>".$resultadoReclamo[$recl]['resultado']."</td> ";
                                                                                                    $mostrarHtml.= "<td>".$resultadoReclamo[$recl]['observacion']."</td> ";
                                                                                                    $mostrarHtml.= "</tr>";     
                                                                        $mostrarHtml.= "  </table>";                                                                                            
                                                                        $mostrarHtml.= "  </td>";      
                                                                        unset($resultadoReclamo);     
                                                                    $mostrarHtml .= "</tr>";
                                                                    unset($mostrarHtml);
                                                }
                                            
                                            }
                                        }    
                                            

                                        $mostrarHtml .= "</tbody>";
                                        $mostrarHtml .= "</table></div>";
                                        //Fin de Conjunto de Controles
                                    echo $mostrarHtml;
                                    unset($mostrarHtml);                                
                                    
                                    if ($hoy <= $resultadoFases[$fase]['fecha_fin_reclamacion'])
                                        {
                                                $id_etapa = $resultadoFases[$fase] ['consecutivo_calendario'];
                                                $etapa = $resultadoFases[$fase] ['nombre'];

                                                $variableNuevo = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
                                                $variableNuevo .= "&bloque=" . $esteBloque ['nombre'];
                                                $variableNuevo .= "&bloqueGrupo=" . $esteBloque ["grupo"];
                                                $variableNuevo .= "&opcion=solicitarReclamacion";
                                                $variableNuevo .= "&consecutivo_inscrito=" . $_REQUEST ['consecutivo_inscrito'];
                                                $variableNuevo .= "&consecutivo_concurso=" . $_REQUEST ['consecutivo_concurso'];
                                                $variableNuevo .= "&consecutivo_perfil=" . $_REQUEST ['consecutivo_perfil'];
                                                $variableNuevo .= "&consecutivo_actividad=" . $resultadoFases[$fase] ['consecutivo_actividad'];
                                                $variableNuevo .= "&id_etapa=" . $id_etapa;
                                                $variableNuevo .= "&etapa=" . $etapa;

                                                $variableNuevo .= "&campoSeguro=" . $_REQUEST ['tiempo'];
                                                $variableNuevo .= "&tiempo=" . time ();
                                                $variableNuevo = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variableNuevo, $directorio );

                                                // enlace para hacer la reclamación
                                        echo "<div ><table width='20%' align='center'>
                                               <tr align='center'>
                                                <td align='center'>";
                                                $esteCampo = 'nuevaReclamacion';
                                                $atributos ['id'] = $esteCampo;
                                                $atributos ['enlace'] = $variableNuevo;
                                                $atributos ['tabIndex'] = 1;
                                                $atributos ['enlaceTexto'] = $this->lenguaje->getCadena ( $esteCampo );
                                                $atributos ['estilo'] = 'textoPequenno textoGris';
                                                $atributos ['enlaceImagen'] = $rutaBloque . "/images/new.png";
                                                $atributos ['posicionImagen'] = "atras"; // "adelante";
                                                $atributos ['ancho'] = '35px';
                                                $atributos ['alto'] = '35px';
                                                $atributos ['redirLugar'] = true;
                                                echo $this->miFormulario->enlace ( $atributos );
                                                unset ( $atributos );
                                        echo " </td>
                                              </tr>
                                            </table></div> ";
                                        }

                                }else
                                {
                                        $atributos["id"]="divnoEncontroEvaluacion";
                                        $atributos["estilo"]="";
                                   //$atributos["estiloEnLinea"]="display:none"; 
                                        echo $this->miFormulario->division("inicio",$atributos);

                                        //-------------Control Boton-----------------------
                                        $esteCampo = "noEncontroEvaluacion";
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
                    echo $this->miFormulario->marcoAgrupacion ( 'fin');
                    unset ( $atributos );
                }
            }    
                


    }
}

$miSeleccionador = new fasesEvaluacion ( $this->lenguaje, $this->miFormulario, $this->miSql );

$miSeleccionador->miForm ();
?>
