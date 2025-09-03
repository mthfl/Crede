<?php
require_once(__DIR__ . '/models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/config/connect.php');
$escola = $_SESSION['escola'];
$nome_completo_escola = strtolower($escola);
$nome_array = explode(' ', $nome_completo_escola);
$nome_escola_banco = $nome_array[1] . '_' . $nome_array[2];
new connect($nome_escola_banco);

require_once(__DIR__ . '/models/model.select.php');
$select = new select($nome_escola_banco);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <header>
        <nav>
            <ul>
                <?php if ($_SESSION['tipo_usuario'] == 'admin') { ?>
                    <li><a href="#">Resultados</a></li>
                <?php } ?>
                <li><a href="#">Relatórios</a></li>
                <?php if ($_SESSION['tipo_usuario'] == 'admin') { ?>
                    <li><a href="views/candidatos.php">Candidatos</a></li>
                <?php } ?>
                <?php if ($_SESSION['tipo_usuario'] == 'admin') { ?>
                    <li><a href="views/cursos.php">Cursos</a></li>

                <?php } ?>
                <?php if ($_SESSION['tipo_usuario'] == 'admin') { ?>
                    <li><a href="views/usuario.php">Usuarios</a></li>

                <?php } ?>
                <?php if ($_SESSION['tipo_usuario'] == 'admin') { ?>
                    <li><a href="#">Limpar banco</a></li>
                <?php } ?>
            </ul>
        </nav>
    </header>
    <main>


        <?php
        $dados = $select->select_cursos();
        if (count($dados) > 0) {
            foreach ($dados as $dado) {
        ?>
                <h1><?= $dado['nome_curso'] ?></h1>
                <h1><?= $dado['cor'] ?></h1>

            <?php }
        } else { ?>
            <p>nenhum curso cadastrado!</p>
        <?php } ?>
    </main>

    <footer>

    </footer>
</body>

</html>