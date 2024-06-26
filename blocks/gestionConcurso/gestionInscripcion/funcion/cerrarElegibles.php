<?php

namespace gestionConcurso\gestionInscripcion\funcion;

use gestionConcurso\gestionInscripcion\funcion\redireccion;

include_once('redireccionar.php');

if (!isset($GLOBALS ["autorizado"])) {
    include("../index.php");
    exit();
}

class cerrarElegibles
{

    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miFuncion;
    var $miSql;
    var $conexion;
    var $miLogger;

    function __construct($lenguaje, $sql, $funcion, $miLogger)
    {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;
        $this->miFuncion = $funcion;
        $this->miLogger = $miLogger;
    }

    function procesarFormulario()
    {
        $SQLs = [];

        $conexion = "estructura";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");
        //busca inscritos a la etapa
        $parametro = array('consecutivo_concurso' => $_REQUEST['consecutivo_concurso'],
            'faseAct' => $_REQUEST['consecutivo_calendario'],
            'fecha_registro' => date("Y-m-d H:m:s"),
            'nombre_concurso' => $_REQUEST['nombre_concurso'],
            'nombre' => $_REQUEST['nombre'],
            'faseNueva' => isset($_REQUEST['etapaPasa']) ? $_REQUEST['etapaPasa'] : 0,
            'faseDesc' => '',
            'hoy' => date("Y-m-d"),);

        $cadena_sql = $this->miSql->getCadenaSql("consultarRegistradoEtapa", $parametro);
        $SQLs[] = $cadena_sql;
        $resultadoListaElegibles = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");
        //consulta datos de puntaje de criterios
        $cadena_sql = $this->miSql->getCadenaSql("consultarCriteriosEtapa", $parametro);
        $SQLs[] = $cadena_sql;
        $resultadoCriterio = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

        $cadena_sql = $this->miSql->getCadenaSql("consultarFasesEvaluacion", $parametro);
        $SQLs[] = $cadena_sql;
        $resultadoFases = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

        if ($resultadoListaElegibles) {   //llama imagen progreso
            $this->progreso($esteBloque);
            //recorre los registros de los que se validaron
            $puntos_aprueba = ($resultadoCriterio[0]['maximo_fase'] * $_REQUEST['porcentaje_aprueba_concurso']) / 100;
            $puntos_aprueba_especial = ($resultadoCriterio[0]['maximo_fase_especial'] * $_REQUEST['porcentaje_aprueba_concurso']) / 100;

            foreach ($resultadoListaElegibles as $key => $value) {
                //verifica que haya pasado todas las etapas
                $cadena_sql = $this->miSql->getCadenaSql("validarPerfilEspecial", $resultadoListaElegibles[$key]['consecutivo_inscrito']);
                $perfilEspecial= $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");
                
                if ($resultadoListaElegibles[$key]['aprobado'] == $resultadoFases[0]['fases_evalua']) {

                                        
                    $parametro['consecutivo_inscrito'] = $resultadoListaElegibles[$key]['consecutivo_inscrito'];
                    $parametro['tipoEvaluacion'] = ($perfilEspecial)?'evaluacion_perfil_especial': 'concurso_evaluar';
                    $cadena_sql = $this->miSql->getCadenaSql("consultarDetalleEvaluacionParcial", $parametro);
                    $SQLs[] = $cadena_sql;
                    $resultadoParcial = $esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");
                    // var_dump($resultadoParcial);

                    if ($resultadoParcial) {
                        $evaluar = array();
                        $puntaje = array();
                        foreach ($resultadoParcial as $parc => $value) {   //recorre las evaluaciones de criterios, registradas por inscrito y calcula el puntaje final
                            if (in_array($resultadoParcial[$parc]['id_evaluar'], $evaluar)) {
                                $pos = array_search($resultadoParcial[$parc]['id_evaluar'], $evaluar);
                                $puntaje[$pos]['puntos'] += round((double)$resultadoParcial[$parc]['puntaje_parcial'], 2);
                            } else {
                                array_push($evaluar, $resultadoParcial[$parc]['id_evaluar']);
                                $pos = array_search($resultadoParcial[$parc]['id_evaluar'], $evaluar);
                                $puntaje[$pos]['evaluar'] = $resultadoParcial[$parc]['id_evaluar'];
                                $puntaje[$pos]['puntos'] = round((double)$resultadoParcial[$parc]['puntaje_parcial'], 2);
                                $puntaje[$pos]['aprueba'] = $resultadoParcial[$parc]['puntos_aprueba'];
                                $puntaje[$pos]['jurados'] = $resultadoParcial[$parc]['jurados'];
                                $puntaje[$pos]['id_inscrito'] = $resultadoParcial[$parc]['id_inscrito'];
                            }
                        }
                        $tipoPuntos_aprueba = ($perfilEspecial)? $puntos_aprueba_especial:$puntos_aprueba;

                        $fase = array('puntos' => 0, 'Paprueba' => $tipoPuntos_aprueba, 'aprobo' => array());
                        $evaluacion = array();
                        $promedio = 0;
                        //calcula puntajes ya crea el arreglo para guardar
                        foreach ($puntaje as $eval => $value) {
                            $final = round(($puntaje[$eval]['puntos'] / $puntaje[$eval]['jurados']), 2);
                            //se calcula los puntajes final de la fase y de aprobación
                            $fase['puntos'] += $final;
                            $puntosFinal = array('id_inscrito' => $puntaje[$eval]['id_inscrito'],
                                'id_evaluar' => $puntaje[$eval]['evaluar'],
                                'puntaje_final' => $final,
                                'observacion' => " Cálculo de puntaje final",
                                'fecha_registro' => $parametro['fecha_registro'],
                                'aprobo' => ($final >= $puntaje[$eval]['aprueba']) ? 'SI' : 'NO',
                            );
                            array_push($fase['aprobo'], $puntosFinal['aprobo']);

                            $puntosParcial = array('id_inscrito' => $puntosFinal['id_inscrito'],
                                'id_evaluar' => $puntosFinal['id_evaluar'],
                                'puntaje_final' => $final,
                            );
                            array_push($evaluacion, $puntosParcial);
                            $promedio += $final;
                            unset($final);
                            unset($puntosFinal);
                        }
                        unset($evaluar);
                        unset($puntaje);

                    }

                    //se valida si pasa todos las evaluaciones y alcanza el porcentaje de aprobacion
                    if (isset($fase)) {
                        $parametroProm = array('id_inscrito' => $resultadoListaElegibles[$key]['consecutivo_inscrito'],
                            'id_calendario' => $parametro['faseAct'],
                            'puntaje_promedio' => $promedio,
                            'evaluaciones' => json_encode($evaluacion),
                            'fecha_registro' => $parametro['fecha_registro'],
                            'id_reclamacion' => 0,
                        );
                        $this->cadena_sql = $this->miSql->getCadenaSql("registroEvaluacionPromedio", $parametroProm);
                        $SQLs[] = $cadena_sql;
                        $resultadoPromedio = $esteRecursoDB->ejecutarAcceso($this->cadena_sql, "registro", $parametroProm, "registroEvaluacionPromedio");
                        unset($evaluacion);
                        unset($promedio);
                    }
                    //se valida si pasa todos las evaluaciones y alcanza el porcentaje de aprobacion
                    if (isset($fase)) {
                        $resultadoTipopuntaje =  ($perfilEspecial)? $resultadoCriterio[0]['maximo_fase_especial']: $resultadoCriterio[0]['maximo_fase'];            
                        
                        $porcetaje_fase = ($fase['puntos'] * 100) / $resultadoTipopuntaje;
                        if (!in_array("NO", $fase['aprobo']) && $porcetaje_fase >= $_REQUEST['porcentaje_aprueba_concurso']) {
                            $parametro['faseDesc'] = ',con ' . $fase['puntos'] . ' puntos y un minimo para aprobar de ' . $tipoPuntos_aprueba . ' puntos;';
                            $parametro['faseDesc'] .= 'Porcentaje total de  ' . number_format($porcetaje_fase, 2) . '%, correspondiente al total de las Evaluaciones';
                            $parametro['inscripcion'] = $resultadoListaElegibles[$key]['consecutivo_inscrito'];
                            $this->pasaFase($parametro, $esteRecursoDB);
                        }
                        unset($fase);
                    }
                }
            }
            $this->cerrarFase($parametro, $esteRecursoDB);
            redireccion::redireccionar('CerroFase', $parametro);
            exit();
        } else {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            $c = 0;
            while ($c < count($SQLs)) {
                $SQLsDec[$c] = $this->miConfigurador->fabricaConexiones->crypto->codificar($SQLs[$c]);
                $c++;
            }

            $query = json_encode($SQLsDec);
            $error = json_encode(error_get_last());

            $datosLog = array(
                'id_usuario' => $_REQUEST['id_usuario'],
                'fecha_log' => date("Y-m-d H:i:s"),
                'host' => $ip,
                'query' => $query,
                'error' => $error,
            );

            $cadenaSQL = $this->miSql->getCadenaSql("insertarLogError", $datosLog);
            $esteRecursoDB->ejecutarAcceso($cadenaSQL, 'busqueda');

            redireccion::redireccionar('noCerroFase', $parametro);
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

    function progreso($esteBloque)
    {
        // ------------------Inicio Division para progreso-------------------------
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $directorioImg = $url . "/blocks/" . $esteBloque ["grupo"] . "" . $esteBloque ["nombre"] . "/images/";
        echo '<div id="divcarga" style="color:#000;margin-top:20px; font-size:20px;font-weight:bold;text-align:center;height:300px;" >
                  <span >Procesando la información, Espere por favor ...  </span>
                  <img  src="' . $directorioImg . 'load.gif">
              </div>';
        // ------------------Fin Division para progreso-------------------------
        //llama funcion para visualizar al div cuando termina de cargar
        //echo "<script language='javascript'> setTimeout(function(){desbloquea('divcarga','tabs')},1000)  </script>";
    }

    function pasaFase($parametro, $esteRecursoDB)
    {
        //registra los aspirantes que pasan la fase
        $arregloDatos = array('consecutivo_inscrito' => $parametro['inscripcion'],
            'consecutivo_calendario' => $parametro['faseNueva'],
            'observacion' => 'Cierre automatico fase ' . $parametro['nombre'] . $parametro['faseDesc'],
            'fecha_registro' => $parametro['fecha_registro'],
            'consecutivo_calendario_ant' => $parametro['faseAct'],
        );
        $this->cadena_sql = $this->miSql->getCadenaSql("registroEtapaInscrito", $arregloDatos);
        $SQLs[] = $this->cadena_sql;
        $resultadoCierre = $esteRecursoDB->ejecutarAcceso($this->cadena_sql, "registro", $arregloDatos, "registroCierreEtapaInscrito");
    }

    function cerrarFase($parametro, $esteRecursoDB)
    {
        //actualiza tipo de cierre
        $arregloCierre = array('consecutivo_inscrito' => $parametro['inscripcion'],
            'consecutivo_calendario' => $parametro['faseAct'],
            'cierre' => 'final',
        );
        $this->cadena_sql = $this->miSql->getCadenaSql("actualizaCierreCalendario", $arregloCierre);
        $SQLs[] = $this->cadena_sql;
        $resultadoCierre = $esteRecursoDB->ejecutarAcceso($this->cadena_sql, "actualiza", $arregloCierre, "actualizaCierreCalendarioRequisito");
    }


}

$miRegistrador = new cerrarElegibles($this->lenguaje, $this->sql, $this->funcion, $this->miLogger);

$resultado = $miRegistrador->procesarFormulario();
?>