<?php
session_start();

if (!isset($_SESSION["username"])) {
	header("Location: index.php");
	exit;
}

include_once("conexao.php");

// $sqlLogon = "SELECT uni_codigo, esp_codigo FROM logon WHERE id_login = '" . $_SESSION['usr_codigo'] . "' ORDER BY id DESC LIMIT 1";
@$result_events = "SELECT id, title, color, start, end FROM events where title = '" . $_SESSION['username'] . "' ";
$resultado_events = mysqli_query($conn, @$result_events);
@$result_Hour_Avaliable = "SELECT id, `start`, `end` FROM events where `start` >= now()";
$resultado__Hour_Avaliable = mysqli_query($conn, @$result_Hour_Avaliable);

?>
<!DOCTYPE html>
<html lang='pt-br'>

<head>
	<meta charset='utf-8' />
	<title>Agenda Cursos da Alura</title>
	<link href='css/bootstrap.min.css' rel='stylesheet'>
	<link href='css/fullcalendar.min.css' rel='stylesheet' />
	<link href='css/fullcalendar.print.min.css' rel='stylesheet' media='print' />
	<link href='css/personalizado.css' rel='stylesheet' />
	<link href='css/datepicker/css/datepicker.css' rel='stylesheet'>
	<script src='js/jquery.min.js'></script>
	<script src='js/bootstrap.min.js'></script>
	<script src='js/moment.min.js'></script>
	<script src='js/fullcalendar.min.js'></script>
	<script src='locale/pt-br.js'></script>
	<script src='css/datepicker/js/bootstrap-datepicker.js'></script>
	<script>
		$(document).ready(function() {
			$('#calendar').fullCalendar({
				plugins: ['interaction', 'dayGrid', 'timeGrid', 'listWeek'],
				header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month,agendaWeek,agendaDay,listWeek'
				},
				defaultDate: Date(),
				navLinks: true, // can click day/week names to navigate views
				editable: false,
				businessHours: false,
				eventLimit: true,
				events: 'list_eventos.php',
				timeFormat: 'HH:mm',
				allDay: true,

				//retirada a regra
				//permite selecionar a grid do dia atual até o 3 dia para frente
				selectConstraint: {
					start: $.fullCalendar.moment().subtract(0, 'hour'),
					end: $.fullCalendar.moment().startOf('now').add(45, 'day')
				},

				// validRange: {
				// 	// start: new Date().toISOString().substring(0, 10)
				// 	start: nowDate()
				// },
				nextDayThreshold: '00:00:00',
				views: {
					settimana: {
						type: 'agendaWeek',
						duration: {
							days: 7
						},
						title: 'Apertura',
						columnFormat: 'dddd', // Format the day to only show like 'Monday'
						// hiddenDays: [0, 0] // Hide Sunday and Saturday?
					}
				},
				defaultView: 'settimana',

				eventLimit: true, // allow 'more' link when too many events

				eventClick: function(event) {
					$('#visualizar #id').text(event.id);
					$('#visualizar #id').val(event.id);
					$('#visualizar #title').text(event.title);
					$('#visualizar #title').val(event.title);
					$('#visualizar #labelstart').text(event.start.format('DD/MM/YYYY HH:mm'));
					$('#visualizar #labelstart').val(event.start.format('DD/MM/YYYY HH:mm'));
					$('#visualizar #labelEnd').text(event.end.format('DD/MM/YYYY HH:mm'));
					$('#visualizar #labelEnd').val(event.end.format('DD/MM/YYYY HH:mm'));
					$('#visualizar #color').val(event.color);
					$('#start').val(event.start.format('DD/MM/YYYY HH:mm'));
					$('#end').val(event.end.format('DD/MM/YYYY HH:mm'));
					$('#visualizar').modal('show');
					return false;
				},

				selectable: true,
				selectHelper: true,
				select: function(start, end) {
					$('#cadastrar #start').val(moment(start).format('DD/MM/YYYY HH:mm'));
					$('#cadastrar #end').val(moment(end).format('DD/MM/YYYY HH:mm'));
					$('#cadastrar').modal('show');
				},
				select: function(start1, end1) {
					$('#cadastrar #start1').val(moment(start1).format('DD/MM/YYYY HH:mm'));
					$('#cadastrar #end1').val(moment(end1).format('DD/MM/YYYY HH:mm'));
					$('#cadastrar').modal('show');
				},
				events: [
					<?php
					while ($row_events = mysqli_fetch_array($resultado_events)) {
					?> {
							id: '<?php echo $row_events['id']; ?>',
							title: '<?php echo $row_events['title']; ?>',
							start: '<?php echo $row_events['start']; ?>',
							end: '<?php echo $row_events['end']; ?>',
							color: '<?php echo $row_events['color']; ?>',
						},
					<?php
					}
					?>
				]
			});
		});

		//Mascara para o campo data e hora
		function DataHora(evento, objeto) {
			var keypress = (window.event) ? event.keyCode : evento.which;
			campo = eval(objeto);
			if (campo.value == '00/00/0000 00:00') {
				campo.value = ""
			}

			caracteres = '0123456789';
			separacao1 = '/';
			separacao2 = ' ';
			separacao3 = ':';
			conjunto1 = 2;
			conjunto2 = 5;
			conjunto3 = 10;
			conjunto4 = 13;
			conjunto5 = 16;
			if ((caracteres.search(String.fromCharCode(keypress)) != -1) && campo.value.length < (16)) {
				if (campo.value.length == conjunto1)
					campo.value = campo.value + separacao1;
				else if (campo.value.length == conjunto2)
					campo.value = campo.value + separacao1;
				else if (campo.value.length == conjunto3)
					campo.value = campo.value + separacao2;
				else if (campo.value.length == conjunto4)
					campo.value = campo.value + separacao3;
				else if (campo.value.length == conjunto5)
					campo.value = campo.value + separacao3;
			} else {
				event.returnValue = false;
			}
		}
	</script>
</head>

<body>
	<div class="container">

		<?php
		if (isset($_SESSION['msg'])) {
			echo $_SESSION['msg'];
			unset($_SESSION['msg']);
		}
		?>

		<div style="margin-right: right; text-align: right;" id="saudacao">
			<span class="border">
				<div style="text-align: center;" id="titulo">
					<p><b><h2>Gerenciador de Licenças Alura - UNJ (TJ)</h2></b></p>
				</div>
				<h4><?php echo "Bem-vindo " . $_SESSION['username'];?></h4>
			</span>
		</div>

		<div style="margin-right: right; text-align: right;" id="saudacao">
			<span class="border">				
				<h4><?php echo '<a href="doLogout.php?token=' . md5(session_id()) . '">Sair</a>';?></h4>				
			</span>
		</div><br>


		<div id='calendar'></div>
	</div>

	<div class="modal fade" id="visualizar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title text-center">Dados do Agendamento</h4>
				</div>
				<div class="modal-body">
					<div class="visualizar">
						<dl class="dl-horizontal">
							<!-- <dt>ID Agenda</dt>
										<dd id="id"></dd> -->
							<dt>Usuário</dt>
							<dd id="title"></dd>
							<dt>Início</dt>
							<dd id="labelstart"></dd>
							<dt>Fim</dt>
							<dd id="labelEnd"></dd>
						</dl>
						<button class="btn btn-canc-vis btn-warning">Editar</button>

					</div>
					<div class="form">
					<h6 class="modal-title text-left">
						<b>1.</b> É obrigatório que o nome completo no Alura seja igual ao usuário de rede.</br>
						<b>2.</b> Permitido apenas 3 agendamentos com até 2 horas cada.</br>
						<b>3.</b> O agendamento deve ser feito até 5 minutos antes do horário de início pretendido.</br></br>
					</h6>
						<form class="form-horizontal" method="POST" action="proc_edit_evento.php">
							<div class="form-group">
								<label for="inputEmail3" class="col-sm-2 control-label">Usuário</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="title" id="title" placeholder="nome de rede" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="inputEmail3" class="col-sm-2 control-label">Início</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="start" id="start" onKeyPress="DataHora(event, this)">
								</div>
							</div>
							<div class="form-group">
								<label for="inputEmail3" class="col-sm-2 control-label">Fim</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="end" id="end" onKeyPress="DataHora(event, this)">
								</div>
							</div>
							<input type="hidden" class="form-control" name="id" id="id">
							<div class="form-group">
								<div class="col-sm-offset-2 col-sm-10">
									<button type="button" class="btn btn-canc-edit btn-primary">Cancelar</button>
									<button type="submit" class="btn btn-warning" onClick="return ValidaTempo1()">Salvar Alterações</button>
									<script>
										function ValidaTempo1() {

											var data1 = '';
											var data2 = '';
											data1 = moment(document.getElementById('start').value, "DD/MM/YYYY hh:mm");
											data2 = moment(document.getElementById('end').value, "DD/MM/YYYY hh:mm");
											var diferenca = data2.diff(data1, 'minutes');

											if (data1 > data2){
												alert('A data final deve ser maior que a data inicial!');
												return false;
											}

											if (diferenca > 120) {
												alert('O tempo de agendamento não pode ser superior a 2 horas!');
												return false;
											}
											return true;
										}
									</script>
								</div>
							</div>
						</form>

						<form class="form-horizontal" method="POST" id="deletar" action="del_event.php">
							<input type="hidden" class="form-control" name="del_id" id="id">
							<button type="submit" class="btn btn-danger">Excluir</button>
						</form>

					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="cadastrar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title text-center">Agendamento de licença</h4></br>
					<h6 class="modal-title text-left">
						<b>1.</b> É obrigatório que o nome completo no Alura seja igual ao usuário de rede.</br>
						<b>2.</b> Permitido apenas 3 agendamentos com até 2 horas cada.</br>
						<b>3.</b> O agendamento deve ser feito até 5 minutos antes do horário de início pretendido.</br>

					</h6>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" method="POST" action="proc_cad_evento.php">
						<div class="form-group">
							<label for="inputEmail3" class="col-sm-2 control-label">Usuário</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="title" placeholder="nome de rede" value="<?php echo $_SESSION['username']; ?>" readonly />
							</div>
						</div>
						<div class="form-group">
							<label for="inputEmail3" class="col-sm-2 control-label">Início</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="start" id="start1" onKeyPress="DataHora(event, this)">
							</div>
						</div>

						<div class="form-group">
							<label for="inputEmail3" class="col-sm-2 control-label">Fim</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="end" id="end1" onKeyPress="DataHora(event, this)">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<button type="submit" class="btn btn-success" onclick="return ValidaTempo()">Agendar</button>
								<script>
									function ValidaTempo() {

										var data1 = moment(document.getElementById('start1').value, "DD/MM/YYYY hh:mm");
										var data2 = moment(document.getElementById('end1').value, "DD/MM/YYYY hh:mm");
										var diferenca = data2.diff(data1, 'minutes');

										if (data1 > data2){
												alert('A data final deve ser maior que a data inicial!');
												return false;
											}

										if (diferenca > 120) {
											alert('O tempo de agendamento não pode ser superior a 2 horas!');
											return false;
										}
										return true;
									}
								</script>

							</div>
						</div>
				</div>
				</form>
			</div>
		</div>
	</div>
	</div>


	<!-- Modal -->
	<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h3 class="modal-title" id="exampleModalLongTitle"  style="margin-right: center; text-align: center;"><b>FAQ Agenda Alura</b></h3>
		</div>
		<div class="modal-body">
		
		<p class="font-weight-normal"><b>Em quais dias e horários consigo agendar?</b></p>		
		<p class="font-italic">É permitido agendar para qualquer horário e dia da semana, inclusive aos finais de semana.</p>

		<p class="font-weight-normal"><b>São permitidos quantos agendamentos?</b></p>		
		<p class="font-italic">Você pode agendar no máximo 3 agendamentos para frente.</p>
		
		<p class="font-weight-normal"><b>Qual o tempo de cada agendamento?</b></p>		
		<p class="font-italic">Cada agendamento deve ter entre 30 minutos e 2 horas.</p>
		
		<p class="font-weight-normal"><b>Qual o usuário para logar no Agenda Cursos Alura?</b></p>		
		<p class="font-italic">A aplicação está integrada com o Active Directory da Softplan, portanto você poderá usar seu usuário e senha de rede.</p>
		
		<p class="font-weight-normal"><b>Os agendamentos de outras pessoas ficam visíveis?</b></p>		
		<p class="font-italic">Não. Apenas seus agendamentos ficam visíveis para você.</p>

		<p class="font-weight-normal"><b>Posso realizar o agendamento para qualquer conta na Alura?</b></p>		
		<p class="font-italic">Não. A conta na Alura precisa estar vinculado a sua conta da Softplan.</p>

		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
		</div>
		</div>
	</div>
	</div>


		<!-- Footer -->
	<footer class="page-footer font-small blue">
	<!-- Copyright -->
	<div class="footer-copyright text-center py-3" data-toggle="modal" data-target="#exampleModalLong">
	<a> 
		<h3><b>FAQ</b></h3>
	</a>
	</div>
	<!-- Copyright -->

	</footer>
	<!-- Footer -->



	<script>
		$('.btn-canc-vis').on("click", function() {
			$('.form').slideToggle();
			$('.visualizar').slideToggle();
		});
		$('.btn-canc-edit').on("click", function() {
			$('.visualizar').slideToggle();
			$('.form').slideToggle();
		});
		$('.btn-danger').on("click", function() {
			$('.deletar').slideToggle();
			$('.form').slideToggle();
		});
	</script>
</body>

</html>