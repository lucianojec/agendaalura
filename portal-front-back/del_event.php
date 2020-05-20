<?php
session_start();
include_once './conexao.php';

$id = filter_input(INPUT_POST, 'del_id', FILTER_SANITIZE_NUMBER_INT);
if(!empty($id)){
    $qry_delete = "DELETE FROM events WHERE id='$id'";    
    $delete_event = $conn->prepare($qry_delete);

    if($delete_event->execute()){
        $_SESSION['msg'] = "<div class='alert alert-success' role='alert'>Evento excluído com sucesso!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
        header("Location: main.php");    
    
    }else{

        $_SESSION['msg'] = "<div class='alert alert-danger' role='alert'>Erro ao excluír evento!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
        header("Location: main.php");
    }
}else{  
    $_SESSION['msg'] = "<div class='alert alert-danger' role='alert'>É necessário selecionar um evento!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
    header("Location: main.php");
}

