<?php 
//Se coloca esta condición para evitar cargar algunos scripts en el formulario de confirmación de entrada de datos.
//if(!isset($_REQUEST["opcion"])||(isset($_REQUEST["opcion"]) && $_REQUEST["opcion"]!="confirmar")){
/*
?>
        $('#tablaProcesos').dataTable({bJQueryUI : true,
        "sPaginationType": "full_numbers"
        });
       
        //sin cabecera ni pie
        $('#tablaProcesos').DataTable({bFilter: false, bInfo: false}});
         */  

$esteBloque = $this->miConfigurador->getVariableConfiguracion ( 'esteBloque' );

$enlace = "action=index.php";
$enlace .= "&bloqueNombre=" . $esteBloque ["nombre"];
$enlace .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$enlace .= "&procesarAjax=true";
$enlace .= "&funcion=codificar";
$enlace .= "&tiempo=" . $_REQUEST['tiempo'];
$directorio = $this->miConfigurador->getVariableConfiguracion ( "host" );
$directorio .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
$directorio .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$enlace = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $enlace, $directorio ); 

    ?>  

var sel = $('#radioBtn a').data('title');
$('#<?php echo $this->campoSeguro("validacion")?>').val(sel);

$('#radioBtn a').on('click', function(){
    var sel = $(this).data('title');
    var tog = $(this).data('toggle');

    $.ajax({
        url: "<?php echo $enlace;?>",
        type: 'POST',
        data: {valor: tog},
        success: function(response){
            var result = response.replace(/\n/g, "");
            $('#'+result).prop('value', sel == 'SI' ? 'true' : 'false');
        }
    });

    $('#'+tog).prop('value', sel == 'SI' ? 'true' : 'false');

    $('a[data-toggle="'+tog+'"]').not('[data-title="'+sel+'"]').removeClass('active').addClass('notActive');
    $('a[data-toggle="'+tog+'"][data-title="'+sel+'"]').removeClass('notActive').addClass('active');
});

// Asociar el widget de validación al formulario datosFormacion
$("#datosFormacion").validationEngine({
    promptPosition : "centerRight",
    scroll: false
});

$(function() {
    $("#datosFormacion").submit(function() {
        $resultado=$("#datosFormacion").validationEngine("validate");
        if ($resultado) {
            return true;
        }
        return false;
    });
});

// Asociar el widget de validación al formulario datosProfesional
$("#datosProfesional").validationEngine({
    promptPosition : "centerRight",
    scroll: false
});

$(function() {
    $("#datosProfesional").submit(function() {
        $resultado=$("#datosProfesional").validationEngine("validate");
        if ($resultado) {
            return true;
        }
        return false;
    });
});

// Asociar el widget de validación al formulario datosDocencia
$("#datosDocencia").validationEngine({
    promptPosition : "centerRight",
    scroll: false
});

$(function() {
    $("#datosDocencia").submit(function() {
        $resultado=$("#datosDocencia").validationEngine("validate");
        if ($resultado) {
            return true;
        }
        return false;
    });
});

// Asociar el widget de validación al formulario datosInvestigacion
$("#datosInvestigacion").validationEngine({
    promptPosition : "centerRight",
    scroll: false
});

$(function() {
    $("#datosInvestigacion").submit(function() {
        $resultado=$("#datosInvestigacion").validationEngine("validate");
        if ($resultado) {
            return true;
        }
        return false;
    });
});

// Asociar el widget de validación al formulario datosIdioma
$("#datosIdioma").validationEngine({
    promptPosition : "centerRight",
    scroll: false
});

$(function() {
    $("#datosIdioma").submit(function() {
        $resultado=$("#datosIdioma").validationEngine("validate");
        if ($resultado) {
            return true;
        }
        return false;
    });
});

$('#tablaListaGeneral').DataTable({
"language": {
    "lengthMenu": "Mostrar _MENU_ registros por p&aacute;gina",
    "zeroRecords": "No se encontraron registros coincidentes",
    "info": "Mostrando _PAGE_ de _PAGES_ p&aacute;ginas",
    "infoEmpty": "No hay datos registrados",
    "infoFiltered": "(filtrado de un m&aacute;ximo de _MAX_)",
    "search": "Buscar:",
    "paginate": {
                "first":      "Primera",
                "last":       "&Uacute;ltima",
                "next":       "Siguiente",
                "previous":   "Anterior"
            }
},
"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
});

$('#tablaListaParcial').DataTable({
"language": {
    "lengthMenu": "Mostrar _MENU_ registros por p&aacute;gina",
    "zeroRecords": "No se encontraron registros coincidentes",
    "info": "Mostrando _PAGE_ de _PAGES_ p&aacute;ginas",
    "infoEmpty": "No hay datos registrados",
    "infoFiltered": "(filtrado de un m&aacute;ximo de _MAX_)",
    "search": "Buscar:",
    "paginate": {
                "first":      "Primera",
                "last":       "&Uacute;ltima",
                "next":       "Siguiente",
                "previous":   "Anterior"
            }
},
"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
});                      
                      
        // Asociar el widget de validación al formulario
        $("#gestionUsuarios").validationEngine({
            promptPosition : "centerRight", 
            scroll: false
        });

        $(function() {
            $("#gestionUsuarios").submit(function() {
                $resultado=$("#gestionUsuarios").validationEngine("validate");
                if ($resultado) {
                                
                    return true;
                    
                }
                return false;
            });
        });
 
<?php /*?>
               $('#<?php echo $this->campoSeguro('fecha_final')?>').datepicker({
		dateFormat: 'yy-mm-dd',
		maxDate: 0,
		changeYear: true,
		changeMonth: true,
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		    'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		    monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
		    dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
		    dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
		    dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
		    onSelect: function(dateText, inst) {
			var lockDate = new Date($('#<?php echo $this->campoSeguro('fecha_final')?>').datepicker('getDate'));
			$('input#<?php echo $this->campoSeguro('fecha_inicio')?>').datepicker('option', 'maxDate', lockDate);
			 },
			 onClose: function() { 
		 	    if ($('input#<?php echo $this->campoSeguro('fecha_final')?>').val()!='')
                    {
                        $('#<?php echo $this->campoSeguro('fecha_inicio')?>').attr("class", "cuadroTexto ui-widget ui-widget-content ui-corner-all   validate[required]");
                }else {
                        $('#<?php echo $this->campoSeguro('fecha_inicio')?>').attr("class", "cuadroTexto ui-widget ui-widget-content ui-corner-all ");
                    }
			  }
			
	   });
 <?php */?>        
        
        
<?php /*?>$('#<?php echo $this->campoSeguro('fechaFin')?>').datetimepicker({<?php */?>
$('#<?php echo $this->campoSeguro('fechaFin')?>').datepicker({
		<?php /*?>timeFormat: 'HH:mm:ss',<?php */?>
                dateFormat: 'yy-mm-dd',
		minDate: 0,
               <?php /*?> maxDate: 0,<?php */?>
		changeYear: true,
		changeMonth: true,
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		    'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		    monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
		    dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
		    dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
		    dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
		    
			
	   });
               

        
        $(function() {
		$(document).tooltip();
	});
	
	// Asociar el widget tabs a la división cuyo id es tabs
	$(function() {
		$("#tabs").tabs();
	});

        $(function() {
            $("button").button().click(function(event) {
                    event.preventDefault();
            });
        });
$('#<?php echo $this->campoSeguro('tipo_identificacion')?>').width(210);
$("#<?php echo $this->campoSeguro('tipo_identificacion')?>").select2(); 
$('#<?php echo $this->campoSeguro('subsistema')?>').width(210);
$("#<?php echo $this->campoSeguro('subsistema')?>").select2(); 
$('#<?php echo $this->campoSeguro('perfil')?>').width(210);
$("#<?php echo $this->campoSeguro('perfil')?>").select2(); 

<?php 
//}



?>



