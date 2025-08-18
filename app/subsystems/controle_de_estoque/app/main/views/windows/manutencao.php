<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="content-language" content="pt-BR">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#005A24">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title>Sistema em Manutenção - CREDE</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png">

    <!-- Fontes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#005A24',
                        secondary: '#FFA500',
                        accent: '#E6F4EA',
                        dark: '#1A3C34',
                        light: '#F8FAF9',
                        white: '#FFFFFF',
                        gray: {
                            50: '#F9FAFB',
                            100: '#F3F4F6',
                            200: '#E5E7EB',
                            300: '#D1D5DB',
                            400: '#9CA3AF',
                            500: '#6B7280',
                            600: '#4B5563',
                            700: '#374151',
                            800: '#1F2937',
                            900: '#111827'
                        }
                    },
                    backgroundImage: {
                        'gradient-primary': 'linear-gradient(135deg, #005A24 0%, #7FB069 50%, #1A3C34 100%)',
                        'gradient-secondary': 'linear-gradient(135deg, #F4A261 0%, #E76F51 100%)',
                        'gradient-light': 'linear-gradient(135deg, #E8F4F8 0%, #F7F3E9 100%)',
                        'gradient-dark': 'linear-gradient(135deg, #2D5016 0%, #005A24 100%)'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Poppins', 'sans-serif']
                    },
                    boxShadow: {
                        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                        'medium': '0 4px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
                        'strong': '0 10px 40px -10px rgba(0, 0, 0, 0.15), 0 2px 10px -2px rgba(0, 0, 0, 0.05)',
                        'primary': '0 10px 25px -5px rgba(0, 90, 36, 0.3)',
                        'secondary': '0 10px 25px -5px rgba(255, 165, 0, 0.3)'
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'sway': 'sway 4s ease-in-out infinite',
                        'pulse-glow': 'pulse-glow 2s ease-in-out infinite',
                        'fadeInUp': 'fadeInUp 0.8s ease-out',
                        'rotate': 'rotate 3s linear infinite',
                        'bounce-gentle': 'bounce-gentle 2s ease-in-out infinite'
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        @keyframes sway {
            0%, 100% {
                transform: translateX(0px) rotate(0deg);
            }
            50% {
                transform: translateX(10px) rotate(2deg);
            }
        }

        @keyframes pulse-glow {
            0%, 100% {
                box-shadow: 0 0 20px rgba(255, 165, 0, 0.4);
            }
            50% {
                box-shadow: 0 0 30px rgba(255, 165, 0, 0.6);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes bounce-gentle {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .floating-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
            overflow: hidden;
        }

        .leaf {
            position: absolute;
            width: 20px;
            height: 20px;
            background: #005A24;
            border-radius: 0 100% 0 100%;
            animation: float 15s linear infinite;
            opacity: 0.6;
        }

        .leaf:nth-child(2n) {
            background: #FFA500;
            animation-duration: 20s;
            animation-delay: -5s;
        }

        .maintenance-card {
            animation: fadeInUp 0.8s ease-out;
        }

        .maintenance-icon {
            animation: rotate 3s linear infinite;
        }

        .progress-bar {
            animation: bounce-gentle 2s ease-in-out infinite;
        }

        .pulse-on-hover:hover {
            animation: pulse-glow 1s ease-in-out;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Elementos flutuantes decorativos -->
    <div class="floating-elements">
        <div class="leaf" style="top: 10%; left: 10%;"></div>
        <div class="leaf" style="top: 20%; right: 15%;"></div>
        <div class="leaf" style="top: 60%; left: 5%;"></div>
        <div class="leaf" style="top: 80%; right: 10%;"></div>
        <div class="leaf" style="top: 30%; left: 80%;"></div>
    </div>

    <!-- Container principal -->
    <div class="min-h-screen flex items-center justify-center p-4 relative z-10">
        <div class="max-w-lg w-full">
            <!-- Card de manutenção -->
            <div class="maintenance-card bg-white rounded-2xl shadow-strong p-8 border border-gray-200">
                <!-- Ícone de manutenção -->
                <div class="text-center mb-6">
                    <div class="maintenance-icon inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-secondary to-orange-600 rounded-full mb-4">
                        <i class="fas fa-tools text-white text-4xl"></i>
                    </div>
                </div>

                <!-- Título -->
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-heading font-bold text-gray-800 mb-3">
                        Sistema em Manutenção
                    </h1>
                    <p class="text-gray-600 text-base">
                        Estamos trabalhando para melhorar sua experiência
                    </p>
                </div>

                <!-- Barra de progresso animada -->
                <div class="mb-6">
                    <div class="bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="progress-bar bg-gradient-to-r from-secondary to-orange-500 h-3 rounded-full" style="width: 75%;"></div>
                    </div>
                    <p class="text-center text-gray-500 text-sm mt-2">
                        Manutenção em andamento...
                    </p>
                </div>

                <!-- Mensagem informativa -->
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-orange-500 mt-1 mr-3 text-lg"></i>
                        <div>
                            <h3 class="text-orange-800 font-medium text-sm mb-2">
                                Atualização em Progresso
                            </h3>
                            <p class="text-orange-700 text-sm leading-relaxed">
                                O sistema CREDE está passando por uma manutenção programada para 
                                implementar melhorias e correções. Agradecemos sua paciência.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Informações adicionais -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                    <h4 class="text-gray-700 font-medium text-sm mb-3">
                        <i class="fas fa-clock text-primary mr-2"></i>
                        Tempo Estimado
                    </h4>
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="bg-white rounded-lg p-3 border border-gray-200">
                            <div class="text-2xl font-bold text-primary">2-3</div>
                            <div class="text-xs text-gray-600">Horas</div>
                        </div>
                        <div class="bg-white rounded-lg p-3 border border-gray-200">
                            <div class="text-2xl font-bold text-secondary">75%</div>
                            <div class="text-xs text-gray-600">Concluído</div>
                        </div>
                    </div>
                </div>

                <!-- Botões de ação -->
                <div class="space-y-3">
                    <button onclick="window.location.reload()" 
                            class="pulse-on-hover w-full bg-gradient-to-r from-primary to-dark text-white font-medium py-3 px-4 rounded-lg hover:shadow-primary transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Verificar Status
                    </button>
                    
                    <button onclick="window.history.back()" 
                            class="w-full bg-gray-100 text-gray-700 font-medium py-3 px-4 rounded-lg hover:bg-gray-200 transition-all duration-300">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Voltar
                    </button>
                </div>

                <!-- Footer -->
                <div class="text-center mt-6 pt-6 border-t border-gray-200">
                    <p class="text-gray-500 text-xs">
                        Sistema CREDE - Coordenadoria Regional de Desenvolvimento da Educação
                    </p>
                    <p class="text-gray-400 text-xs mt-1">
                        © 2024 Governo do Estado do Ceará
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para auto-refresh -->
    <script>
        // Auto-refresh a cada 2 minutos
        setTimeout(function() {
            window.location.reload();
        }, 120000);

        // Adicionar efeito de pulse ao clicar no botão
        document.querySelectorAll('.pulse-on-hover').forEach(button => {
            button.addEventListener('click', function() {
                this.style.animation = 'pulse-glow 1s ease-in-out';
                setTimeout(() => {
                    this.style.animation = '';
                }, 1000);
            });
        });

        // Atualizar barra de progresso
        let progress = 75;
        const progressBar = document.querySelector('.progress-bar');
        const progressText = document.querySelector('.text-2xl.font-bold.text-secondary');
        
        setInterval(() => {
            progress += Math.random() * 2;
            if (progress > 95) progress = 95;
            progressBar.style.width = progress + '%';
            progressText.textContent = Math.round(progress) + '%';
        }, 5000);
    </script>
</body>

</html>
