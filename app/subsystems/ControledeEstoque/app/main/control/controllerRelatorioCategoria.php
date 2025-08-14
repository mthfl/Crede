<?php
require("../model/model.functions.php");
$x = new relatorios();
if(isset($_POST['categoria'])){ 
    $x->relatorioPorCategoria($_POST['categoria']);
}else{
    header('Location: ../views/relatorios.php?erro=1');
    exit;
}
?>