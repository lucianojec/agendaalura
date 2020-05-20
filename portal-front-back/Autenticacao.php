<?php
/**
 * Created by Joe of ExchangeCore.com
 */
if(isset($_POST['username']) && isset($_POST['password']))
{

    $adServer = "SOFTPLAN.COM.BR";
    $ldap_porta = "389";
    $ldap = ldap_connect($adServer, $ldap_porta);
    $username = $_POST['username'];
    $password = $_POST['password'];
    $dominio = "@softplan.com.br"; //Dominio local ou global    
    $ldaprdn = $username.$dominio;

    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

    $bind = @ldap_bind($ldap, $ldaprdn, $password);

    if ($bind) 
    {
        $filter="(sAMAccountName=$username)";
    
        $result = ldap_search($ldap,"dc=SOFTPLAN,dc=COM",$filter);
        $info = ldap_get_entries($ldap, $result);

        echo "Autenticado com Sucesso!";
        session_start();
        $_SESSION["username"] = $username; 
        header("Location: index.php");
        @ldap_close($ldap);
    }
    else 
    {
        // echo "<div class='alert alert-danger' role='alert'>Usuário ou senha Inválido<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
        // 	header("Location: autenticacao.php");
        
        echo "<script>alert('Usuário e senha não correspondem.'); history.back();</script>";
    }
}
else
{
    ?>

        <!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login Agenda</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> 
        <style type="text/css">
            .login-form {
                width: 340px;
                margin: 50px auto;
            }
            .login-form form {
                margin-bottom: 15px;
                background: #f7f7f7;
                box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
                padding: 30px;
            }
            .login-form h2 {
                margin: 0 0 15px;
            }
            .form-control, .btn {
                min-height: 38px;
                border-radius: 2px;
            }
            .btn {        
                font-size: 15px;
                font-weight: bold;
            }
        </style>
        </head>
        <body>
            <div class="login-form">
                <form action="#" method="POST">
                    <h2 class="text-center">Login</h2>       
                    <div class="form-group">                        
                            <label for="username">Usuário: </label><input id="username" type="text" class="form-control" name="username" placeholder="Usuário de rede" required="required"/> 
                    </div>
                    <div class="form-group">                        
                        <label for="password">Senha: </label><input id="password" type="password" class="form-control" name="password" placeholder="Senha" required="required"/>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="submit" value="Submit" class="btn btn-primary btn-block">Acessar</button>
                    </div>
                    <div class="clearfix">
                        <label class="pull-left checkbox-inline"><input type="checkbox"> Remember me</label>                       
                    </div>        
                </form>                
            </div>
        </body>
        </html>                   

    <?php } ?> 