<?php
require_once(__DIR__ . '/../config/connect_escolas.php');
require_once('../src/PHPMailer.php');
require_once('../src/SMTP.php');
require_once('../src/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class model_usuario extends connect_escolas
{
    private string $table5;


    function __construct($escola_banco)
    {
        parent::__construct($escola_banco);
        $table = require(__DIR__ . '/../../../.env/tables.php');

        $this->table5 = $table["ss_$escola_banco"][5];
    }

    public function pre_cadastro(string $cpf, string $email): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table5 WHERE senha IS NULL AND email = :email AND cpf = :cpf");
            $stmt_check->bindValue(":cpf", $cpf);
            $stmt_check->bindValue(":email", $email);
            $stmt_check->execute();

            $user = $stmt_check->fetch(PDO::FETCH_ASSOC);
            if ($user['status'] == 0) {
                return 4;
            }
            if ($stmt_check->rowCount() == 1) {

                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['email'] = $email;
                $_SESSION['cpf'] = $cpf;

                return 1;
            } else {

                return 3;
            }
        } catch (Exception $e) {

            error_log("Erro no login: " . $e->getMessage());
            return 0;
        }
    }
    public function primeiro_acesso($cpf, $email, $senha)
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table5 WHERE email = :email AND cpf = :cpf");
            $stmt_check->bindValue(":cpf", $cpf);
            $stmt_check->bindValue(":email", $email);
            $stmt_check->execute();
            $user = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($stmt_check->rowCount() > 0) {

                if ($user['status'] == 0) {
                    return 4;
                }
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt_check = $this->connect->prepare("UPDATE $this->table5 SET senha = :senha WHERE email = :email AND cpf = :cpf");
                $stmt_check->bindValue(":cpf", $cpf);
                $stmt_check->bindValue(":email", $email);
                $stmt_check->bindValue(":senha", $hash);

                if ($stmt_check->execute()) {

                    return 1;
                } else {
                    return 2;
                }
            } else {

                return 3;
            }
        } catch (Exception $e) {

            error_log("Erro no login: " . $e->getMessage());
            return 0;
        }
    }
    public function login(string $email, string $senha, string $escola): int
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table5 WHERE email = :email");
            $stmt_check->bindValue(':email', $email);
            $stmt_check->execute();

            $user = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if ($user['status'] == 0) {
                    return 4;
                }
                if (password_verify($senha, $user['senha'])) {

                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }

                    $_SESSION['id'] = $user['id'];
                    $_SESSION['nome'] = $user['nome_user'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['tipo_usuario'] = $user['tipo_usuario'];
                    $_SESSION['id_perfil'] = $user['id_perfil'];
                    $_SESSION['escola'] = $escola;
                    return 1;
                } else {
                    return 3;
                }
            } else {

                return 3;
            }
        } catch (Exception $e) {

            error_log("Erro no login: " . $e->getMessage());
            return 0;
        }
    }
    public function verificar_email(string $email, string $escola_banco):int
    {
        try {
            $stmt = $this->connect->prepare("SELECT * FROM $this->table5 WHERE email = :email AND status = 1");
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return 2;
            }

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['email_recuperar_senha'] = $email;
            $_SESSION['codigo'] = random_int(100000, 999999);

            $dados = require(__DIR__.'/../../../.env/config.php');
            $mail = new PHPMailer(true);
            try {
                // Configurações do servidor
                $mail->SMTPDebug = 0; // Desativar debug em produção (ou use SMTP::DEBUG_SERVER para testes locais)
                $mail->Debugoutput = function($str, $level) {
                    error_log("PHPMailer Debug [$level]: $str"); // Redirecionar debug para error_log
                };
                $mail->isSMTP();
                $mail->Host = $dados['emails']['host']; // e.g., smtp.hostinger.com
                $mail->SMTPAuth = true;
                $mail->Username = $dados['emails']['email']; // e.g., otavio.ce@salaberga.com
                $mail->Password = $dados['emails']['senha']; // Senha do e-mail
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = $dados['emails']['porta']; // e.g., 587
            
                // Destinatário e remetente
                $mail->setFrom($dados['emails']['email'], 'Sistema Salaberga');
                $mail->addAddress($email);
            
                // Conteúdo do e-mail
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = 'Recuperar Senha - Sistema Salaberga';
                $mail->Body = '
                <!DOCTYPE html>
                <html lang="pt-BR">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Recuperar Senha - Sistema Salaberga</title>
                    <style>
                        body, table, td, a, p, h1, h2 {
                            margin: 0;
                            padding: 0;
                            font-family: "Inter", sans-serif;
                        }
                        img { border: none; max-width: 100%; height: auto; display: block; }
                        a { text-decoration: none; }
                        table { border-collapse: collapse; width: 100%; }
                        .email-container {
                            max-width: 600px;
                            margin: 0 auto;
                            background-color: #F8FAF9;
                            border-radius: 16px;
                            overflow: hidden;
                            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
                        }
                        .header {
                            background: linear-gradient(135deg, #005A24 0%, #1A3C34 100%);
                            padding: 20px;
                            text-align: center;
                            color: #FFFFFF;
                        }
                        .header img { margin: 0 auto; width: 80px; }
                        .header h1 { font-family: "Poppins", sans-serif; font-size: 24px; font-weight: 600; margin-top: 10px; }
                        .content { padding: 30px 20px; background-color: #FFFFFF; text-align: center; }
                        .content h2 { font-family: "Poppins", sans-serif; font-size: 20px; color: #1A3C34; margin-bottom: 15px; }
                        .content p { font-size: 16px; color: #374151; line-height: 1.5; margin-bottom: 20px; }
                        .code-box { display: inline-block; background-color: #E6F4EA; color: #005A24; font-size: 24px; font-weight: 600; padding: 15px 30px; border-radius: 8px; margin: 20px 0; letter-spacing: 2px; }
                        .btn { display: inline-block; background: linear-gradient(135deg, #005A24 0%, #1A3C34 100%); color: #FFFFFF; font-family: "Poppins", sans-serif; font-size: 16px; font-weight: 600; padding: 12px 24px; border-radius: 8px; }
                        .btn:hover { background: linear-gradient(135deg, #1A3C34 0%, #005A24 100%); }
                        .footer { background-color: #F4F4F4; padding: 20px; text-align: center; font-size: 14px; color: #6B7280; }
                        .footer a { color: #FFA500; }
                        .footer a:hover { text-decoration: underline; }
                        @media only screen and (max-width: 600px) {
                            .email-container { width: 100%; border-radius: 0; }
                            .header h1 { font-size: 20px; }
                            .content h2 { font-size: 18px; }
                            .content p { font-size: 14px; }
                            .code-box { font-size: 20px; padding: 10px 20px; }
                            .btn { font-size: 14px; padding: 10px 20px; }
                        }
                    </style>
                </head>
                <body>
                    <table role="presentation" class="email-container">
                        <tr>
                            <td class="header">
                                <img src="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png" alt="Logo Salaberga">
                                <h1 style="color: #FFFFFF;">Sistema de Seleção</h1>
                            </td>
                        </tr>
                        <tr>
                            <td class="content">
                                <h2>Recuperação de Senha</h2>
                                <p>Olá, você solicitou a recuperação de senha para o Sistema Salaberga. Use o código abaixo para redefinir sua senha:</p>
                                <div class="code-box">' . htmlspecialchars($_SESSION['codigo'], ENT_QUOTES, 'UTF-8') . '</div>
                                <p>Este código é válido por 10 minutos. Clique no botão abaixo para redefinir sua senha:</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="footer">
                                <p>Coordenadoria Regional de Desenvolvimento da Educação<br>
                                <a href="mailto:suporte@salaberga.com">suporte@salaberga.com</a> | <a href="https://salaberga.com">salaberga.com</a></p>
                                <p style="margin-top: 10px;">Se você não solicitou esta recuperação, ignore este e-mail.</p>
                            </td>
                        </tr>
                    </table>
                </body>
                </html>';
            
                $mail->AltBody = "Olá,\n\nVocê solicitou a recuperação de senha para o Sistema Salaberga. Use o código abaixo para redefinir sua senha:\n\n" . $_SESSION['codigo'] . "\n\nEste código é válido por 10 minutos.";
            
                // Enviar e-mail
                $mail->send();
                error_log("E-mail enviado com sucesso para $email em " . date('Y-m-d H:i:s'));
                $_SESSION['email_success'] = 'E-mail de recuperação enviado com sucesso!';
                return 1;
            } catch (Exception $e) {
                error_log("Erro ao enviar e-mail para $email: {$mail->ErrorInfo} em " . date('Y-m-d H:i:s'));
                $_SESSION['email_error'] = 'Erro ao enviar o e-mail de recuperação. Tente novamente.';
                return 0;
            }
        } catch (Exception $e) {
            error_log("Erro no login: " . $e->getMessage());
            return 0;
        }
    }
    public function alterar_senha($email, $senha)
    {
        try {
            $stmt_check = $this->connect->prepare("SELECT * FROM $this->table5 WHERE email = :email");
            $stmt_check->bindValue(":email", $email);
            $stmt_check->execute();
            $user = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($stmt_check->rowCount() > 0) {

                if ($user['status'] == 0) {
                    return 4;
                }
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt_check = $this->connect->prepare("UPDATE $this->table5 SET senha = :senha WHERE email = :email");
                $stmt_check->bindValue(":email", $email);
                $stmt_check->bindValue(":senha", $hash);

                if ($stmt_check->execute()) {

                    return 1;
                } else {
                    return 2;
                }
            } else {

                return 3;
            }
        } catch (Exception $e) {

            error_log("Erro no login: " . $e->getMessage());
            return 0;
        }
    }
}
