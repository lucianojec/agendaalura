<?php

    $ldap_server = "SOFTPLAN.COM.BR";
    $dominio = "@softplan.com.br"; //Dominio local ou global
    $user = 'luciano.fagundes'.$dominio;
    $ldap_porta = "389";
    $ldap_pass   = 'Pedrojuliana/8784';
    $ldapcon = ldap_connect($ldap_server, $ldap_porta) or die("Could not connect to LDAP server.");

    if ($ldapcon){

    // binding to ldap server
    //$ldapbind = ldap_bind($ldapconn, $user, $ldap_pass);

    $bind = ldap_bind($ldapcon, $user, $ldap_pass);

    // verify binding
    if ($bind) {
    echo "LDAP bind successful…";
    } else {
    echo "LDAP bind failed…";
    }

    }

?>