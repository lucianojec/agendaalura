<?php
session_start();

//Incluir conexao com BD
include_once("conexao.php");

	$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
	$color = filter_input(INPUT_POST, 'color', FILTER_SANITIZE_STRING);
	$start = filter_input(INPUT_POST, 'start', FILTER_SANITIZE_STRING);
	$end = filter_input(INPUT_POST, 'end', FILTER_SANITIZE_STRING);

	$color = "40E0D0";

	$data = explode(" ", $start);
	list($date, $hora) = $data;
	$data_sem_barra = array_reverse(explode("/", $date));
	$data_sem_barra = implode("-", $data_sem_barra);
	$start_sem_barra = $data_sem_barra . " " . $hora;
	
	$data = explode(" ", $end);
	list($date, $hora) = $data;
	$data_sem_barra = array_reverse(explode("/", $date));
	$data_sem_barra = implode("-", $data_sem_barra);
	$end_sem_barra = $data_sem_barra . " " . $hora;

	$query_events_future = "SELECT count(title) as events_future FROM events where `start` >= now() and `title` = '" . $_SESSION['username'] . "' ";
	$sql_events_future = mysqli_query($conn, $query_events_future);
	$row_events_future = mysqli_fetch_assoc($sql_events_future);
	$row_events_future = $row_events_future['events_future'];

	$queryPrimeiraMeiaHora = "SELECT COUNT(*) as quantidade from events WHERE (`start` <= '$start_sem_barra' AND `end` >= date_add('$start_sem_barra', interval 30 MINUTE))";
	$querySegundaMeiaHora = "SELECT COUNT(*) as quantidade from events WHERE (`start` <= date_add('$start_sem_barra', interval 30 MINUTE) AND `end` >= date_add('$start_sem_barra', interval 60 MINUTE))";
	$queryTerceiraMeiaHora = "SELECT COUNT(*) as quantidade from events WHERE (`start` <= date_add('$start_sem_barra', interval 60 MINUTE) AND `end` >= date_add('$start_sem_barra', interval 90 MINUTE))";
	$queryQuartaMeiaHora = "SELECT COUNT(*) as quantidade from events WHERE (`start` <= date_add('$start_sem_barra', interval 90 MINUTE) AND `end` >= date_add('$start_sem_barra', interval 120 MINUTE))";
	$sql = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($sql);
	$row = $row['quantidade'];

	function quantidadeDeMeiaHora($startdate,$enddate){
		$date = new DateTime( $startdate );
		$date2 = new DateTime( $enddate );

		$diff = ($date2->getTimestamp() - $date->getTimestamp())/1800;
		return $diff;
	}
	$quantidadeDeLicencas = 15;

	$temMenosDe3AgendamentosFuturos = intval($row_events_future) < 3;
	if($temMenosDe3AgendamentosFuturos)		
	{
		$possuiLicencaDisponivel = true;

		$quantidadeDeMeiaHora = quantidadeDeMeiaHora($start_sem_barra, $end_sem_barra);
		
		$licencasUsadasNaPrimeiraMeiaHora = mysqli_fetch_assoc(mysqli_query($conn, $queryPrimeiraMeiaHora))['quantidade'];
		if($licencasUsadasNaPrimeiraMeiaHora >= $quantidadeDeLicencas){
			$possuiLicencaDisponivel = false;
		}
		if ($quantidadeDeMeiaHora >= 2 && $possuiLicencaDisponivel) {
			$licencasUsadasNaSegundaMeiaHora = mysqli_fetch_assoc(mysqli_query($conn, $querySegundaMeiaHora))['quantidade'];
			if($licencasUsadasNaSegundaMeiaHora >= $quantidadeDeLicencas){
				$possuiLicencaDisponivel = false;
			}
		}
		if ($quantidadeDeMeiaHora >= 3 && $possuiLicencaDisponivel) {
			$licencasUsadasNaTerceiraMeiaHora = mysqli_fetch_assoc(mysqli_query($conn, $queryTerceiraMeiaHora))['quantidade'];
			if($licencasUsadasNaTerceiraMeiaHora >= $quantidadeDeLicencas){
				$possuiLicencaDisponivel = false;
			}
		}
		if ($quantidadeDeMeiaHora == 4 && $possuiLicencaDisponivel) {
			$licencasUsadasNaQuartaMeiaHora = mysqli_fetch_assoc(mysqli_query($conn, $queryQuartaMeiaHora))['quantidade'];
			if($licencasUsadasNaQuartaMeiaHora >= $quantidadeDeLicencas){
				$possuiLicencaDisponivel = false;
			}
		}
		
		if(!$possuiLicencaDisponivel) 
		{   
			$_SESSION['msg'] = "<div class='alert alert-danger' role='alert'>Para esse horário requerido não há licença disponível. Tente outro horário.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";   
			header("Location: main.php");
			
		}
		elseif(!empty($title) && !empty($color) && !empty($start) && !empty($end))
		{
			$result_events = "INSERT INTO events (title, color, start, end) VALUES ('$title', '$color', '$start_sem_barra', '$end_sem_barra')";
			$resultado_events = mysqli_query($conn, $result_events);		
			if(mysqli_insert_id($conn))
			{
				$_SESSION['msg'] = "<div class='alert alert-success' role='alert'>Agendamento realizado com sucesso!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";		
				header("Location: main.php");
			}
			else
			{
				$_SESSION['msg'] = "<div class='alert alert-danger' role='alert'>Erro ao cadastrar o agendamento!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";		
				header("Location: main.php");
			}	
		}
	}
	else
	{
		$_SESSION['msg'] = "<div class='alert alert-danger' role='alert'>Você já possui 3 agendamentos futuros!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
		header("Location: main.php");
	}
	
?>