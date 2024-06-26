<?php

namespace gestionPublicacion;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/connection/Sql.class.php");

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
	var $miConfigurador;
	function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
	}
	function getCadenaSql($tipo, $variable = "") {
		
		/**
		 * 1. Revisar las variables para evitar SQL Injection
		 */
		$prefijo = $this->miConfigurador->getVariableConfiguracion ( "prefijo" );
		$idSesion = $this->miConfigurador->getVariableConfiguracion ( "id_sesion" );
		
		switch ($tipo) {
			
			/**
			 * Clausulas específicas
			 */
                        case "idioma":
                        	$cadenaSql = "SET lc_time_names = 'es_ES' ";
			break;

            case "insertarLogError" :
                $cadenaSql = "INSERT INTO ";
                $cadenaSql .= "public." . $prefijo . "log_error ";
                $cadenaSql .= "( ";
                $cadenaSql .= "id_usuario, ";
                $cadenaSql .= "fecha_log, ";
                $cadenaSql .= "host, ";
                $cadenaSql .= "query, ";
                $cadenaSql .= "error ";
                $cadenaSql .= ") ";
                $cadenaSql .= "VALUES ";
                $cadenaSql .= "( ";
                $cadenaSql .= "'" . $variable ['id_usuario'] . "', ";
                $cadenaSql .= "'" . $variable ['fecha_log'] . "', ";
                $cadenaSql .= "'" . $variable ['host'] . "', ";
                $cadenaSql .= "'" . $variable ['query'] . "', ";
                $cadenaSql .= "'" . $variable ['error'] . "' ";
                $cadenaSql .= ");";
                break;

			case 'buscarTipoSoporte' :
				$cadenaSql=" SELECT DISTINCT";
                                $cadenaSql.=" tipo_soporte,";
                                $cadenaSql.=" nombre,";
                                $cadenaSql.=" ubicacion,";
                                $cadenaSql.=" descripcion,";
                                $cadenaSql.=" extencion_permitida,";
                                $cadenaSql.=" tamanno_permitido,";
                                $cadenaSql.=" dato_relaciona,";
                                $cadenaSql.=" alias,";
                                $cadenaSql.=" validacion,";
                                $cadenaSql.=" posicion,";
                                $cadenaSql.=" estado ";
                                $cadenaSql.=" FROM ".$this->miConfigurador->getVariableConfiguracion ("esquemaTipoSoporte").".tipo_soporte";
                                $cadenaSql.=" WHERE ";
				$cadenaSql.=" estado='A' ";
                                $cadenaSql.=" AND dato_relaciona = '".$variable['dato_relaciona']."'";
                                if(isset($variable['tipo_soporte']) && $variable['tipo_soporte']!='')
                                    {$cadenaSql.=" AND nombre = '".$variable['tipo_soporte']."'";}
                                $cadenaSql.=" ORDER BY dato_relaciona, posicion ASC, alias ASC";    
                            break;
			case 'buscarSoporte' :
				$cadenaSql=" SELECT DISTINCT";
                                $cadenaSql.=" sop.consecutivo_soporte,";
                                $cadenaSql.=" sop.consecutivo_persona,";
                                $cadenaSql.=" sop.tipo_dato, ";
                                $cadenaSql.=" sop.consecutivo_dato,";
                                $cadenaSql.=" sop.nombre archivo,";
                                $cadenaSql.=" sop.alias,";
                                $cadenaSql.=" tsop.tipo_soporte,";
                                $cadenaSql.=" tsop.nombre, ";
                                $cadenaSql.=" tsop.ubicacion";
                                $cadenaSql.=" FROM ".$this->miConfigurador->getVariableConfiguracion ("esquemaSoporte").".soporte sop";
                                $cadenaSql.=" INNER JOIN ".$this->miConfigurador->getVariableConfiguracion ("esquemaTipoSoporte").".tipo_soporte tsop";
                                $cadenaSql.=" ON tsop.tipo_soporte=sop.tipo_soporte";
                                $cadenaSql.=" AND tsop.estado=sop.estado";
                                $cadenaSql.=" WHERE";
                                $cadenaSql.=" tsop.estado='A' ";
                                $cadenaSql.=" AND sop.tipo_dato='".$variable['tipo_dato']."'";
                                $cadenaSql.=" AND sop.consecutivo_persona='".$variable['consecutivo_persona']."'";
                                $cadenaSql.=" AND tsop.nombre='".$variable['nombre_soporte']."'";
                                if(isset($variable['consecutivo_dato']) && $variable['consecutivo_dato']!='')
                                    {$cadenaSql.=" AND sop.consecutivo_dato='".$variable['consecutivo_dato']."' ";}
                                $cadenaSql.=" ORDER BY sop.consecutivo_soporte DESC ";
                            break;                              
                                        
                        case "consultaCriterioFase":
                                $cadenaSql=" SELECT DISTINCT ";
                                $cadenaSql.=" eval.consecutivo_evaluar codigo, ";
                                $cadenaSql.=" crt.nombre, ";
                                $cadenaSql.=" crt.estado, ";
                                $cadenaSql.=" eval.maximo_puntos ";
                                $cadenaSql.=" FROM concurso.criterio_evaluacion crt ";
                                $cadenaSql.=" INNER JOIN concurso.".$variable['tipoEvaluacion']." eval  ";
                                $cadenaSql.=" ON crt.consecutivo_criterio=eval.consecutivo_criterio  ";
                                $cadenaSql.=" AND eval.estado='A' ";
                                $cadenaSql.=" WHERE "; 
                                $cadenaSql .= " eval.consecutivo_concurso='".$variable['consecutivo_concurso']."' "; 
                                if(isset($variable['consecutivo_calendario']) &&  $variable['consecutivo_calendario']!='' )
                                   {
                                    $cadenaSql.=" AND eval.consecutivo_calendario='".$variable['consecutivo_calendario']."' ";
                                   } 
                                if(isset($variable['consecutivo_criterio']) &&  $variable['consecutivo_criterio']>0 )
                                   {$cadenaSql.=" AND eval.consecutivo_criterio='".$variable['consecutivo_criterio']."' "; 
                                   }
                                $cadenaSql.=" ORDER BY eval.consecutivo_evaluar ASC ";
                            break; 

                            case "validarPerfilEspecial":
                                $cadenaSql=" SELECT ";
                                $cadenaSql.=" epe.consecutivo_evaluar , epe.consecutivo_concurso , epe.consecutivo_criterio , epe.maximo_puntos , epe.estado , epe.puntos_aprueba , epe.consecutivo_calendario , epe.consecutivo_perfil ";
                                $cadenaSql.=" FROM concurso.evaluacion_perfil_especial epe  ";
                                $cadenaSql.=" WHERE ";
                                $cadenaSql.=" epe.consecutivo_perfil=".$variable.";";                             
                    
                            break;
                    
                            case "validarPerfilEspecial2":
                                $cadenaSql=" SELECT ";
                                $cadenaSql.=" epe.consecutivo_evaluar , epe.consecutivo_concurso , epe.consecutivo_criterio , epe.maximo_puntos , epe.estado , epe.puntos_aprueba , epe.consecutivo_calendario , epe.consecutivo_perfil ";
                                $cadenaSql.=" FROM concurso.evaluacion_perfil_especial epe  ";
                                $cadenaSql.=" inner join concurso.concurso_inscrito ci on epe.consecutivo_perfil = ci.consecutivo_perfil ";
                                $cadenaSql.=" WHERE ";
                                $cadenaSql.=" ci.consecutivo_inscrito =".$variable.";";                             
                    
                            break;

                        case "consultarInscritoConcurso":
                                $cadenaSql="SELECT DISTINCT ";
                                $cadenaSql.="insc.consecutivo_inscrito, ";
                                $cadenaSql.="insc.fecha_registro, ";
                                $cadenaSql.="prf.codigo , ";
                                $cadenaSql.="prf.nombre perfil, ";
                                $cadenaSql.="prf.dependencia, ";
                                $cadenaSql.="prf.area, ";
                                $cadenaSql.=" conc.consecutivo_modalidad,";
                                $cadenaSql.=" conc.nombre, ";
                                $cadenaSql.=" conc.acuerdo, ";
                                $cadenaSql.=" conc.descripcion,";
                                $cadenaSql.=" mdl.nombre modalidad, ";
                                $cadenaSql.=" nvl.nombre nivel_concurso ";
                                
                                $cadenaSql.="FROM concurso.concurso_perfil prf ";
                                $cadenaSql.="INNER JOIN concurso.concurso_inscrito insc ON prf.consecutivo_perfil=insc.consecutivo_perfil ";
                                $cadenaSql.=" INNER JOIN concurso.concurso conc ON conc.consecutivo_concurso=prf.consecutivo_concurso";
                                $cadenaSql.=" INNER JOIN concurso.modalidad_concurso mdl ON mdl.consecutivo_modalidad=conc.consecutivo_modalidad";
                                $cadenaSql.=" INNER JOIN general.nivel nvl ON nvl.tipo_nivel='TipoConcurso' AND nvl.codigo_nivel= mdl.codigo_nivel_concurso";
                                $cadenaSql.=" WHERE "; 
                                $cadenaSql .= " insc.consecutivo_inscrito='".$variable['consecutivo_inscrito']."' "; 
                                $cadenaSql.=" ORDER BY prf.dependencia, prf.area,prf.nombre ";
                                
                            break;                     
                    
                        case "consultaSoportesInscripcion":
                                $cadenaSql="SELECT DISTINCT ";
                                $cadenaSql.="ins.consecutivo_soporte_ins, ";
                                $cadenaSql.="ins.consecutivo_inscrito, ";
                                $cadenaSql.="ins.tipo_dato, ";
                                $cadenaSql.="ins.consecutivo_dato, ";
                                $cadenaSql.="ins.fuente_dato, ";
                                $cadenaSql.="ins.valor_dato, ";
                                $cadenaSql.="ins.consecutivo_soporte, ";
                                $cadenaSql.="ins.nombre_soporte, ";
                                $cadenaSql.="ins.alias_soporte, ";
                                $cadenaSql.="ins.fecha_registro, ";
                                $cadenaSql.="ins.estado, ";
                                $cadenaSql.=" tsop.tipo_soporte,";
                                $cadenaSql.=" tsop.nombre nombre_tipo ";
                                $cadenaSql.=" FROM concurso.soporte_inscrito ins ";
                                $cadenaSql.=" LEFT OUTER JOIN concurso.soporte sop ON sop.consecutivo_soporte=ins.consecutivo_soporte ";
                                $cadenaSql.=" LEFT OUTER JOIN general.tipo_soporte tsop ON tsop.tipo_soporte=sop.tipo_soporte ";
                                $cadenaSql.=" WHERE ins.consecutivo_inscrito='".$variable['consecutivo_inscrito']."' "; 
                                $cadenaSql.="AND ins.estado='A' ";
                                if(isset($variable['tipo_dato']) &&  $variable['tipo_dato']!='' )
                                   {$cadenaSql .= " AND ins.tipo_dato='".$variable['tipo_dato']."' ";} 
                            break; 
                            
                        case "consultaFaseCerroValida":
                                $cadenaSql="SELECT DISTINCT  ";
                                $cadenaSql.="prf4.consecutivo_concurso, ";
                                $cadenaSql.="prf4.consecutivo_perfil, ";
                                $cadenaSql.="prf4.codigo, ";
                                $cadenaSql.="prf4.nombre perfil, ";
                                $cadenaSql.="prf4.descripcion,  ";
                                $cadenaSql.="prf4.dependencia, ";
                                $cadenaSql.="prf4.area, ";
                                $cadenaSql.="insc4.consecutivo_inscrito,  ";
                                $cadenaSql.="insc4.consecutivo_persona,  ";
                                $cadenaSql.="bas.identificacion, ";
                                $cadenaSql.="bas.nombre, ";
                                $cadenaSql.="bas.apellido, ";
                                $cadenaSql.="etapa2.consecutivo_etapa, ";
                                $cadenaSql.="etapa2.fecha_registro,  ";
                                $cadenaSql.="etapa2.estado, ";
                                $cadenaSql.="etapa2.consecutivo_calendario_ant faseAprobo ";
                                $cadenaSql.="FROM concurso.concurso_perfil prf4  ";
                                $cadenaSql.="INNER JOIN concurso.concurso_inscrito insc4 ON prf4.consecutivo_perfil=insc4.consecutivo_perfil  ";
                                $cadenaSql.="INNER JOIN concurso.persona bas ON bas.consecutivo=insc4.consecutivo_persona ";
                                $cadenaSql.="INNER JOIN concurso.etapa_inscrito etapa2 ON etapa2.consecutivo_inscrito=insc4.consecutivo_inscrito AND etapa2.estado='A' ";
                                $cadenaSql.="WHERE ";
                                $cadenaSql.="prf4.consecutivo_concurso='".$variable['consecutivo_concurso']."'";
                                $cadenaSql.="AND etapa2.consecutivo_calendario_ant='".$variable['consecutivo_calendario']."'";
                                if(isset($variable['consecutivo_perfil']) &&  $variable['consecutivo_perfil']!='' )
                                   {$cadenaSql .= " AND prf4.consecutivo_perfil='".$variable['consecutivo_perfil']."' ";} 
                                $cadenaSql.=" ORDER BY ";   
                                $cadenaSql.=" prf4.codigo ASC, insc4.consecutivo_inscrito  ";
                                   
                            break;   
                            
                        case "listadoCierreRequisitos":
                                $cadenaSql="SELECT DISTINCT ";
                                $cadenaSql.="prf.consecutivo_concurso,  ";
                                $cadenaSql.="prf.consecutivo_perfil,  ";
                                $cadenaSql.="prf.codigo,  ";
                                $cadenaSql.="prf.nombre perfil,  ";
                                $cadenaSql.="prf.descripcion,  ";
                                $cadenaSql.="prf.dependencia,  ";
                                $cadenaSql.="prf.area,  ";
                                $cadenaSql.="insc.consecutivo_inscrito inscripcion,  ";
                                $cadenaSql.="insc.consecutivo_persona,  ";
                                $cadenaSql.="bas.identificacion,  ";
                                $cadenaSql.="initcap(lower(bas.nombre)) nombre,  ";
                                $cadenaSql.="initcap(lower(bas.apellido)) apellido,  ";
                                $cadenaSql.="req.consecutivo_valida,  ";
                                $cadenaSql.="req.cumple_requisito,  ";
                                $cadenaSql.="req.observacion,  ";
                                $cadenaSql.="req.fecha_registro,  ";
                                $cadenaSql.="req.estado estado_resultado,  ";
                                $cadenaSql.="req.id_reclamacion ";
                                $cadenaSql.="FROM concurso.concurso_perfil prf  ";
                                $cadenaSql.="INNER JOIN concurso.concurso_inscrito insc ON prf.consecutivo_perfil=insc.consecutivo_perfil  AND prf.estado='A' ";
                                $cadenaSql.="INNER JOIN concurso.persona bas ON bas.consecutivo=insc.consecutivo_persona ";
                                
                                if(isset($variable['tipo_cierre']) &&  $variable['tipo_cierre']=='final' )   
                                    {$cadenaSql.="INNER JOIN  concurso.valida_requisito req ON req.consecutivo_inscrito=insc.consecutivo_inscrito AND req.estado='A' ";
                                     $cadenaSql.="INNER JOIN concurso.etapa_inscrito etapa ON etapa.consecutivo_inscrito=insc.consecutivo_inscrito AND etapa.consecutivo_calendario_ant='".$variable['consecutivo_calendario']."'  AND etapa.estado='A' ";
                                    }
                                elseif(isset($variable['tipo_cierre']) &&  $variable['tipo_cierre']=='reclamo' )   
                                    {$cadenaSql.="INNER JOIN concurso.valida_requisito req ON req.consecutivo_inscrito=insc.consecutivo_inscrito AND req.estado='A' ";
                                     $cadenaSql.="INNER JOIN concurso.evaluacion_reclamacion reclamo ON reclamo.id_inscrito=insc.consecutivo_inscrito AND reclamo.consecutivo_calendario='".$variable['consecutivo_calendario']."'  AND reclamo.estado='A' ";
                                    } 
                                else{$cadenaSql.="INNER JOIN  concurso.valida_requisito req ON req.consecutivo_inscrito=insc.consecutivo_inscrito AND req.estado='A' AND req.version_valida='1' ";
                                    }    
                                
                                $cadenaSql.="WHERE prf.consecutivo_concurso='".$variable['consecutivo_concurso']."'";
                                
                                if(isset($variable['consecutivo_perfil']) &&  $variable['consecutivo_perfil']!='' )
                                   {$cadenaSql .= " AND prf.consecutivo_perfil='".$variable['consecutivo_perfil']."' ";} 
                               
                                if(isset($variable['tipo_cierre']) &&  $variable['tipo_cierre']=='parcial' )   
                                {
                                    $cadenaSql.=" UNION ";
                                    $cadenaSql.="SELECT DISTINCT  ";
                                    $cadenaSql.="prf.consecutivo_concurso,  ";
                                    $cadenaSql.="prf.consecutivo_perfil,  ";
                                    $cadenaSql.="prf.codigo,  ";
                                    $cadenaSql.="prf.nombre perfil,  ";
                                    $cadenaSql.="prf.descripcion,  ";
                                    $cadenaSql.="prf.dependencia,  ";
                                    $cadenaSql.="prf.area,  ";
                                    $cadenaSql.="insc.consecutivo_inscrito inscripcion,  ";
                                    $cadenaSql.="insc.consecutivo_persona,  ";
                                    $cadenaSql.="bas.identificacion,  ";
                                    $cadenaSql.="initcap(lower(bas.nombre)) nombre,  ";
                                    $cadenaSql.="initcap(lower(bas.apellido)) apellido,  ";
                                    $cadenaSql.="req.consecutivo_valida,  ";
                                    $cadenaSql.="req.cumple_requisito ,  ";
                                    $cadenaSql.="req.observacion,  ";
                                    $cadenaSql.="req.fecha_registro,  ";
                                    $cadenaSql.="req.estado estado_resultado,  ";
                                    $cadenaSql.="req.id_reclamacion ";
                                    $cadenaSql.="FROM concurso.concurso_perfil prf  ";
                                    $cadenaSql.="INNER JOIN concurso.concurso_inscrito insc ON prf.consecutivo_perfil=insc.consecutivo_perfil  AND prf.estado='A' ";
                                    $cadenaSql.="INNER JOIN concurso.persona bas ON bas.consecutivo=insc.consecutivo_persona  ";
                                    $cadenaSql.="INNER JOIN  concurso.valida_requisito req ON req.consecutivo_inscrito=insc.consecutivo_inscrito AND req.estado='I' AND req.version_valida='1' ";
                                    $cadenaSql.="WHERE prf.consecutivo_concurso='".$variable['consecutivo_concurso']."'";
                                    if(isset($variable['consecutivo_perfil']) &&  $variable['consecutivo_perfil']!='' )
                                       {$cadenaSql .= " AND prf.consecutivo_perfil='".$variable['consecutivo_perfil']."' ";} 

                                }
                                    $cadenaSql.=" ORDER BY codigo, perfil , cumple_requisito DESC, inscripcion ";                                
                            break;                            

                        case "listadoCierreEvaluacion":
                                //consulta promedios activos para cierre parcial y final
                                $cadenaSql="SELECT DISTINCT ";
                                $cadenaSql.="prf.consecutivo_concurso,  ";
                                $cadenaSql.="prf.consecutivo_perfil,  ";
                                $cadenaSql.="prf.nombre perfil,  ";
                                $cadenaSql.="prf.codigo,  ";
                                $cadenaSql.="prf.descripcion,  ";
                                $cadenaSql.="prf.dependencia,  ";
                                $cadenaSql.="prf.area,  ";
                                $cadenaSql.="prf.vacantes,  ";
                                $cadenaSql.="insc.consecutivo_inscrito inscripcion,  ";
                                $cadenaSql.="insc.consecutivo_persona,  ";
                                $cadenaSql.="bas.identificacion,  ";
                                $cadenaSql.="initcap(lower(bas.nombre)) nombre,  ";
                                $cadenaSql.="initcap(lower(bas.apellido)) apellido,  ";
                                $cadenaSql.="prmd.id_calendario fase, ";
                                $cadenaSql.="prmd.puntaje_promedio, ";
                                $cadenaSql.="prmd.evaluaciones, ";
                                $cadenaSql.="prmd.fecha_registro,  ";
                                $cadenaSql.="prmd.id_reclamacion,  ";
                                $cadenaSql.=" (CASE WHEN prmd.estado='A' THEN 'Activo' WHEN prmd.estado='I' THEN 'Inactivo' ELSE '' END) estado_prom ";
                                $cadenaSql.="FROM concurso.concurso_perfil prf  ";
                                $cadenaSql.="INNER JOIN concurso.concurso_inscrito insc ON prf.consecutivo_perfil=insc.consecutivo_perfil  AND prf.estado='A' ";
                                $cadenaSql.="INNER JOIN concurso.persona bas ON bas.consecutivo=insc.consecutivo_persona ";
                                if(isset($variable['tipo_cierre']) &&  $variable['tipo_cierre']=='final' )   
                                    {$cadenaSql.="INNER JOIN concurso.evaluacion_promedio prmd ON prmd.id_inscrito=insc.consecutivo_inscrito AND prmd.estado='A' ";
                                     $cadenaSql.="INNER JOIN concurso.etapa_inscrito etapa ON etapa.consecutivo_inscrito=insc.consecutivo_inscrito AND etapa.consecutivo_calendario_ant=prmd.id_calendario AND etapa.estado='A' ";
                                    }
                                elseif(isset($variable['tipo_cierre']) &&  $variable['tipo_cierre']=='reclamo' )   
                                    {$cadenaSql.="INNER JOIN concurso.evaluacion_promedio prmd ON prmd.id_inscrito=insc.consecutivo_inscrito AND prmd.estado='A' ";
                                     $cadenaSql.="INNER JOIN concurso.evaluacion_reclamacion reclamo ON reclamo.id_inscrito=insc.consecutivo_inscrito AND reclamo.consecutivo_calendario=prmd.id_calendario AND reclamo.estado='A' ";
                                    }    
                                elseif(isset($variable['tipo_cierre']) &&  $variable['tipo_cierre']=='parcial' )   
                                    {$cadenaSql.="INNER JOIN concurso.evaluacion_promedio prmd ON prmd.id_inscrito=insc.consecutivo_inscrito AND prmd.estado='A' AND prmd.id_reclamacion=0  ";
                                    }
                                else{$cadenaSql.="INNER JOIN concurso.evaluacion_promedio prmd ON prmd.id_inscrito=insc.consecutivo_inscrito   ";
                                    }    
                                $cadenaSql.="WHERE prf.consecutivo_concurso='".$variable['consecutivo_concurso']."'";
                                $cadenaSql .= " AND prmd.id_calendario='".$variable['consecutivo_calendario']."' ";
                                if(isset($variable['consecutivo_perfil']) &&  $variable['consecutivo_perfil']!='' )
                                   {$cadenaSql .= " AND prf.consecutivo_perfil='".$variable['consecutivo_perfil']."' ";} 
                                if(isset($variable['consecutivo_inscrito']) &&  $variable['consecutivo_inscrito']!='' )
                                   {$cadenaSql .= " AND insc.consecutivo_inscrito='".$variable['consecutivo_inscrito']."' ";}    
                                //consulta promedios inactivos para cierre parcial
                                if(isset($variable['tipo_cierre']) &&  $variable['tipo_cierre']=='parcial' )   
                                {
                                    $cadenaSql.="UNION ";
                                    $cadenaSql.="SELECT DISTINCT  ";
                                    $cadenaSql.="prf.consecutivo_concurso,  ";
                                    $cadenaSql.="prf.consecutivo_perfil,  ";
                                    $cadenaSql.="prf.nombre perfil,  ";
                                    $cadenaSql.="prf.codigo,  ";
                                    $cadenaSql.="prf.descripcion,  ";
                                    $cadenaSql.="prf.dependencia,  ";
                                    $cadenaSql.="prf.area,  ";
                                    $cadenaSql.="prf.vacantes,  ";
                                    $cadenaSql.="insc.consecutivo_inscrito inscripcion,  ";
                                    $cadenaSql.="insc.consecutivo_persona,  ";
                                    $cadenaSql.="bas.identificacion,  ";
                                    $cadenaSql.="initcap(lower(bas.nombre)) nombre,  ";
                                    $cadenaSql.="initcap(lower(bas.apellido)) apellido,  ";
                                    $cadenaSql.="prmd.id_calendario fase, ";
                                    $cadenaSql.="prmd.puntaje_promedio, ";
                                    $cadenaSql.="prmd.evaluaciones, ";
                                    $cadenaSql.="prmd.fecha_registro,  ";
                                    $cadenaSql.="prmd.id_reclamacion,  ";
                                    $cadenaSql.=" (CASE WHEN prmd.estado='A' THEN 'Activo' WHEN prmd.estado='I' THEN 'Inactivo' ELSE '' END) estado_prom ";
                                    $cadenaSql.="FROM concurso.concurso_perfil prf  ";
                                    $cadenaSql.="INNER JOIN concurso.concurso_inscrito insc ON prf.consecutivo_perfil=insc.consecutivo_perfil  AND prf.estado='A' ";
                                    $cadenaSql.="INNER JOIN concurso.persona bas ON bas.consecutivo=insc.consecutivo_persona  ";
                                    $cadenaSql.="INNER JOIN concurso.evaluacion_promedio prmd ON prmd.id_inscrito=insc.consecutivo_inscrito AND prmd.estado='I' AND prmd.id_reclamacion=0 ";
                                    $cadenaSql.="WHERE prf.consecutivo_concurso='".$variable['consecutivo_concurso']."'";
                                    $cadenaSql .= " AND prmd.id_calendario='".$variable['consecutivo_calendario']."' ";
                                    if(isset($variable['consecutivo_perfil']) &&  $variable['consecutivo_perfil']!='' )
                                       {$cadenaSql .= " AND prf.consecutivo_perfil='".$variable['consecutivo_perfil']."' ";} 

                                }
                                    $cadenaSql.="ORDER BY codigo, puntaje_promedio DESC, inscripcion, estado_prom ";                                
                            break;     
                                                        
                            
                       case "listadoCierreEvaluacionResultados":
                                //consulta promedios activos para cierre parcial y final
                                $cadenaSql="SELECT DISTINCT ";
                                $cadenaSql.="prf.consecutivo_concurso,  ";
                                $cadenaSql.="prf.consecutivo_perfil,  ";
                                $cadenaSql.="prf.codigo,  ";
                                $cadenaSql.="prf.nombre perfil,  ";
                                $cadenaSql.="prf.descripcion,  ";
                                $cadenaSql.="prf.dependencia,  ";
                                $cadenaSql.="prf.area,  ";
                                $cadenaSql.="insc.consecutivo_inscrito inscripcion,  ";
                                $cadenaSql.="insc.consecutivo_persona,  ";
                                $cadenaSql.="bas.identificacion,  ";
                                $cadenaSql.="initcap(lower(bas.nombre)) nombre,  ";
                                $cadenaSql.="initcap(lower(bas.apellido)) apellido,  ";
                                $cadenaSql.="eval.consecutivo_calendario fase, ";
                                $cadenaSql.="eval.consecutivo_evaluar , ";
                                $cadenaSql.="prmd.puntaje_promedio, ";
                                $cadenaSql.="prmd.evaluaciones, ";
                                $cadenaSql.="prmd.fecha_registro,  ";
                                $cadenaSql.="prmd.id_reclamacion,  ";
                                $cadenaSql.=" (CASE WHEN prmd.estado='A' THEN 'Activo' WHEN prmd.estado='I' THEN 'Inactivo' ELSE '' END) estado_prom ";
                                $cadenaSql.="FROM concurso.concurso_perfil prf  ";
                                $cadenaSql.="INNER JOIN concurso.concurso_inscrito insc ON prf.consecutivo_perfil=insc.consecutivo_perfil  AND prf.estado='A' ";
                                $cadenaSql.="INNER JOIN concurso.persona bas ON bas.consecutivo=insc.consecutivo_persona ";
                                $cadenaSql.="INNER JOIN concurso.".$variable['tipoEvaluacion']." eval ON eval.consecutivo_concurso=prf.consecutivo_concurso ";
                                
                                $cadenaSql.="LEFT OUTER JOIN concurso.evaluacion_promedio prmd ON prmd.id_inscrito = insc.consecutivo_inscrito AND prmd.id_calendario=eval.consecutivo_calendario   ";
                                if(isset($variable['estado_evaluar']) &&  $variable['estado_evaluar']!='' )
                                       {$cadenaSql .= " AND prmd.estado='".$variable['estado_evaluar']."' ";}

                                $cadenaSql.="WHERE prf.consecutivo_concurso='".$variable['consecutivo_concurso']."'";
                                $cadenaSql .= " AND eval.consecutivo_calendario='".$variable['consecutivo_calendario']."' ";
                                if(isset($variable['consecutivo_perfil']) &&  $variable['consecutivo_perfil']!='' )
                                   {$cadenaSql .= " AND prf.consecutivo_perfil='".$variable['consecutivo_perfil']."' ";} 
                                if(isset($variable['consecutivo_inscrito']) &&  $variable['consecutivo_inscrito']!='' )
                                   {$cadenaSql .= " AND insc.consecutivo_inscrito='".$variable['consecutivo_inscrito']."' ";}    
                                
                            break;     
                            
                        case "consultarValidadoPerfilConcurso":
                                $cadenaSql=" SELECT DISTINCT";
                                $cadenaSql.=" val.consecutivo_valida,";
                                $cadenaSql.=" val.consecutivo_inscrito,";
                                $cadenaSql.=" (CASE WHEN val.cumple_requisito='SI' THEN 'Cumple' ELSE 'No cumple' END) cumple_requisito,";
                                $cadenaSql.=" val.observacion,";
                                $cadenaSql.=" val.fecha_registro,";
                                $cadenaSql.=" (CASE WHEN val.estado='A' THEN 'Activo' ELSE 'Inactivo' END) estado,";
                                $cadenaSql.=" val.id_reclamacion";
                                if(isset($variable['tipo_dato']) &&  $variable['tipo_dato']='evaluacionPerfilDetalle')
                                       {$cadenaSql .= ", logger.id_usuario";
                                        $cadenaSql .= ", us.nombre";
                                        $cadenaSql .= ", us.apellido";
                                        $cadenaSql .= ", recl.consecutivo_calendario";
                                       } 
                                $cadenaSql.=" FROM concurso.concurso_perfil prf2";
                                $cadenaSql.=" INNER JOIN concurso.concurso_inscrito insc2 ON prf2.consecutivo_perfil=insc2.consecutivo_perfil";
                                $cadenaSql.=" INNER JOIN concurso.valida_requisito val ON val.consecutivo_inscrito=insc2.consecutivo_inscrito";
                                $cadenaSql.=" left join concurso.evaluacion_reclamacion recl on val.id_reclamacion = recl.id";
                                if(isset($variable['tipo_dato']) &&  $variable['tipo_dato']='evaluacionPerfilDetalle')
                                       {$cadenaSql .= " INNER JOIN public.jano_log_usuario logger ON logger.tipo_registro='registroValidacion' AND logger.id_registro::integer=val.consecutivo_valida::integer ";
                                        $cadenaSql .= " INNER JOIN public.jano_usuario us ON us.id_usuario= logger.id_usuario ";
                                       } 
                                $cadenaSql.=" WHERE prf2.consecutivo_concurso='".$variable['consecutivo_concurso']."' ";
                                $cadenaSql .= "AND insc2.consecutivo_inscrito='".$variable['consecutivo_inscrito']."' "; 
                                //$cadenaSql.=" AND val.estado='A'   ";
                                $cadenaSql.=" ORDER BY estado ASC ";
                            break; 
                        
                        case "consultarReclamacionConcurso":
                                $cadenaSql=" SELECT DISTINCT ";
                                $cadenaSql.=" recl.id, ";
                                $cadenaSql.=" recl.consecutivo_calendario, ";
                                $cadenaSql.=" recl.observacion reclamo , ";
                                $cadenaSql.=" recl.fecha_registro, ";
                                $cadenaSql.=" recl.id_inscrito, ";
                                $cadenaSql.=" rsta.id id_rsta, ";
                                $cadenaSql.=" (CASE WHEN rsta.respuesta='SI' THEN 'Aplica' ELSE 'No aplica' END) resultado,";
                                $cadenaSql.=" rsta.observacion, ";
                                $cadenaSql.=" rsta.fecha_registro fecha_respuesta, ";
                                $cadenaSql.=" rsta.id_evaluar_respuesta, ";
                                $cadenaSql.=" rsta.id_evaluador ";
                                $cadenaSql.=" FROM concurso.evaluacion_reclamacion recl ";
                                $cadenaSql.=" INNER JOIN concurso.respuesta_reclamacion rsta ON recl.id=rsta.id_reclamacion AND recl.estado=rsta.estado ";
                                $cadenaSql.=" WHERE rsta.estado='A' ";
                                $cadenaSql .= "AND recl.id_inscrito='".$variable['consecutivo_inscrito']."' "; 
                                if(isset($variable['id_reclamacion']) &&  $variable['id_reclamacion']!=''  &&  $variable['id_reclamacion']>0 )
                                   {
                                    $cadenaSql.=" AND recl.id='".$variable['id_reclamacion']."' ";
                                   }
                                if(isset($variable['id_evalua']) &&  $variable['id_evalua']!='' )
                                   {
                                    $cadenaSql .= "AND rsta.id_evaluar_respuesta='".$variable['id_evalua']."' "; 
                                   }   
                                if(isset($variable['fase']) &&  $variable['fase']!='' )
                                   {
                                    $cadenaSql .= "AND recl.consecutivo_calendario='".$variable['consecutivo_calendario']."' "; 
                                   }                                   
                                
                                //$cadenaSql.=" AND rsta.estado='A'   ";
                                //$cadenaSql.=" ORDER BY estado ASC ";
                            break;                                 

                        case "consultarFasesEvaluacion":
                                $cadenaSql="SELECT ";
                                $cadenaSql.="cal.consecutivo_concurso, ";
                                $cadenaSql.="cal.consecutivo_calendario, ";
                                $cadenaSql.="cal.porcentaje_aprueba, ";
                                $cadenaSql.="act.nombre ";
                                $cadenaSql.="FROM ";
                                $cadenaSql.="concurso.concurso_calendario cal  ";
                                $cadenaSql.="INNER JOIN concurso.actividad_calendario act ON act.consecutivo_actividad=cal.consecutivo_actividad ";
                                $cadenaSql.="WHERE ";
                                $cadenaSql.="cal.estado='A' ";
                                $cadenaSql.="AND cal.consecutivo_concurso='".$variable['consecutivo_concurso']."' ";
                                $cadenaSql.="AND act.nombre<>'Inscripción' ";
                                $cadenaSql.="AND act.nombre<>'Registro soportes' ";
                                $cadenaSql.="AND act.nombre<>'Evaluar requisitos' ";
                                $cadenaSql.="AND act.nombre<>'Resultados finales' ";
                                $cadenaSql.="ORDER BY cal.fecha_inicio ";

                            break;           
                        
                         case "consultarEvaluacion":
                             
	 			$cadenaSql=" SELECT DISTINCT ";
                        	$cadenaSql.="ep.id, ";
	 			$cadenaSql.="ep.id_grupo, ";
	 			$cadenaSql.="ep.id_inscrito, ";
	 			$cadenaSql.="ep.id_evaluar, ";
	 			$cadenaSql.="ep.puntaje_parcial, ";
	 			$cadenaSql.="ep.observacion, ";
	 			$cadenaSql.="ep.fecha_registro, ";
	 			$cadenaSql.="ep.estado, ";
	 			$cadenaSql.="ep.id_reclamacion, ";
	 			$cadenaSql.="ce.consecutivo_criterio, ";
	 			$cadenaSql.="ceval.consecutivo_criterio AS id_criterio, ";
	 			$cadenaSql.="ceval.nombre AS criterio, ";
	 			$cadenaSql.="eg.id_evaluador, concat(us.nombre, ' ', us.apellido) AS evaluador, ";
	 			$cadenaSql.="'' nuevo_puntaje, ";
	 			$cadenaSql.="'' fecha_nuevo, ";
	 			$cadenaSql.="'' nueva_obs, ";
	 			$cadenaSql.="ef.puntaje_final, ";
	 			$cadenaSql.="ef.aprobo ";
	 			$cadenaSql.="FROM  concurso.".$variable['tipoEvaluacion']." ce "; 
	 			$cadenaSql.="INNER JOIN  concurso.criterio_evaluacion ceval ON ce.consecutivo_criterio=ceval.consecutivo_criterio ";
                                $cadenaSql.="INNER JOIN concurso.evaluacion_parcial ep ON ep.id_evaluar = ce.consecutivo_evaluar AND ep.estado='A' AND ep.id_reclamacion IS NULL ";
	 			$cadenaSql.="INNER JOIN concurso.evaluacion_grupo eg ON ep.id_grupo=eg.id ";
	 			$cadenaSql.="INNER JOIN jano_usuario us ON UPPER(concat(us.tipo_identificacion, '', us.identificacion))=UPPER(eg.id_evaluador) ";
	 			$cadenaSql.="LEFT OUTER JOIN concurso.evaluacion_final ef ON ef.estado='A' AND ef.id=ep.id_evaluacion_final ";
	 			$cadenaSql.="WHERE ce.consecutivo_concurso='".$variable['consecutivo_concurso']."'";
                                $cadenaSql.="AND ep.id_inscrito='".$variable['consecutivo_inscrito']."'";
                                $cadenaSql.="AND ce.consecutivo_criterio  "; 
	 			$cadenaSql.="IN(select distinct criterio.consecutivo_criterio  ";
                                   $cadenaSql.="from concurso.".$variable['tipoEvaluacion']." ce, concurso.criterio_evaluacion criterio, concurso.evaluacion_parcial ep  ";
                                    $cadenaSql.="WHERE ce.consecutivo_criterio=criterio.consecutivo_criterio ";
                                    $cadenaSql.="and ep.id_evaluar=ce.consecutivo_evaluar and id_inscrito='".$variable['consecutivo_inscrito']."' ";
                                    $cadenaSql.="and  ce.consecutivo_evaluar IN (".$variable['criterios'].") ";
                                    $cadenaSql.=" ) ";
                                
                                $cadenaSql.=" UNION ";    
                                
	 			$cadenaSql.=" SELECT DISTINCT ";
	 			$cadenaSql.="ep.id, ";
	 			$cadenaSql.="ep.id_grupo, ";
	 			$cadenaSql.="ep.id_inscrito, ";
	 			$cadenaSql.="ep.id_evaluar, ";
	 			$cadenaSql.="ep.puntaje_parcial, ";
	 			$cadenaSql.="ep.observacion, ";
	 			$cadenaSql.="ep.fecha_registro, ";
	 			$cadenaSql.="ep.estado, ";
	 			$cadenaSql.="ep.id_reclamacion, ";
	 			$cadenaSql.="ce.consecutivo_criterio, ";
	 			$cadenaSql.="ceval.consecutivo_criterio AS id_criterio, ";
	 			$cadenaSql.="ceval.nombre AS criterio, ";
	 			$cadenaSql.="eg.id_evaluador, concat(us.nombre, ' ', us.apellido) AS evaluador, ";
	 			$cadenaSql.="epnew.puntaje_parcial nuevo_puntaje, ";
	 			$cadenaSql.="epnew.fecha_registro fecha_nuevo, ";
	 			$cadenaSql.="epnew.observacion nueva_obs, ";
	 			$cadenaSql.="ef.puntaje_final, ";
	 			$cadenaSql.="ef.aprobo ";
	 			$cadenaSql.="FROM  concurso.".$variable['tipoEvaluacion']." ce ";
	 			$cadenaSql.="INNER JOIN  concurso.criterio_evaluacion ceval ON ce.consecutivo_criterio=ceval.consecutivo_criterio ";
	 			$cadenaSql.="INNER JOIN concurso.evaluacion_parcial ep ON ep.id_evaluar = ce.consecutivo_evaluar AND ep.id_reclamacion IS NOT NULL   ";
                                            $cadenaSql.=" AND ep.estado= ";
                                            $cadenaSql.="( ";
                                            $cadenaSql.="SELECT (CASE WHEN COUNT(DISTINCT id)>1 THEN 'I' ELSE 'A' END) estado ";
                                            $cadenaSql.="FROM concurso.evaluacion_parcial evant ";
                                            $cadenaSql.="WHERE evant.id_grupo=ep.id_grupo ";
                                            $cadenaSql.="AND evant.id_inscrito=ep.id_inscrito ";
                                            $cadenaSql.="AND evant.id_evaluar=ep.id_evaluar ";
                                            $cadenaSql.="AND evant.id_reclamacion=ep.id_reclamacion ";
                                            $cadenaSql.="GROUP BY evant.id_grupo, evant.id_inscrito, evant.id_evaluar,evant.id_reclamacion ";
                                            $cadenaSql.=")";
	 			$cadenaSql.="INNER JOIN concurso.evaluacion_parcial epnew ON epnew.id_evaluar = ep.id_evaluar  AND epnew.id_grupo = ep.id_grupo  AND epnew.id_inscrito = ep.id_inscrito  AND epnew.estado='A' AND epnew.id_reclamacion=ep.id_reclamacion  ";
	 			$cadenaSql.="INNER JOIN concurso.evaluacion_grupo eg ON ep.id_grupo=eg.id ";
	 			$cadenaSql.="INNER JOIN jano_usuario us ON UPPER(concat(us.tipo_identificacion, '', us.identificacion))=UPPER(eg.id_evaluador) ";
	 			$cadenaSql.="LEFT OUTER JOIN  concurso.evaluacion_final ef ON ef.estado = 'A' AND ef.id_inscrito=ep.id_inscrito AND ef.id_evaluar=ep.id_evaluar ";
	 			$cadenaSql.="WHERE ce.consecutivo_concurso='".$variable['consecutivo_concurso']."'";
                                $cadenaSql.="AND ep.id_inscrito='".$variable['consecutivo_inscrito']."'";
                                $cadenaSql.="AND ce.consecutivo_criterio  ";
	 			$cadenaSql.="IN(select distinct criterio.consecutivo_criterio  ";
                                    $cadenaSql.="from concurso.".$variable['tipoEvaluacion']." ce, concurso.criterio_evaluacion criterio, concurso.evaluacion_parcial ep  ";
                                    $cadenaSql.="WHERE ce.consecutivo_criterio=criterio.consecutivo_criterio ";
                                    $cadenaSql.="and ep.id_evaluar=ce.consecutivo_evaluar and id_inscrito='".$variable['consecutivo_inscrito']."' ";
                                    $cadenaSql.=" and  ce.consecutivo_evaluar IN (".$variable['criterios'].") ";
                                    $cadenaSql.=" ) ";
	 			  
                                $cadenaSql.="ORDER BY ";
	 			$cadenaSql.="id_grupo, ";
	 			$cadenaSql.="id_inscrito, ";
	 			$cadenaSql.="id_evaluar ";    
	 			    
	 			//echo $cadenaSql;
                        break;
                        /***Consultas arma hoja de vida general***/

			case 'buscarTipoSoporte' :
				$cadenaSql=" SELECT DISTINCT";
                                $cadenaSql.=" tipo_soporte,";
                                $cadenaSql.=" nombre,";
                                $cadenaSql.=" ubicacion,";
                                $cadenaSql.=" descripcion,";
                                $cadenaSql.=" extencion_permitida,";
                                $cadenaSql.=" tamanno_permitido,";
                                $cadenaSql.=" dato_relaciona,";
                                $cadenaSql.=" alias,";
                                $cadenaSql.=" validacion,";
                                $cadenaSql.=" posicion,";
                                $cadenaSql.=" estado ";
                                $cadenaSql.=" FROM ".$this->miConfigurador->getVariableConfiguracion ("esquemaTipoSoporte").".tipo_soporte";
                                $cadenaSql.=" WHERE ";
				$cadenaSql.=" estado='A' ";
                                $cadenaSql.=" AND dato_relaciona = '".$variable['dato_relaciona']."'";
                                if(isset($variable['tipo_soporte']) && $variable['tipo_soporte']!='')
                                    {$cadenaSql.=" AND nombre = '".$variable['tipo_soporte']."'";}
                                $cadenaSql.=" ORDER BY dato_relaciona, posicion ASC, alias ASC";    
                            break;
                        
                        case 'buscarSoporte' :
				$cadenaSql=" SELECT DISTINCT";
                                $cadenaSql.=" sop.consecutivo_soporte,";
                                $cadenaSql.=" sop.consecutivo_persona,";
                                $cadenaSql.=" sop.tipo_dato, ";
                                $cadenaSql.=" sop.consecutivo_dato,";
                                $cadenaSql.=" sop.nombre archivo,";
                                $cadenaSql.=" sop.alias,";
                                $cadenaSql.=" tsop.tipo_soporte,";
                                $cadenaSql.=" tsop.nombre, ";
                                $cadenaSql.=" tsop.ubicacion";
                                $cadenaSql.=" FROM concurso.soporte sop";
                                $cadenaSql.=" INNER JOIN general.tipo_soporte tsop";
                                $cadenaSql.=" ON tsop.tipo_soporte=sop.tipo_soporte";
                                $cadenaSql.=" AND tsop.estado=sop.estado";
                                $cadenaSql.=" WHERE";
                                $cadenaSql.=" tsop.estado='A' ";
                                $cadenaSql.=" AND sop.tipo_dato='".$variable['tipo_dato']."'";
                                $cadenaSql.=" AND sop.consecutivo_persona='".$variable['consecutivo']."'";
                                $cadenaSql.=" AND tsop.nombre='".$variable['nombre_soporte']."'";
                                if(isset($variable['consecutivo_dato']) && $variable['consecutivo_dato']!='')
                                    {$cadenaSql.=" AND sop.consecutivo_dato='".$variable['consecutivo_dato']."' ";}
                                $cadenaSql.=" ORDER BY sop.consecutivo_soporte DESC ";
                            break;                            
                        case "consultarBasicos":
                                $cadenaSql=" SELECT DISTINCT";
                                $cadenaSql.=" bas.consecutivo,";
                                $cadenaSql.=" bas.tipo_identificacion, ";
                                $cadenaSql.=" bas.identificacion, ";
                                $cadenaSql.=" bas.nombre, ";
                                $cadenaSql.=" bas.apellido,";
                                $cadenaSql.=" cud.nombre lugar_nacimiento, ";
                                $cadenaSql.=" bas.fecha_nacimiento, ";
                                $cadenaSql.=" ps.nombre_pais pais_nacimiento, ";
                                $cadenaSql.=" bas.departamento_nacimiento, ";
                                $cadenaSql.=" bas.sexo, ";
                                $cadenaSql.=" us.id_usuario, ";
                                $cadenaSql.=" cudIdent.nombre lugar_identificacion, ";
                                $cadenaSql.=" bas.fecha_identificacion, ";
                                $cadenaSql.=" idm.nombre idioma_nativo ";
                                $cadenaSql.=" FROM concurso.persona bas ";
                                $cadenaSql.=" INNER JOIN ".$prefijo."usuario us ";
                                $cadenaSql.=" ON trim(us.tipo_identificacion)=trim(bas.tipo_identificacion) ";
                                $cadenaSql.=" AND bas.identificacion=us.identificacion ";
                                $cadenaSql.=" LEFT OUTER JOIN general.pais ps ";
                                $cadenaSql.=" ON ps.id_pais=bas.pais_nacimiento ";
                                $cadenaSql.=" LEFT OUTER JOIN general.ciudad cud ";
                                $cadenaSql.=" ON cud.id_ciudad=bas.lugar_nacimiento ";
                                $cadenaSql.=" LEFT OUTER JOIN general.ciudad cudIdent ";
                                $cadenaSql.=" ON cudIdent.id_ciudad=bas.lugar_identificacion ";
                                $cadenaSql.=" LEFT OUTER JOIN general.idioma idm ON idm.codigo_idioma=bas.codigo_idioma_nativo";
                                $cadenaSql.=" WHERE bas.consecutivo IS NOT NULL ";
                                if(isset($variable['identificacion']) && $variable['identificacion']!='')
                                    {$cadenaSql.=" AND bas.identificacion='".$variable['identificacion']."' ";}
                                if(isset($variable['id_usuario']) && $variable['id_usuario']!='')
                                    {$cadenaSql.=" AND us.id_usuario='".$variable['id_usuario']."' ";}
                            break;
                            
                        case "consultarContacto":
                                $cadenaSql=" SELECT DISTINCT ";
                                $cadenaSql.=" bas.identificacion, ";
                                $cadenaSql.=" bas.nombre, ";
                                $cadenaSql.=" bas.apellido,";
                                $cadenaSql.=" (CASE WHEN cont.consecutivo_contacto IS NULL THEN 0 ELSE cont.consecutivo_contacto END ) consecutivo_contacto, ";
                                $cadenaSql.=" bas.consecutivo consecutivo_persona, ";
                                $cadenaSql.=" ps.nombre_pais pais, ";
                                $cadenaSql.=" cont.departamento_residencia, ";
                                $cadenaSql.=" cud.nombre ciudad, ";
                                $cadenaSql.=" cont.direccion_residencia, ";
                                $cadenaSql.=" cont.correo , ";
                                $cadenaSql.=" cont.correo_secundario, ";
                                $cadenaSql.=" cont.telefono , ";
                                $cadenaSql.=" cont.celular";
                                $cadenaSql.=" FROM concurso.persona bas ";
                                $cadenaSql.=" LEFT OUTER JOIN concurso.contacto cont ON cont.consecutivo_persona=bas.consecutivo";
                                $cadenaSql.=" LEFT OUTER JOIN general.pais ps ";
                                $cadenaSql.=" ON ps.id_pais=cont.pais_residencia ";
                                $cadenaSql.=" LEFT OUTER JOIN general.ciudad cud ";
                                $cadenaSql.=" ON cud.id_ciudad=cont.ciudad_residencia ";
                                $cadenaSql.=" WHERE bas.consecutivo='".$variable['id_usuario']."'";
                            break;
                        case "consultarFormacion":
                                $cadenaSql=" SELECT DISTINCT ";
                                $cadenaSql.=" form.consecutivo_formacion, ";
                                $cadenaSql.=" form.consecutivo_persona, ";
                                $cadenaSql.=" form.codigo_modalidad, ";
                                $cadenaSql.=" modo.nombre modalidad,";
                                $cadenaSql.=" form.codigo_nivel, ";
                                $cadenaSql.=" nv.nombre nivel,";
                                $cadenaSql.=" form.pais_formacion, ";
                                $cadenaSql.=" ps.nombre_pais pais,";
                                $cadenaSql.=" form.codigo_institucion, ";
                                $cadenaSql.=" form.nombre_institucion, ";
                                $cadenaSql.=" form.codigo_programa, ";
                                $cadenaSql.=" form.nombre_programa, ";
                                $cadenaSql.=" form.cursos_aprobados, ";
                                $cadenaSql.=" per.nombre periodicidad, ";
                                $cadenaSql.=" form.graduado, ";
                                $cadenaSql.=" form.fecha_grado, ";
                                $cadenaSql.=" form.promedio ";
                                $cadenaSql.=" FROM concurso.persona bas ";
                                $cadenaSql.=" INNER JOIN concurso.formacion form ON form.consecutivo_persona=bas.consecutivo";
                                $cadenaSql.=" INNER JOIN general.modalidad_educacion modo ON modo.codigo_modalidad=form.codigo_modalidad ";
                                $cadenaSql.=" INNER JOIN general.nivel nv ON nv.codigo_nivel=form.codigo_nivel";
                                $cadenaSql.=" LEFT OUTER JOIN general.nivel per ON per.codigo_nivel=form.cursos_temporalidad AND per.tipo_nivel='Temporalidad' ";
                                $cadenaSql.=" INNER JOIN general.pais ps ON ps.id_pais=form.pais_formacion";
                                $cadenaSql.=" WHERE bas.consecutivo='".$variable['id_usuario']."'";
                                $cadenaSql.=" ORDER BY form.codigo_nivel, ";
                                $cadenaSql.=" form.fecha_grado";    
                            break;      
                        case "consultarExperiencia":
                                $cadenaSql=" SELECT DISTINCT";
                                $cadenaSql.=" prof.consecutivo_experiencia,";
                                $cadenaSql.=" prof.consecutivo_persona,";
                                $cadenaSql.=" prof.codigo_nivel_experiencia, ";
                                $cadenaSql.=" (SELECT nombre FROM general.nivel WHERE codigo_nivel=prof.codigo_nivel_experiencia) nivel_experiencia,";
                                $cadenaSql.=" prof.pais_experiencia,";
                                $cadenaSql.=" ps.nombre_pais pais,";
                                $cadenaSql.=" prof.codigo_nivel_institucion, ";
                                $cadenaSql.=" (SELECT nombre FROM general.nivel WHERE codigo_nivel=prof.codigo_nivel_institucion) nivel_institucion,";
                                $cadenaSql.=" prof.codigo_institucion,";                                
                                $cadenaSql.=" prof.nombre_institucion, ";
                                $cadenaSql.=" prof.direccion_institucion,";
                                $cadenaSql.=" prof.correo_institucion,";
                                $cadenaSql.=" prof.telefono_institucion, ";
                                $cadenaSql.=" prof.cargo,";
                                $cadenaSql.=" prof.descripcion_cargo,";
                                $cadenaSql.=" prof.actual,";
                                $cadenaSql.=" prof.fecha_inicio,";
                                $cadenaSql.=" prof.fecha_fin ";
                                $cadenaSql.=" FROM concurso.persona bas "; 
                                $cadenaSql.=" INNER JOIN concurso.experiencia_laboral prof ON prof.consecutivo_persona=bas.consecutivo";
                                $cadenaSql.=" INNER JOIN general.pais ps ON ps.id_pais=prof.pais_experiencia";
                                $cadenaSql.=" WHERE bas.consecutivo='".$variable['id_usuario']."'";
                                $cadenaSql.=" ORDER BY prof.fecha_inicio DESC";    
                            break;    
                        case "consultarDocencia":
                                $cadenaSql=" SELECT DISTINCT";
                                $cadenaSql.=" doc.consecutivo_docencia,";
                                $cadenaSql.=" doc.consecutivo_persona,";
                                $cadenaSql.=" doc.codigo_nivel_docencia,";
                                $cadenaSql.=" (SELECT nombre FROM general.nivel WHERE codigo_nivel=doc.codigo_nivel_docencia) nivel_docencia,";
                                $cadenaSql.=" doc.pais_docencia,";
                                $cadenaSql.=" ps.nombre_pais pais,";
                                $cadenaSql.=" doc.codigo_nivel_institucion,";
                                $cadenaSql.=" (SELECT nombre FROM general.nivel WHERE codigo_nivel=doc.codigo_nivel_institucion) nivel_institucion,";
                                $cadenaSql.=" doc.codigo_institucion,";
                                $cadenaSql.=" doc.nombre_institucion,";
                                $cadenaSql.=" doc.direccion_institucion,";
                                $cadenaSql.=" doc.correo_institucion,";
                                $cadenaSql.=" doc.telefono_institucion,";
                                $cadenaSql.=" doc.codigo_vinculacion,";
                                $cadenaSql.=" doc.nombre_vinculacion,";
                                $cadenaSql.=" doc.descripcion_docencia,";
                                $cadenaSql.=" doc.actual,";
                                $cadenaSql.=" doc.fecha_inicio,";
                                $cadenaSql.=" doc.fecha_fin,";
                                $cadenaSql.=" doc.horas_catedra";
                                $cadenaSql.=" FROM concurso.persona bas "; 
                                $cadenaSql.=" INNER JOIN concurso.experiencia_docencia doc ON doc.consecutivo_persona=bas.consecutivo";
                                $cadenaSql.=" INNER JOIN general.pais ps ON ps.id_pais=doc.pais_docencia";
                                $cadenaSql.=" WHERE bas.consecutivo='".$variable['id_usuario']."'";
                                $cadenaSql.=" ORDER BY doc.fecha_inicio DESC";    
                            break;                                
                        case "consultarActividad":
                                $cadenaSql=" SELECT DISTINCT";
                                $cadenaSql.=" act.consecutivo_actividad,";
                                $cadenaSql.=" act.consecutivo_persona, ";
                                $cadenaSql.=" act.pais_actividad,";
                                $cadenaSql.=" ps.nombre_pais pais,";
                                $cadenaSql.=" act.codigo_nivel_institucion, ";
                                $cadenaSql.=" (SELECT nombre FROM general.nivel WHERE codigo_nivel=act.codigo_nivel_institucion) nivel_institucion,";
                                $cadenaSql.=" act.codigo_institucion,";
                                $cadenaSql.=" act.nombre_institucion,";
                                $cadenaSql.=" act.correo_institucion, ";
                                $cadenaSql.=" act.telefono_institucion, ";
                                $cadenaSql.=" act.codigo_tipo_actividad,";
                                $cadenaSql.=" act.nombre_tipo_actividad, ";
                                $cadenaSql.=" act.nombre_actividad, ";
                                $cadenaSql.=" act.descripcion, ";
                                $cadenaSql.=" act.jefe_actividad,";
                                $cadenaSql.=" act.fecha_inicio,";
                                $cadenaSql.=" act.fecha_fin,";
                                $cadenaSql.=" act.rol_investigacion, ";
                                $cadenaSql.=" act.tipo_producto, ";
                                $cadenaSql.=" act.nombre_ponencia, ";
                                $cadenaSql.=" act.nombre_evento, ";
                                $cadenaSql.=" act.titulo_patente, ";
                                $cadenaSql.=" act.numero_aprobacion_patente, ";
                                $cadenaSql.=" act.ano_aprobacion, ";
                                $cadenaSql.=" act.nombre_proyecto";
                                $cadenaSql.=" FROM concurso.persona bas "; 
                                $cadenaSql.=" INNER JOIN concurso.actividad_academica act ON act.consecutivo_persona=bas.consecutivo";
                                $cadenaSql.=" INNER JOIN general.nivel n on act.tipo_producto = n.nombre and n.estado = 'A'";
                                $cadenaSql.=" LEFT JOIN general.pais ps ON ps.id_pais=act.pais_actividad";
                                $cadenaSql.=" WHERE bas.consecutivo='".$variable['id_usuario']."'";
                                $cadenaSql.=" ORDER BY act.fecha_inicio DESC";   
                            break;                            
                        case "consultarInvestigacion":
                                $cadenaSql=" SELECT DISTINCT";
                                $cadenaSql.=" inv.consecutivo_investigacion,";
                                $cadenaSql.=" inv.consecutivo_persona,";
                                $cadenaSql.=" inv.pais_investigacion,";
                                $cadenaSql.=" ps.nombre_pais pais,";
                                $cadenaSql.=" inv.codigo_nivel_institucion,";
                                $cadenaSql.=" (SELECT nombre FROM general.nivel WHERE codigo_nivel=inv.codigo_nivel_institucion) nivel_institucion,";
                                $cadenaSql.=" inv.codigo_institucion,";
                                $cadenaSql.=" inv.nombre_institucion,";
                                $cadenaSql.=" inv.direccion_institucion,";
                                $cadenaSql.=" inv.correo_institucion,";
                                $cadenaSql.=" inv.telefono_institucion,";
                                $cadenaSql.=" inv.titulo_investigacion,";
                                $cadenaSql.=" inv.jefe_investigacion,";
                                $cadenaSql.=" inv.descripcion_investigacion,";
                                $cadenaSql.=" inv.direccion_investigacion,";
                                $cadenaSql.=" inv.actual,";
                                $cadenaSql.=" inv.fecha_inicio,";
                                $cadenaSql.=" inv.fecha_fin,";
                                $cadenaSql.=" inv.grupo_investigacion,";
                                $cadenaSql.=" inv.categoria_grupo,";
                                $cadenaSql.=" inv.rol_investigacion ";
                                $cadenaSql.=" FROM concurso.persona bas "; 
                                $cadenaSql.=" INNER JOIN concurso.experiencia_investigacion inv ON inv.consecutivo_persona=bas.consecutivo";
                                $cadenaSql.=" INNER JOIN general.pais ps ON ps.id_pais=inv.pais_investigacion";
                                $cadenaSql.=" WHERE bas.consecutivo='".$variable['id_usuario']."'";
                                $cadenaSql.=" ORDER BY inv.fecha_inicio DESC";    
                            break;    
                        case "consultarProduccion":
                                $cadenaSql=" SELECT DISTINCT";
                                $cadenaSql.=" prod.consecutivo_produccion,";
                                $cadenaSql.=" prod.consecutivo_persona,";
                                $cadenaSql.=" prod.codigo_tipo_produccion,";
                                $cadenaSql.=" (CASE WHEN prod.codigo_tipo_produccion!=0
                                                    THEN  (SELECT nombre FROM general.nivel WHERE codigo_nivel=prod.codigo_tipo_produccion)
                                                    ELSE prod.nombre_tipo_produccion END) nombre_tipo_produccion, ";
                                $cadenaSql.=" prod.titulo_produccion,";
                                $cadenaSql.=" prod.nombre_autor,";
                                //$cadenaSql.=" prod.nombre_producto_incluye,";
                                $cadenaSql.=" prod.nombre_editorial,";
                                $cadenaSql.=" prod.volumen,";
                                $cadenaSql.=" prod.pagina,";
                                $cadenaSql.=" prod.codigo_isbn,";
                                $cadenaSql.=" prod.codigo_issn,";
                                //$cadenaSql.=" prod.indexado,";
                                //$cadenaSql.=" prod.pais_produccion,";
                                //$cadenaSql.=" prod.departamento_produccion,";
                                //$cadenaSql.=" prod.ciudad_produccion,";
                                //$cadenaSql.=" city.nombre ciudad,";
                                $cadenaSql.=" prod.descripcion,";
                                $cadenaSql.=" prod.direccion_produccion,";
                                $cadenaSql.=" prod.fecha_produccion";
                                $cadenaSql.=" FROM concurso.persona bas "; 
                                $cadenaSql.=" INNER JOIN concurso.produccion_academica prod ON prod.consecutivo_persona=bas.consecutivo";
                                //$cadenaSql.=" INNER JOIN general.ciudad city ON city.id_ciudad=prod.ciudad_produccion";
                                $cadenaSql.=" INNER join general.nivel n on prod.nombre_tipo_produccion = n.nombre and n.estado = 'A'";
                                $cadenaSql.=" WHERE bas.consecutivo='".$variable['id_usuario']."'";
                                $cadenaSql.=" ORDER BY prod.fecha_produccion DESC";    
                            break;                              
                        case "consultarIdiomas":
                                $cadenaSql=" SELECT DISTINCT";
                                $cadenaSql.=" conidm.consecutivo_conocimiento,";
                                $cadenaSql.=" conidm.consecutivo_persona, ";
                                $cadenaSql.=" conidm.codigo_idioma, ";
                                $cadenaSql.=" idm.nombre idioma, ";
                                $cadenaSql.=" conidm.nivel_lee, ";
                               // $cadenaSql.=" (SELECT nombre FROM general.nivel WHERE codigo_nivel=to_number(conidm.nivel_lee,'99')) nombre_nivel_lee,";
                                $cadenaSql.=" conidm.nivel_escribe,";
                               // $cadenaSql.=" (SELECT nombre FROM general.nivel WHERE codigo_nivel=to_number(conidm.nivel_escribe,'99')) nombre_nivel_escribe,";
                                $cadenaSql.=" conidm.nivel_habla, ";
                               // $cadenaSql.=" (SELECT nombre FROM general.nivel WHERE codigo_nivel=to_number(conidm.nivel_habla,'99')) nombre_nivel_habla,";
                                $cadenaSql.=" conidm.certificacion,";
                                $cadenaSql.=" conidm.institucion_certificacion, ";
                                $cadenaSql.=" conidm.idioma_concurso, ";
                                $cadenaSql.=" conidm.tipo_examen, ";
                                $cadenaSql.=" conidm.nivel_certificado ";
                                $cadenaSql.=" FROM concurso.persona bas "; 
                                $cadenaSql.=" INNER JOIN concurso.conocimiento_idioma conidm ON conidm.consecutivo_persona=bas.consecutivo";    
                                $cadenaSql.=" INNER JOIN general.idioma idm ON idm.codigo_idioma=conidm.codigo_idioma";
                                $cadenaSql.=" WHERE bas.consecutivo='".$variable['id_usuario']."'";
                                $cadenaSql.=" ORDER BY idm.nombre DESC";   
                            break;                              
                        
            case "insertValidacionSoporte":
                $cadenaSql = "INSERT INTO concurso.validacion_soporte ";
                $cadenaSql .= "(consecutivo_soporte_ins, valido, observacion, estado) ";
                $cadenaSql .= "VALUES ";
                $cadenaSql .= "(" . $variable['idSoporte'] . ", " . $variable["validacion"] . ", '" . $variable["observacion"] . "', 'A')";
                break;

            case "existValidacionSoporte":
                $cadenaSql = "SELECT EXISTS(SELECT 1 AS validado FROM concurso.validacion_soporte ";
                $cadenaSql .= "WHERE estado='A' ";
                $cadenaSql .= "AND consecutivo_soporte_ins = " . $variable . ")";
                break;

            case "consultaValidacionSoporte":
                $cadenaSql = "SELECT valido, observacion FROM concurso.validacion_soporte ";
                $cadenaSql .= "WHERE estado='A' ";
                $cadenaSql .= "AND consecutivo_soporte_ins = " . $variable;
                break;

            case "actualizaValidacionSoporte":
                $cadenaSql = "UPDATE concurso.validacion_soporte ";
                $cadenaSql .= "SET ";
                if ($variable["validacion"] != '') {
                    $cadenaSql .= "valido=" . $variable["validacion"]. ", ";
                }
                $cadenaSql .= "observacion='" . $variable["observacion"] . "' ";
                $cadenaSql .= "WHERE estado='A' ";
                $cadenaSql .= "AND consecutivo_soporte_ins = " . $variable['idSoporte'];
                break;


            case "insertValidacionTipoSoporte":
                $cadenaSql = "INSERT INTO concurso.validacion_tipo_soporte ";
                $cadenaSql .= "(consecutivo_inscrito, tipo_dato, valido, observacion, estado) ";
                $cadenaSql .= "VALUES (" . $variable["consecutivo_inscrito"] . ", '" . $variable["tipo_dato"] . "', " . $variable["validacion"] . ", '" . $variable["observacion"] . "', 'A')";
                break;

            case "existValidacionTipoSoporte":
                $cadenaSql = "SELECT EXISTS(SELECT 1 AS validado FROM concurso.validacion_tipo_soporte ";
                $cadenaSql .= "WHERE estado='A' ";
                $cadenaSql .= "AND consecutivo_inscrito = " . $variable["consecutivo_inscrito"] . " ";
                $cadenaSql .= "AND tipo_dato='" . $variable["tipo_dato"] . "')";
                break;

            case "consultaValidacionTipoSoporte":
                $cadenaSql = "SELECT valido, observacion FROM concurso.validacion_tipo_soporte ";
                $cadenaSql .= "WHERE estado='A' ";
                $cadenaSql .= "AND consecutivo_inscrito = " . $variable["consecutivo_inscrito"] . " ";
                $cadenaSql .= "AND tipo_dato='" . $variable["tipo_dato"] . "'";
                break;

            case "actualizaValidacionTipoSoporte":
                $cadenaSql = "UPDATE concurso.validacion_tipo_soporte ";
                $cadenaSql .= "SET ";
                if ($variable["validacion"] != '') {
                    $cadenaSql .= "valido=" . $variable["validacion"]. ", ";
                }
                $cadenaSql .= "observacion='" . $variable["observacion"] . "' ";
                $cadenaSql .= "WHERE estado='A' ";
                $cadenaSql .= "AND consecutivo_inscrito = " . $variable["consecutivo_inscrito"] . " ";
                $cadenaSql .= "AND tipo_dato='" . $variable["tipo_dato"] . "'";
                break;
                                        
				/**
				 * Clausulas genéricas. se espera que estén en todos los formularios
				 * que utilicen esta plantilla
				 */

			case "iniciarTransaccion":
				$cadenaSql="START TRANSACTION";
				break;

			case "finalizarTransaccion":
				$cadenaSql="COMMIT";
				break;

			case "cancelarTransaccion":
				$cadenaSql="ROLLBACK";
				break;

		}
		return $cadenaSql;
	}
}

?>
