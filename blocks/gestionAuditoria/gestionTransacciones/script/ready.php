window.onload = detectarCarga;

function detectarCarga() {
    $('#marcoDatos').show('slow');

    tinyMCE.get('<?php echo $this->campoSeguro('query')?>').theme.resizeTo("100%", 650);

    
}

$("#accordionR").bwlAccordion({
		search: false,
        theme: 'theme-blue',
        toggle: true,
        animation: 'faderight'
    });

<?php ?>

// Asociar el widget de validación al formulario
$("#consultaContratosAprobados").validationEngine({
promptPosition : "centerRight", 
scroll: false,
autoHidePrompt: true,
autoHideDelay: 2000
});


$(function() {
$("#consultaContratosAprobados").submit(function() {
$resultado=$("#consultaContratosAprobados").validationEngine("validate");
if ($resultado) {

return true;
}
return false;
});
});

$('#tablaTitulos').dataTable( {
"sPaginationType": "full_numbers"
} );

$('#tablaParticipantesSociedad').DataTable({
dom: 'T<"clear">lfrtip',
tableTools: {
"sRowSelect": "os",
"aButtons": ["select_all", "select_none"]
},
"language": {
"sProcessing": "Procesando...",
"sLengthMenu": "Mostrar _MENU_ registros",
"sZeroRecords": "No se encontraron resultados",
"sSearch": "Buscar:",
"sLoadingRecords": "Cargando...",
"sEmptyTable": "Ningún dato disponible en esta tabla",
"sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
"sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
"sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
"oPaginate": {
"sFirst": "Primero",
"sLast": "Ãšltimo",
"sNext": "Siguiente",
"sPrevious": "Anterior"
}
},
"columnDefs": [
{
"targets": [0, 1],
"visible": false,
"searchable": false
}
],
processing: true,
searching: true,
info: true,
"scrollY": "400px",
"scrollCollapse": false,
"bLengthChange": false,
"bPaginate": false,
"aoColumns": [
{sWidth: "10%", sClass: "center"},
{sWidth: "10%", sClass: "center"},
{sWidth: "10%", sClass: "center"},
{sWidth: "10%", sClass: "center"},
{sWidth: "10%", sClass: "center"},
{sWidth: "10%", sClass: "center"},
]


});

$('#tablaRegistros').DataTable();

$("#<?php echo $this->campoSeguro('id_usuario') ?>").width(220);

$("#<?php echo $this->campoSeguro('accion') ?>").width(220);
$("#<?php echo $this->campoSeguro('accion') ?>").select2();

$("#<?php echo $this->campoSeguro('ingreso') ?>").width(220);
$("#<?php echo $this->campoSeguro('ingreso') ?>").select2();
$("#<?php echo $this->campoSeguro('ingreso') ?>").parent().hide();

$("#<?php echo $this->campoSeguro('consulta') ?>").width(220);
$("#<?php echo $this->campoSeguro('consulta') ?>").select2();
$("#<?php echo $this->campoSeguro('consulta') ?>").parent().hide();

$("#<?php echo $this->campoSeguro('eliminacion') ?>").width(220);
$("#<?php echo $this->campoSeguro('eliminacion') ?>").select2();
$("#<?php echo $this->campoSeguro('eliminacion') ?>").parent().hide();

$("#<?php echo $this->campoSeguro('registro') ?>").width(220);
$("#<?php echo $this->campoSeguro('registro') ?>").select2();
$("#<?php echo $this->campoSeguro('registro') ?>").parent().hide();

$("#<?php echo $this->campoSeguro('actualizacion') ?>").width(220);
$("#<?php echo $this->campoSeguro('actualizacion') ?>").select2();
$("#<?php echo $this->campoSeguro('actualizacion') ?>").parent().hide();

$("#<?php echo $this->campoSeguro('salida') ?>").width(220);
$("#<?php echo $this->campoSeguro('salida') ?>").select2();
$("#<?php echo $this->campoSeguro('salida') ?>").parent().hide();

$("#<?php echo $this->campoSeguro('solicitud') ?>").width(220);
$("#<?php echo $this->campoSeguro('solicitud') ?>").select2();
$("#<?php echo $this->campoSeguro('solicitud') ?>").parent().hide();
