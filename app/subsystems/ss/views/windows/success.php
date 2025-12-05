<?php
require_once(__DIR__ . '/../../models/sessions.php');
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once(__DIR__ . '/../../config/connect.php');
$escola = $_SESSION['escola'];

new connect($escola);
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
    :root {
      --primary: #005A24;
      --secondary: #FFA500;
      --dark: #1A3C34;
      --light: #F8FAF9;
      --gray: #666666;
      --light-gray: #f5f5f5;
    }

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
      background: linear-gradient(135deg, var(--primary) 0%, var(--dark) 100%);
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
      background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
      animation: backgroundMove 60s linear infinite;
      opacity: 0.4;
    }

    @keyframes backgroundMove {
      0% {
        background-position: 0 0;
      }

      100% {
        background-position: 500px 500px;
      }
    }

    .container {
      width: 100%;
      max-width: 600px;
      position: relative;
      z-index: 1;
      transform: translateY(0);
      animation: floatAnimation 6s ease-in-out infinite;
    }

    @keyframes floatAnimation {

      0%,
      100% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-15px);
      }
    }

    .success-card {
      background-color: var(--light);
      border-radius: 24px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      padding: 3.5rem 2.5rem;
      text-align: center;
      position: relative;
      overflow: hidden;
      transform: translateY(0);
      transition: all 0.3s ease;
      border: 1px solid rgba(255, 255, 255, 0.8);
    }

    .success-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.3);
    }

    .success-card::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 0) 70%);
      opacity: 0;
      transform: scale(0);
      transition: transform 0.8s ease, opacity 0.8s ease;
      pointer-events: none;
    }

    .success-card:hover::before {
      opacity: 0.2;
      transform: scale(1);
    }

    .success-icon {
      width: 110px;
      height: 110px;
      background: linear-gradient(135deg, var(--primary) 0%, var(--dark) 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 2.5rem;
      position: relative;
      animation: pulseAnimation 2s infinite;
      box-shadow: 0 15px 35px rgba(0, 90, 36, 0.3);
      border: 5px solid rgba(255, 255, 255, 0.7);
    }

    .success-icon i {
      color: var(--light);
      font-size: 50px;
      filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.2));
      animation: scaleAnimation 2s infinite alternate;
    }

    @keyframes scaleAnimation {
      0% {
        transform: scale(1);
      }

      100% {
        transform: scale(1.1);
      }
    }

    h1 {
      color: var(--dark);
      font-size: 2.4rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
      letter-spacing: 0.5px;
      line-height: 1.3;
      background: linear-gradient(to right, var(--primary), var(--dark));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .message {
      color: var(--gray);
      font-size: 1.2rem;
      margin-bottom: 2.5rem;
      line-height: 1.7;
      padding: 0 1rem;
    }

    .congratulations {
      display: block;
      color: var(--primary);
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .loading-indicator {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      color: var(--secondary);
      font-size: 1.1rem;
      font-weight: 600;
      background-color: rgba(255, 165, 0, 0.1);
      padding: 12px 20px;
      border-radius: 50px;
      margin: 0 auto;
      width: fit-content;
      box-shadow: 0 5px 15px rgba(255, 165, 0, 0.15);
      border: 1px solid rgba(255, 165, 0, 0.2);
      transition: all 0.3s ease;
    }

    .loading-indicator:hover {
      background-color: rgba(255, 165, 0, 0.15);
      box-shadow: 0 8px 20px rgba(255, 165, 0, 0.2);
    }

    .loading-dots {
      display: flex;
      gap: 6px;
    }

    .dot {
      width: 10px;
      height: 10px;
      background-color: var(--secondary);
      border-radius: 50%;
      animation: dotBounce 1.4s infinite ease-in-out;
    }

    .dot:nth-child(1) {
      animation-delay: 0s;
    }

    .dot:nth-child(2) {
      animation-delay: 0.2s;
    }

    .dot:nth-child(3) {
      animation-delay: 0.4s;
    }

    @keyframes dotBounce {

      0%,
      100% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-8px);
      }
    }

    @keyframes pulseAnimation {
      0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(0, 140, 69, 0.5);
      }

      70% {
        transform: scale(1.05);
        box-shadow: 0 0 0 20px rgba(0, 140, 69, 0);
      }

      100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(0, 140, 69, 0);
      }
    }

    /* Responsividade melhorada */
    @media (max-width: 640px) {
      .container {
        padding: 0 15px;
      }

      .success-card {
        padding: 2.5rem 1.5rem;
        border-radius: 20px;
      }

      h1 {
        font-size: 1.8rem;
      }

      .message {
        font-size: 1.1rem;
        padding: 0;
      }

      .success-icon {
        width: 90px;
        height: 90px;
        margin-bottom: 2rem;
      }

      .success-icon i {
        font-size: 40px;
      }

      .loading-indicator {
        padding: 10px 16px;
        font-size: 1rem;
      }
    }

    @media (max-width: 480px) {
      .success-card {
        padding: 2rem 1.2rem;
      }

      h1 {
        font-size: 1.6rem;
      }

      .message {
        font-size: 1rem;
        margin-bottom: 2rem;
      }

      .success-icon {
        width: 80px;
        height: 80px;
        margin-bottom: 1.5rem;
      }

      .success-icon i {
        font-size: 35px;
      }

      .loading-indicator {
        padding: 8px 14px;
        font-size: 0.9rem;
      }

      .dot {
        width: 8px;
        height: 8px;
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

      <?php if (isset($_GET['criado'])) { ?>
        <h1> <?= $_SESSION['candidato'] ?> </h1>
        <span class="congratulations">Confirmado!</span>
        <p class="message">
          Inscrito com sucesso
          <br>

        </p>
      <?php } else if (isset($_GET['ja_existe'])) { ?>
        <h1>Candidato já cadastrado!</h1>
        <span class="congratulations">O Candidato já foi cadastrado!</span>
        <p class="message">
          Já cadastrado.
          <br>

        </p>
      <?php } ?>
      <div class="loading-indicator">
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
    }, 4000);
  </script>
</body>

</html>