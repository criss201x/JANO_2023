<script type='text/javascript'>

    $(function () {
        $(function () {
            $('#tablaLog').ready(function () {
                var table = $('#tablaLog').dataTable({
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
                            "sLast": "Último",
                            "sNext": "Siguiente",
                            "sPrevious": "Anterior"
                        }
                    },
                    processing: true,
                    searching: true,
                    info: true,
                    "scrollY": "300px",
                    "scrollCollapse": false,
                    "bLengthChange": false,
                    "bPaginate": false,
                    "aoColumns": [
                        {sWidth: "8%"},
                        {sWidth: "8%"},
                        {sWidth: "8%"},
                        {sWidth: "16%"},
                        {sWidth: "14%"},
                        {sWidth: "8%"},
                        {sWidth: "8%"},
                    ]
                });
            });
        });

        $(function () {
            $('#tablaLogError').ready(function () {
                var table = $('#tablaLogError').dataTable({
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
                    processing: true,
                    searching: true,
                    info: true,
                    "scrollY": "300px",
                    "scrollCollapse": false,
                    "bLengthChange": false,
                    "bPaginate": false,
                    "aoColumns": [
                        {sWidth: "4%"},
                        {sWidth: "6%"},
                        {sWidth: "8%"},
                        {sWidth: "4%"},
                        {sWidth: "20%"},
                        {sWidth: "8%"},
                        /*{sWidth: "8%"},
                        {sWidth: "8%"},
                        {sWidth: "8%"},*/
                    ]
                });
            });
        });
    });
</script>
