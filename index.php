<html>
<head>
	
    <script src="js/jquery-1.11.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/sweetalert.min.js"></script>

	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/index.css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="css/jquery.dataTables.min.css">
	<link rel="stylesheet" href="css/jquery-ui.theme.min.css">
	<link rel="stylesheet" href="css/jquery-ui.structure.min.css">
	<link rel="stylesheet" href="css/sweetalert.css">

	<link rel="stylesheet" type="text/css" href="themes/google.css">

	<script>

		function confirmAlert(){
			swal("Éxito", "Alerta generada!", "success");
		};

		function ignoreAlert(){
			swal("Éxito", "Alerta ignorada!", "success");
		};

		function getNow(){
			var today = new Date();
			var dd = today.getDate();
			var mm = today.getMonth()+1;
			var yyyy = today.getFullYear();

			if(dd<10) {
    			dd='0'+dd
			} 

			if(mm<10) {
			    mm='0'+mm
			} 

			today = dd+'-'+mm+'-'+yyyy;
			
			document.getElementById('dateEnd').value = today;		
		};

		function getDefaultDate(){
			var today = new Date();
			var defaultd = new Date(new Date(today).setMonth(today.getMonth()-3));
			var dd = defaultd.getDate();
			var mm = defaultd.getMonth()+1;
			var yyyy = defaultd.getFullYear();

			if(dd<10) {
    			dd='0'+dd
			} 

			if(mm<10) {
			    mm='0'+mm
			} 

			defaultd = dd+'-'+mm+'-'+yyyy;
			
			document.getElementById('dateStart').value = defaultd;
		};

	</script>
</head>
<body>

	<?php

		if (isset($_REQUEST['mess']))
		{
			$mess = $_REQUEST['mess'];

			switch ($mess)
			{
				case 1: echo '<script> confirmAlert(); </script>'; break;
				case 2: echo '<script> ignoreAlert(); </script>'; break;
			}
		}

	?>

	<div id="header" class="clearfix"><br>
		<h4 class="pull-left" style="padding-left: 5px;">Datos a buscar: </h4><br><br>
		<div id="limit" class="pull-left" style="padding-top: 5px; padding-left: 5px;">
			<button type="button" class="btn lim btn-default active" id="50">50</button>
			<button type="button" class="btn lim btn-default" id="100">100</button>
			<button type="button" class="btn lim btn-default" id="500">500</button>
			<button type="button" class="btn lim btn-default" id="1000">1000</button>
		</div>

		<div style="padding-top: 5px; padding-right: 20px">
			<button type="button" class="btn refresh btn-info pull-right">Recargar datos</button>
		</div>
	</div>

	<div id="baseDateControl" style="padding-top: 20px; padding-left: 5px;">
		<div class="dateControlBlock">
	        Entre <input type="text" name="dateStart" id="dateStart" class="datepicker" value="" size="8" /> y 
	        <input type="text" name="dateEnd" id="dateEnd" class="datepicker" value="" size="8"/>
	    </div>
	</div>


	<div id="tablespace" style="padding: 5px;"><br><br>
	<table id='datatable' class="table table-bordered"><thead><tr>
		<th>ID</th>
		<th>Nodo</th>
		<th>Mensaje</th>
		<th>Historia</th>
		<th>Objeto</th>
		<th>Fecha y hora</th>
		<th>Importancia</th>
		<th>Estado de alerta</th>
		<th>Estado de OS</th>
		<th>ID de OS</th>
		<th>Generar OS</th>
		<th>Ignorar alerta</th>
	</tr></thead>
	<tbody></tbody>
	</table></div>

</body>

<script type="text/javascript" charset="utf-8">

	$(document).ready(function (){

		getNow();
		getDefaultDate();

		var Q = $('button.active').attr('id');
		var dS = $('#dateStart').val();
		var dE = $('#dateEnd').val();

		var A = [Q, dS, dE];
		A = JSON.stringify(A);

    	$('#datatable').dataTable({
    		"createdRow": function (row, data, index) {
        		if(data[7] == "Closed") {
					$(row).addClass('warning');
        		}

        		if(data[9]) {
					if($(row).hasClass('warning')){
						$(row).toggleClass('warning').addClass('success');
					} else {
						$(row).addClass('success');
					}
        		}
        	},
    		"order": [[0, "desc"]],
    		"language": {
    			"url": "localisation/spanish.json"
    		},
        	"sAjaxSource": "datafetch.php?A="+A
		});

		$('#dateStart').datepicker({
			showOn: 'both', 
			buttonImage: 'css/images/calendar.gif', 
			buttonImageOnly: true,
			dateFormat: 'dd-mm-yy',
			autoSize: true,
			maxDate: new Date()
		});

		$('#dateEnd').datepicker({
			showOn: 'both', 
			buttonImage: 'css/images/calendar.gif', 
			buttonImageOnly: true,
			dateFormat: 'dd-mm-yy',
			autoSize: true,
			maxDate: dE,
			minDate: dS
		});
	});

	$('button.btn.lim').click(function(){
		$('button.active').toggleClass('active');
		$(this).addClass('active');

		Q = $(this).attr('id');
		dS = $('#dateStart').val();
		dE = $('#dateEnd').val();

		var A = [Q, dS, dE];
		A = JSON.stringify(A);

		$('#datatable').dataTable({
			"bDestroy": true,
			"createdRow": function (row, data, index) {
        		if(data[7] == "Closed") {
					$(row).addClass('warning');
        		}

        		if(data[9]) {
					if($(row).hasClass('warning')){
						$(row).toggleClass('warning').addClass('success');
					} else {
						$(row).addClass('success');
					}
        		}
        	},
			"order": [[0, "desc"]],
			"language": {
    			"url": "localisation/spanish.json"
    		},
        	"sAjaxSource": "datafetch.php?A="+A
		});
	});

	$('button.btn.refresh').click(function(){
		Q = $('button.btn.lim.active').attr('id');
		dS = $('#dateStart').val();
		dE = $('#dateEnd').val();

		var A = [Q, dS, dE];
		A = JSON.stringify(A);

		$('#datatable').dataTable({
			"bDestroy": true,
			"createdRow": function (row, data, index) {
        		if(data[7] == "Closed") {
					$(row).addClass('warning');
        		}

        		if(data[9]) {
					if($(row).hasClass('warning')){
						$(row).toggleClass('warning').addClass('success');
					} else {
						$(row).addClass('success');
					}
        		}
        	},
			"order": [[0, "desc"]],
			"language": {
    			"url": "localisation/spanish.json"
    		},
        	"sAjaxSource": "datafetch.php?A="+A,

		});
	});

	$('#dateStart').keyup(function(){
		redrawTable();
	});

	$('#dateStart').change(function(){
		redrawTable();
	});

	$('#dateEnd').keyup(function(){
		redrawTable();
	});

	$('#dateEnd').change(function(){
		redrawTable();
	});

	$('body').on('click', '.generar', function(){

		var id = $(this).attr('id');

		swal({
			title: "Confirmación necesaria",
			text: "¿Seguro que quieres generar una OS para esta alerta?",
			type: "warning",
			showCancelButton: true,
			confirmButtonText: "Generar",
			cancelButtonText: "Cancelar",
			closeOnConfirm: false,
			closeOnCancel: true
		},
		function(isConfirm){
			if(isConfirm){
				location.href = "create.php?a="+id;
			}
		});
	});

	$('body').on('click', '.ignorar', function(){

		var id = $(this).attr('id');

		swal({
			title: "Confirmación necesaria",
			text: "¿Seguro que quieres ignorar esta alerta?",
			type: "warning",
			showCancelButton: true,
			confirmButtonText: "Ignorar",
			cancelButtonText: "Cancelar",
			closeOnConfirm: false,
			closeOnCancel: true
		},
		function(isConfirm){
			if(isConfirm){
				location.href = "ignore.php?a="+id;
			}
		});
	});

	function redrawTable(){

		Q = $('button.btn.lim.active').attr('id');
		dS = $('#dateStart').val();
		dE = $('#dateEnd').val();

		var A = [Q, dS, dE];
		A = JSON.stringify(A);

		$('#datatable').dataTable({
			"bDestroy": true,
			"createdRow": function (row, data, index) {
        		if(data[7] == "Closed") {
					$(row).addClass('warning');
        		}

        		if(data[9]) {
					if($(row).hasClass('warning')){
						$(row).toggleClass('warning').addClass('success');
					} else {
						$(row).addClass('success');
					}
        		}
        	},
			"order": [[0, "desc"]],
			"language": {
    			"url": "localisation/spanish.json"
    		},
        	"sAjaxSource": "datafetch.php?A="+A,

		});

		$('.datepicker').datepicker('destroy');

		$('#dateStart').datepicker({
			showOn: 'both', 
			buttonImage: 'css/images/calendar.gif', 
			buttonImageOnly: true,
			dateFormat: 'dd-mm-yy',
			autoSize: true,
			maxDate: dE
		});

		$('#dateEnd').datepicker({
			showOn: 'both', 
			buttonImage: 'css/images/calendar.gif', 
			buttonImageOnly: true,
			dateFormat: 'dd-mm-yy',
			autoSize: true,
			minDate: dS,
			maxDate: new Date(),
		});

		$('.datepicker').datepicker('refresh');
	}

</script>

</html>