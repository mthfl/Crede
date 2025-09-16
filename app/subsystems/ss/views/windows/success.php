<?php
require_once(__DIR__ . '/../../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../../config/connect.php');
$escola = $_SESSION['escola'];

$conexao = new connect($escola);
$conn = $GLOBALS['conn']; // Garantir que a variável $conn está disponível

// Obter a cor do curso
$curso_cor = '#005A24'; // Cor padrão

// Verificar se o curso_id está disponível na URL ou na sessão
$curso_id = isset($_GET['curso_id']) ? $_GET['curso_id'] : null;

// Se não tiver na URL, tenta pegar da sessão
if (!$curso_id && isset($_SESSION['curso_id'])) {
    $curso_id = $_SESSION['curso_id'];
}

// Remover código de debug que estava causando erro
// Não vamos mais usar logs para debug

if ($curso_id) {
    // Consultar a cor do curso no banco de dados
    $sql = "SELECT cor_curso FROM cursos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $curso_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $curso_cor = $row['cor_curso'];
    }
}

// Função para converter hex para rgba
function hex2rgba($color, $opacity = false) {
    $default = 'rgb(0,0,0)';
    
    if(empty($color))
        return $default; 
    
    if ($color[0] == '#')
        $color = substr($color, 1);
    
    if (strlen($color) == 6)
        $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
    elseif (strlen($color) == 3)
        $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
    else
        return $default;
    
    $rgb = array_map('hexdec', $hex);
    
    if($opacity){
        if(abs($opacity) > 1)
            $opacity = 1.0;
        $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
    } else {
        $output = 'rgb('.implode(",",$rgb).')';
    }
    
    return $output;
}

// Definir cores com base na cor do curso
$curso_cor_light = hex2rgba($curso_cor, 0.8);
$curso_cor_dark = '#1A3C34'; // Cor escura padrão para o gradiente
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Sistema SEEPS - Confirmação de Inscrição">
  <meta name="author" content="SEEPS">

  <title>Saida - Confirmação</title>

  <link rel="shortcut icon" href="../assets/images/icone_salaberga.png" type="image">
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Raleway', sans-serif;
    }

    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      background: linear-gradient(135deg, <?= $curso_cor ?> 0%, <?= $curso_cor_dark ?> 100%);
      position: relative;
      overflow: hidden;
    }

    body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(45deg,
          rgba(255, 255, 255, 0.1) 25%,
          transparent 25%,
          transparent 50%,
          rgba(255, 255, 255, 0.1) 50%,
          rgba(255, 255, 255, 0.1) 75%,
          transparent 75%,
          transparent);
      background-size: 100px 100px;
      animation: backgroundMove 30s linear infinite;
      opacity: 0.3;
    }

    @keyframes backgroundMove {
      0% {
        background-position: 0 0;
      }

      100% {
        background-position: 100px 100px;
      }
    }

    .container {
      width: 100%;
      max-width: 600px;
      position: relative;
      z-index: 1;
    }

    .success-card {
      background-color: #FFFFFF;
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
      padding: 3rem 2rem;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .success-icon {
      width: 90px;
      height: 90px;
      background: <?= $curso_cor ?>;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 2rem;
      position: relative;
      animation: pulseAnimation 2s infinite;
    }

    .success-icon i {
      color: #FFFFFF;
      font-size: 40px;
    }

    h1 {
      color: #333;
      font-size: 2.2rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
      letter-spacing: 0.5px;
    }


    .message {
      color: #666;
      font-size: 1.1rem;
      margin-bottom: 2rem;
      line-height: 1.6;
    }

    .congratulations {
      display: block;
      color: <?= $curso_cor ?>;
      font-size: 1.4rem;
      font-weight: 700;
      margin-bottom: -15px;

    }

    .loading-indicator {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      color: <?= $curso_cor ?>;
      font-size: 1rem;
      font-weight: 500;
    }

    .loading-dots {
      display: flex;
      gap: 5px;
    }

    .dot {
      width: 8px;
      height: 8px;
      background-color: <?= $curso_cor ?>;
      border-radius: 50%;
      animation: dotAnimation 1.4s infinite;
    }

    .dot:nth-child(2) {
      animation-delay: 0.2s;
    }

    .dot:nth-child(3) {
      animation-delay: 0.4s;
    }

    @keyframes pulseAnimation {
      0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 <?= hex2rgba($curso_cor, 0.4) ?>;
      }

      70% {
        transform: scale(1.05);
        box-shadow: 0 0 0 15px <?= hex2rgba($curso_cor, 0) ?>;
      }

      100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 <?= hex2rgba($curso_cor, 0) ?>;
      }
    }

    @keyframes dotAnimation {

      0%,
      100% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-5px);
      }
    }

    @media (max-width: 480px) {
      .success-card {
        padding: 2rem 1.5rem;
      }

      h1 {
        font-size: 1.8rem;
      }

      .success-icon {
        width: 70px;
        height: 70px;
      }

      .success-icon i {
        font-size: 30px;
      }
    }
  </style>
</head>

<body>
  <div class="container text-center">
    <div class="success-card">
      <div class="success-icon">
        <i class="fas fa-check"></i>
      </div>
      <h1>Aluno(a) <?=$_SESSION['candidato'] ?> saiu com sucesso!</h1>
      <p class="message">
        
        <br>
        Sua saida foi processada e confirmada em nosso sistema.
      </p>
      <div class="loading-indicator" style="margin-left: 30px">
        Redirecionando
        <div class="loading-dots">
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Redireciona a página após 2 segundos
    setTimeout(function() {
      window.location.href = "../../index.php";
    }, 2000000);
</script>
</body>

</html>