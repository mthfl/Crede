<?php
require_once __DIR__ . "/../models/sessions.php";
$session = new sessions();
$session->autenticar_session();
$session->tempo_session();

require_once __DIR__ . "/../config/connect.php";
if (!isset($_SESSION["escola"]) || empty($_SESSION["escola"])) {
    die("Escola não definida na sessão");
}
$escola = $_SESSION["escola"];

new connect($escola);
require_once __DIR__ . "/../models/model.select.php";
$select = new select($escola);
?>
<!DOCTYPE html>
<html lang="pt-BR" style="height: auto; overflow-y: auto;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Sistema Escolar - Relatórios</title>
    <link rel="icon" type="image/png" href="https://i.postimg.cc/0N0dsxrM/Bras-o-do-Cear-svg-removebg-preview.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
                        success: '#28A745',
                        warning: '#FFC107',
                        danger: '#DC3545',
                        info: '#17A2B8'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Poppins', 'sans-serif'],
                        display: ['Inter', 'system-ui', 'sans-serif'],
                        body: ['Inter', 'system-ui', 'sans-serif']
                    },
                    boxShadow: {
                        card: '0 10px 15px -3px rgba(0, 90, 36, 0.1), 0 4px 6px -2px rgba(0, 90, 36, 0.05)',
                        'card-hover': '0 20px 25px -5px rgba(0, 90, 36, 0.2), 0 10px 10px -5px rgba(0, 90, 36, 0.1)',
                        'glow': '0 0 20px rgba(255, 165, 0, 0.3)'
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'pulse-slow': 'pulse 3s infinite',
                        'bounce-slow': 'bounce 2s infinite',
                        'slide-in-left': 'slideInLeft 0.5s ease-out',
                        'slide-in-right': 'slideInRight 0.5s ease-out',
                        'fade-in-up': 'fadeInUp 0.6s ease-out',
                        'scale-in': 'scaleIn 0.4s ease-out',
                        'pulse-soft': 'pulseSoft 2s ease-in-out infinite'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        },
                        slideInLeft: {
                            from: { opacity: '0', transform: 'translateX(-30px)' },
                            to: { opacity: '1', transform: 'translateX(0)' }
                        },
                        slideInRight: {
                            from: { opacity: '0', transform: 'translateX(30px)' },
                            to: { opacity: '1', transform: 'translateX(0)' }
                        },
                        fadeInUp: {
                            from: { opacity: '0', transform: 'translateY(20px)' },
                            to: { opacity: '1', transform: 'translateY(0)' }
                        },
                        scaleIn: {
                            from: { opacity: '0', transform: 'scale(0.95)' },
                            to: { opacity: '1', transform: 'scale(1)' }
                        },
                        pulseSoft: {
                            '0%, 100%': { opacity: '1' },
                            '50%': { opacity: '0.8' }
                        }
                    },
                    spacing: {
                        '18': '4.5rem',
                        '88': '22rem'
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        :root {
            --primary: #005A24;
            --secondary: #FFA500;
            --accent: #E6F4EA;
            --dark: #1A3C34;
            --light: #F8FAF9;
        }

        * {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
            background: linear-gradient(135deg, #F8FAF9 0%, #E6F4EA 100%);
            height: auto;
            min-height: 100%;
            overflow-y: auto;
            position: relative;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #005A24 0%, #1A3C34 100%);
        }

        .page-title {
            position: relative;
            width: 100%;
            text-align: center;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #FFA500, #FF8C00);
            border-radius: 3px;
        }

        /* Select2 Styles */
        .select2-container {
            width: 100% !important;
            font-family: 'Inter', sans-serif;
        }

        /* Ajuste do botão de limpar (x) do Select2 */
        .select2-selection__clear {
            margin-left: 0.5rem !important;
            position: relative;
            left: 0.2rem;
        }

        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            height: 3rem;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #374151;
            font-size: 0.875rem;
            line-height: 1.25rem;
            padding-left: 0.75rem;
            padding-right: 2rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af;
            font-weight: 500;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            width: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #005A24;
            box-shadow: 0 0 0 3px rgba(0, 90, 36, 0.1);
        }

        .select2-dropdown {
            border: 2px solid #005A24;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .select2-results__option {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #005A24;
            color: white;
        }

        .select2-search--dropdown .select2-search__field {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.5rem;
            font-size: 0.875rem;
        }

        .select2-search--dropdown .select2-search__field:focus {
            outline: none;
            border-color: #005A24;
            box-shadow: 0 0 0 3px rgba(0, 90, 36, 0.1);
        }

        .stats-card {
            transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1); /* Reduced from 0.3s to 0.2s */
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #FFFFFF 0%, #F8FAF9 100%);
            border: 2px solid transparent;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 165, 0, 0.1) 0%, rgba(0, 90, 36, 0.05) 100%);
            opacity: 0;
            transition: opacity 0.2s ease; /* Reduced from 0.3s to 0.2s */
            z-index: 1;
        }

        .stats-card:hover::before {
            opacity: 1;
        }

        .stats-card:hover {
            transform: translateY(-3px) scale(1.01); /* Reduced from translateY(-5px) and added slight scale */
            box-shadow: 0 10px 15px -3px rgba(0, 90, 36, 0.15), 0 5px 5px -2px rgba(0, 90, 36, 0.08); /* Smaller shadow */
            border-color: #FFA500;
        }

        .report-card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #FFFFFF 0%, #F8FAF9 100%);
            border: 2px solid #E6F4EA;
            border-radius: 1rem;
        }

        .report-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 165, 0, 0.1) 0%, rgba(0, 90, 36, 0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }

        .report-card:hover::before {
            opacity: 1;
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 90, 36, 0.2), 0 10px 10px -5px rgba(0, 90, 36, 0.1);
            border-color: #FFA500;
        }

        .card-shine {
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0) 100%);
            transform: skewX(-25deg);
            transition: all 0.75s ease;
            z-index: 2;
        }

        .report-card:hover .card-shine {
            left: 150%;
        }

        .report-card:hover .card-icon {
            transform: scale(1.1);
            color: #FFA500;
        }

        .report-card p {
            z-index: 2;
            position: relative;
            transition: color 0.3s ease;
        }

        .report-card:hover p {
            color: #005A24;
        }

        .report-card a,
        .report-card button {
            position: relative;
            z-index: 3;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: linear-gradient(135deg, #FFFFFF 0%, #F8FAF9 100%);
            padding: 2rem;
            border-radius: 1rem;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 25px 50px -12px rgba(0, 90, 36, 0.25);
            animation: slideUp 0.5s ease-out;
            position: relative;
            border: 2px solid #E6F4EA;
        }

        .modal-content h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            color: #005A24;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .modal-content .form-group {
            margin-bottom: 1.5rem;
        }

        .modal-content label {
            display: block;
            font-size: 0.875rem;
            color: #1A3C34;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .modal-content select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #E6F4EA;
            border-radius: 0.5rem;
            font-size: 1rem;
            color: #1A3C34;
            background-color: #F8FAF9;
            transition: all 0.3s ease;
        }

        .modal-content select:focus {
            border-color: #FFA500;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 165, 0, 0.1);
        }

        .modal-content .confirm-btn {
            background: linear-gradient(135deg, #FFA500 0%, #FF8C00 100%);
            color: #FFFFFF;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .modal-content .confirm-btn:hover {
            background: linear-gradient(135deg, #E59400 0%, #E67E00 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 165, 0, 0.3);
        }

        .modal-content .close-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #1A3C34;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .modal-content .close-btn:hover {
            color: #DC3545;
        }

        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            backdrop-filter: blur(10px);
            background: linear-gradient(135deg, var(--primary) 0%, var(--dark) 100%);
            z-index: 50;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .overlay {
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            backdrop-filter: blur(2px);
        }

        .overlay.show {
            opacity: 1;
            visibility: visible;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes pulseSoft {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            overflow: hidden;
        }

        .card-hover::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }

        .card-hover:hover::before {
            left: 100%;
        }

        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 90, 36, 0.25), 0 0 0 1px rgba(0, 90, 36, 0.05);
        }

        .btn-animate {
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .btn-animate::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s, height 0.3s;
        }

        .btn-animate:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-animate:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-animate:active {
            transform: translateY(0);
        }

        .nav-item {
            position: relative;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border-radius: 12px;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--secondary);
            border-radius: 0 4px 4px 0;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .nav-item:hover::before {
            transform: scaleY(1);
        }

        .nav-item:hover {
            transform: translateX(8px);
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar {
            z-index: 50;
        }

        .overlay {
            z-index: 45;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 100vw;
            max-width: 20rem;
            z-index: 50;
        }

        @media (min-width: 1024px) {
            .sidebar {
                width: 20rem;
                position: static;
                flex-shrink: 0;
            }

            .main-content {
                flex: 1;
                min-width: 0;
                margin-left: 0;
                overflow-x: hidden;
            }

            body {
                overflow-x: hidden;
            }
        }

        .grid-item {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }

        .grid-item:nth-child(1) {
            animation-delay: 0.1s;
        }

        .grid-item:nth-child(2) {
            animation-delay: 0.2s;
        }

        .grid-item:nth-child(3) {
            animation-delay: 0.3s;
        }

        .grid-item:nth-child(4) {
            animation-delay: 0.4s;
        }

        .grid-item:nth-child(5) {
            animation-delay: 0.5s;
        }

        .grid-item:nth-child(6) {
            animation-delay: 0.6s;
        }

        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100vw;
                max-width: 320px;
            }

            .card-hover:hover {
                transform: none;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }

            .nav-item:hover {
                transform: none;
            }

            .main-content {
                height: auto;
                min-height: 100vh;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch;
            }

            html, body {
                height: auto;
                overflow-y: auto !important;
                position: relative;
                -webkit-overflow-scrolling: touch;
            }
        }

        .focus-ring:focus {
            outline: 2px solid var(--secondary);
            outline-offset: 2px;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: var(--dark);
        }

        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-item {
            background: linear-gradient(135deg, #FFFFFF 0%, #F8FAF9 100%);
            border: 2px solid #E6F4EA;
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 90, 36, 0.15);
            border-color: #FFA500;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #005A24;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #646464;
            font-weight: 500;
        }

        .stat-icon {
            font-size: 1.5rem;
            color: #FFA500;
            margin-bottom: 0.5rem;
        }

       .chart-container {
    position: relative;
    height: 300px;
    margin: 1rem 0;
}
/* Estilo personalizado para legenda em duas colunas */
#distributionChart {
    margin-bottom: 1rem;
}

.legend-container {
    display: flex;
    justify-content: space-evenly;
    align-items: flex-start;
    margin-top: 0.1rem;
    margin-left: 40px;
    padding: 0.5rem;
    background: linear-gradient(to bottom, rgba(255, 255, 255, 0.9), rgba(230, 244, 234, 0.4));
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 90, 36, 0.05);
    flex-wrap: wrap;
    gap: 1rem;
}

.legend-column {
    flex: 1;
    max-width: 45%;
    padding: 0.5rem;

    border-radius: 8px;

    transition: all 0.3s ease;
    text-align: left;
}

@media (max-width: 640px) {
    .legend-container {
        flex-direction: column;
    }
    .legend-column {
        max-width: 100%;
        min-width: auto;
    }
    .legend-item::after {
        width: calc(100% - 2rem);
    }
}



.legend-column > h4 {
    font-size: 0.95rem;
    font-weight: 700;
    color: #1A3C34;
    padding: 0.25rem 0;
    margin-bottom: 0.35rem;
    border-bottom: 2px solid;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    width: fit-content;
}

.legend-column:first-child > h4 {
    border-bottom-color: #005A24;
}

.legend-column:last-child > h4 {
    border-bottom-color: #FFA500;
}

.legend-column > h4::before {
    content: '';
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
    transition: transform 0.3s ease;
}

.legend-column:first-child > h4::before {
    background-color: #004D1F;
}

.legend-column:last-child > h4::before {
    background-color: #CC7A00;
}

.legend-column:hover > h4::before {
    transform: scale(1.2);
}

.legend-subgroup {
    padding: 0.25rem;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.5);
}

.legend-subgroup h5 {
    font-size: 0.85rem;
    font-weight: 600;
    color: #1A3C34;
    margin-bottom: 0.25rem;
    opacity: 0.9;
}

.legend-item {
    display: flex;
    align-items: center;
    padding: 0.15rem 0.25rem;
    gap: 0.5rem;
    font-size: 0.8rem;
    color: #1A3C34;
    font-weight: 500;
    border-radius: 4px;
    transition: background-color 0.2s ease;
}

.legend-item:hover {
    background-color: rgba(255, 255, 255, 0.8);
}

.legend-item .legend-color {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    transition: transform 0.2s ease;
}

.legend-item:hover .legend-color {
    transform: scale(1.3);
}

        .sidebar-link {
            transition: all 0.3s ease;
        }

        .sidebar-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(0.5rem);
        }

        .sidebar-link.active {
            background-color: rgba(255, 165, 0, 0.2);
            color: #FFA500;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100vw;
                max-width: 320px;
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col font-sans">
    <div id="overlay" class="overlay fixed inset-0 bg-black/30 z-40 lg:hidden"></div>
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar (from index.php) -->
        <aside id="sidebar" class="sidebar bg-primary fixed left-0 top-0 h-screen w-80 shadow-2xl z-50 lg:translate-x-0 lg:static lg:z-auto custom-scrollbar overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-8 pb-6 border-b border-white/20">
                    <div class="animate-slide-in-left">
                        <div class="flex items-center space-x-3 mb-2">
                            <img src="../assets/Brasão_do_Ceará.svg.png" alt="Brasão do Ceará" class="w-8 h-10 transition-transform hover:scale-105">
                            <h2 class="text-white text-2xl font-bold font-display">Sistema Seleção</h2>
                        </div>
                    </div>
                    <button id="closeSidebar" class="text-white lg:hidden btn-animate p-2 rounded-xl hover:bg-white/10 focus-ring">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <nav class="space-y-2">
                    <?php if (
                        isset($_SESSION["tipo_usuario"]) &&
                        ($_SESSION["tipo_usuario"] === "admin" ||
                            $_SESSION["tipo_usuario"] === "cadastrador")
                    ) { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.1s;">
                            <a href="../index.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Dashboard</span>
                                    <p class="text-green-200 text-xs mt-1">Página inicial</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>
                    <?php if (
                        isset($_SESSION["tipo_usuario"]) &&
                        $_SESSION["tipo_usuario"] === "admin"
                    ) { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.2s;">
                            <a href="cursos.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Cursos</span>
                                    <p class="text-green-200 text-xs mt-1">Administrar cursos</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>
                    <?php if (
                        isset($_SESSION["tipo_usuario"]) &&
                        ($_SESSION["tipo_usuario"] === "admin" ||
                            $_SESSION["tipo_usuario"] === "cadastrador")
                    ) { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.3s;">
                            <a href="candidatos.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Candidatos</span>
                                    <p class="text-green-200 text-xs mt-1">Gerenciar inscrições</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>
                    <?php if (
                        isset($_SESSION["tipo_usuario"]) &&
                        $_SESSION["tipo_usuario"] === "admin"
                    ) { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.35s;">
                            <a href="cotas.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a5 5 0 10-10 0v2M5 9h14l-1 11H6L5 9z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Cotas</span>
                                    <p class="text-green-200 text-xs mt-1">Regras e perfis</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>
                    <?php if (
                        isset($_SESSION["tipo_usuario"]) &&
                        $_SESSION["tipo_usuario"] === "admin"
                    ) { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.4s;">
                            <a href="usuario.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Usuários</span>
                                    <p class="text-green-200 text-xs mt-1">Controle de acesso</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>
                    <?php if (
                        isset($_SESSION["tipo_usuario"]) &&
                        $_SESSION["tipo_usuario"] === "admin"
                    ) { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.5s;">
                            <a href="relatorios.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring bg-white/10">
                                <div class="w-12 h-12 bg-secondary rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Relatórios</span>
                                    <p class="text-green-200 text-xs mt-1">Gerar documentos</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>
                    <?php if (
                        isset($_SESSION["tipo_usuario"]) &&
                        ($_SESSION["tipo_usuario"] === "admin" ||
                            $_SESSION["tipo_usuario"] === "cadastrador")
                    ) { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.6s;">
                            <a href="solicitar_alteracao.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Requisições</span>
                                    <p class="text-green-200 text-xs mt-1">Alteração de candidato</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>
                    <?php if (
                        isset($_SESSION["tipo_usuario"]) &&
                        $_SESSION["tipo_usuario"] === "admin"
                    ) { ?>
                        <div class="animate-slide-in-left" style="animation-delay: 0.6s;">
                            <a href="limpar_banco.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-red-500 group-hover:scale-110 transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-base">Limpar Banco</span>
                                    <p class="text-green-200 text-xs mt-1">Resetar dados</p>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <!-- FAQ -->
                    <div class="animate-slide-in-left" style="animation-delay: 0.7s;">
                        <a href="faq.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring">
                            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994 .54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <span class="font-semibold text-base">FAQ</span>
                                <p class="text-green-200 text-xs mt-1">Dúvidas frequentes</p>
                            </div>
                        </a>
                    </div>
                </nav>
            </div>
        </aside>
        <!-- Main Content -->
        <div class="main-content flex-1 bg-white">
            <header class="bg-white shadow-sm border-b border-gray-200 z-30 sticky top-0">
                <div class="px-3 sm:px-4 md:px-6 lg:px-8 py-3 sm:py-4">
                    <div class="flex items-center justify-between">
                        <button id="openSidebar" class="text-primary lg:hidden p-2 sm:p-3 rounded-lg hover:bg-accent focus-ring">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <div class="flex items-center space-x-2 sm:space-x-4 lg:ml-auto">
                            <div class="hidden sm:block text-right">
                                <p class="text-xs sm:text-sm font-semibold text-gray-900">Bem-vindo,</p>
                                <p class="text-xs sm:text-sm text-primary font-medium"><?= $_SESSION[
                                    "nome"
                                ] ?? "Usuário" ?></p>
                            </div>
                            <a href="perfil.php" title="Perfil" class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-primary to-dark rounded-full flex items-center justify-center hover:brightness-95 focus-ring">
                                <span class="text-white font-bold text-xs sm:text-sm"><?= strtoupper(
                                    substr($_SESSION["nome"] ?? "U", 0, 1),
                                ) ?></span>
                            </a>
                            <a href="../models/sessions.php?sair" class="bg-primary text-white px-3 sm:px-4 md:px-6 py-2 sm:py-3 rounded-lg hover:bg-dark font-semibold shadow-lg focus-ring text-xs sm:text-sm">
                                <span class="hidden sm:inline">Sair</span>
                                <i class="fas fa-sign-out-alt sm:hidden"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </header>
            <main class="p-4 sm:p-6 lg:p-8">
                <div class="text-center mb-8">
                    <h1 class="text-primary text-3xl md:text-4xl font-bold mb-4 page-title tracking-tight font-heading">
                        <i class="fas fa-chart-line mr-3 text-secondary"></i>
                        Relatórios
                    </h1>
                    <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                        Acesse relatórios detalhados e estatísticas em tempo real
                    </p>
                </div>

                <!-- Estatísticas Rápidas -->
                <div class="quick-stats mb-8">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="stat-number"><?php echo $select->countTotalAlunos(); ?></div>
                        <div class="stat-label">Total de Candidatos</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="stat-number"><?php echo $select->countTotalCursos(); ?></div>
                        <div class="stat-label">Total de Cursos</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-number"><?php echo $select->countTotalRelatorios(); ?></div>
                        <div class="stat-label">Total de Requisições</div>
                    </div>
                </div>

                  <!-- Gráfico de Estatísticas -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            <div class="bg-white rounded-xl shadow-card p-6 border-2 border-accent hover:border-secondary transition-all duration-300">
                <div class="flex items-center justify-between mb-4 pb-3 border-b-2 border-accent">
                    <h3 class="text-xl font-bold text-primary font-heading flex items-center">
                        <i class="fas fa-chart-pie text-secondary mr-3"></i>
                        Distribuição por Modalidade
                    </h3>
                </div>
                <div class="chart-container" style="height: 240px;">
                    <canvas id="distributionChart"></canvas>
                </div>

                    <div class="legend-container">
                        <div class="legend-column" style="align-self: flex-start;">
                            <h4>Pública (Ampla) (<span id="public-ampla-percent">0%</span>) <span class="legend-color" style="background-color: #005A24; display: inline-block; width: 10px; height: 10px; border-radius: 2px; margin-left: 4px; vertical-align: middle;"></span></h4>
                            <div class="legend-subgroup">
                                <h5>Pública (Cotas) (<span id="public-cotas-percent">0%</span>)</h5>
                                <div class="legend-item" id="legend-public-bairro" style="display: none;">
                                    <span class="legend-color" style="background-color: #4CAF50;"></span>
                                    <span class="legend-text"></span>
                                </div>
                                <div class="legend-item" id="legend-public-pcd" style="display: none;">
                                    <span class="legend-color" style="background-color: #A5D6A7;"></span>
                                    <span class="legend-text"></span>
                                </div>
                            </div>
                        </div>
                        <div class="legend-column" style="align-self: flex-end;">
                            <h4>Privada (Ampla) (<span id="private-ampla-percent">0%</span>) <span class="legend-color" style="background-color: #FFA500; display: inline-block; width: 10px; height: 10px; border-radius: 2px; margin-left: 4px; vertical-align: middle;"></span></h4>
                            <div class="legend-subgroup">
                                <h5>Privada (Cotas) (<span id="private-cotas-percent">0%</span>)</h5>
                                <div class="legend-item" id="legend-private-bairro" style="display: none;">
                                    <span class="legend-color" style="background-color: #FF8C00;"></span>
                                    <span class="legend-text"></span>
                                </div>
                                <div class="legend-item" id="legend-private-pcd" style="display: none;">
                                    <span class="legend-color" style="background-color: #FFD180;"></span>
                                    <span class="legend-text"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>

                    <!-- Gráfico de Barras -->
                    <!-- Gráfico de Barras com rótulos abaixo de cada barra -->
                <div class="bg-white rounded-xl shadow-card p-6 border-2 border-accent hover:border-secondary transition-all duration-300">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b-2 border-accent">
                        <h3 class="text-xl font-bold text-primary font-heading flex items-center">
                            <i class="fas fa-chart-bar text-secondary mr-3"></i>
                            Candidatos por Curso
                        </h3>
                    </div>
                    <div class="chart-container chart-bar" style="height: 280px;">
                        <canvas id="coursesChart"></canvas>
                    </div>
                </div>
                </div>
                <!-- Seção de Relatórios -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                    <!-- Escolas Públicas e Privadas -->
                    <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in">
                        <div class="card-shine"></div>
                        <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-school text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-primary mb-2 text-center font-heading">Relatórios</h3>
                        <p class="text-gray-600 text-center mb-6">Gere relatórios detalhados por tipo de escola (pública/privada) e modalidade (AC/cotas)</p>
                        <button onclick="openSchoolReportModal()" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-3 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold flex items-center justify-center">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Gerar Relatório
                        </button>
                    </div>

                    <!-- Resultados -->
                    <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in">
                        <div class="card-shine"></div>
                        <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-chart-line text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-primary mb-2 text-center font-heading">Resultados</h3>
                        <p class="text-gray-600 text-center mb-6">Gere relatórios de classificados, classificáveis e resultados finais</p>
                        <button onclick="openReportModal('resultados', 'Resultados')" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-3 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold flex items-center justify-center">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Gerar Relatório
                        </button>
                    </div>

                    <!-- Relatórios Específicos -->
                    <div class="report-card bg-white border-2 border-primary rounded-xl shadow-card p-6 flex flex-col items-center animate-fade-in">
                        <div class="card-shine"></div>
                        <div class="card-icon w-16 h-16 text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-file-alt text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-primary mb-2 text-center font-heading">Relatórios Específicos</h3>
                        <p class="text-gray-600 text-center mb-6">Gere relatórios específicos como comissão de seleção, movimentações e requisições</p>
                        <button onclick="openSpecificReportModal()" class="bg-gradient-to-r from-secondary to-orange-500 text-white py-3 px-6 rounded-lg hover:from-orange-500 hover:to-secondary transition-all duration-300 font-semibold flex items-center justify-center">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Gerar Relatório
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal for Report Selection (Resultados) -->
    <div id="reportModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('reportModal')">×</button>
            <h2 class="font-heading" id="reportModalTitle">
                <i class="fas fa-file-alt mr-2 text-secondary"></i>
                Gerar Relatório
            </h2>
            <form id="reportForm" action="../controllers/controller_relatorios.php" method="POST" class="space-y-4">
                <input type="hidden" name="form" value="relatorio_pdf">

                <!-- Tipo de Relatório -->
                <div class="form-group">
                    <label for="tipo_relatorio" class="font-semibold">
                        <i class="fas fa-list mr-1"></i>
                        Tipo de Relatório
                    </label>
                    <select name="tipo_relatorio" id="tipo_relatorio" required class="select2-tipo">
                        <option value="" disabled selected>SELECIONAR TIPO DE RELATÓRIO</option>
                        <option value="Classificados">Classificados</option>
                        <option value="Classificaveis">Classificáveis</option>
                        <option value="Resultado Final">Resultado Final</option>
                        <option value="Resultado pré-liminar">Resultado Pré-liminar</option>
                    </select>
                </div>

                <!-- Curso -->
                <div class="form-group mt-4">
                    <label for="curso_id" class="font-semibold">
                        <i class="fas fa-book mr-1"></i>
                        Curso
                    </label>
                    <select name="curso" id="curso_id" required class="select2-curso">
                        <option value="" disabled selected>SELECIONAR CURSO</option>
                        <?php
                        $cursos = $select->select_cursos();
                        foreach ($cursos as $curso) { ?>
                            <option value="<?= htmlspecialchars(
                                $curso["id"],
                            ) ?>"><?= htmlspecialchars(
    $curso["nome_curso"],
) ?></option>
                        <?php }
                        ?>
                    </select>
                </div>

                <button type="submit" class="confirm-btn">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Gerar Relatório
                </button>
            </form>
        </div>
    </div>

    <!-- Modal for School Reports -->
    <div id="schoolReportModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('schoolReportModal')">×</button>
            <h2 class="font-heading" id="schoolReportModalTitle">
                <i class="fas fa-file-alt mr-2 text-secondary"></i>
                Gerar Relatório de Escolas
            </h2>
            <form id="schoolReportForm" action="../controllers/controller_relatorios.php" method="POST" class="space-y-4">
                <input type="hidden" name="form" value="relatorio_pdf">

                <!-- Tipo de Relatório para Escolas -->
                <div class="form-group">
                    <label for="school_tipo_relatorio" class="font-semibold">
                        <i class="fas fa-list mr-1"></i>
                        Tipo de Relatório
                    </label>
                    <select name="tipo_relatorio" id="school_tipo_relatorio" required class="select2-school-tipo">
                        <option value="" disabled selected>SELECIONAR TIPO DE RELATÓRIO</option>
                        <option value="publica_ac">Pública AC</option>
                        <option value="publica_cotas">Pública Cotas</option>
                        <option value="publica_geral">Pública Geral</option>
                        <option value="privada_ac">Privada AC</option>
                        <option value="privada_cotas">Privada Cotas</option>
                        <option value="privada_geral">Privada Geral</option>
                    </select>
                </div>

                <!-- Curso -->
                <div class="form-group mt-4">
                    <label for="school_curso_id" class="font-semibold">
                        <i class="fas fa-book mr-1"></i>
                        Curso
                    </label>
                    <select name="curso" id="school_curso_id" required class="select2-school-curso">
                        <option value="" disabled selected>SELECIONAR CURSO</option>
                        <?php
                        $cursos = $select->select_cursos();
                        foreach ($cursos as $curso) { ?>
                            <option value="<?= htmlspecialchars(
                                $curso["id"],
                            ) ?>"><?= htmlspecialchars(
    $curso["nome_curso"],
) ?></option>
                        <?php }
                        ?>
                    </select>
                </div>

                <button type="submit" class="confirm-btn">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Gerar Relatório
                </button>
            </form>
        </div>
    </div>
<<script>
    // Definir cores do sistema
    const systemColors = {
        primary: '#005A24',
        secondary: '#FFA500',
        dark: '#1A3C34',
        accent: '#E6F4EA'
    };

    // Função para formatar nome do curso
    function formatarNomeCurso(nome) {
        return nome
            .replace('TÉCNICO EM ', '')
            .replace('TÉCNICO', '')
            .trim()
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
            .join(' ');
    }

    // Dados dos alunos por tipo
    const alunosPublicaAC = <?php echo $select->countAlunosPorTipo(
        "publica_ac",
    ); ?>;
    const alunosPublicaCotasBairro = <?php echo $select->countAlunosPorTipo(
        "publica_cotas_bairro",
    ); ?>;
    const alunosPublicaCotasPCD = <?php echo $select->countAlunosPorTipo(
        "publica_cotas_pcd",
    ); ?>;
    const alunosPrivadaAC = <?php echo $select->countAlunosPorTipo(
        "privada_ac",
    ); ?>;
    const alunosPrivadaCotasBairro = <?php echo $select->countAlunosPorTipo(
        "privada_cotas_bairro",
    ); ?>;
    const alunosPrivadaCotasPCD = <?php echo $select->countAlunosPorTipo(
        "privada_cotas_pcd",
    ); ?>;

    const totalAlunos = alunosPublicaAC + alunosPublicaCotasBairro + alunosPublicaCotasPCD +
                       alunosPrivadaAC + alunosPrivadaCotasBairro + alunosPrivadaCotasPCD;

    // Dados dos alunos por curso
    const dadosCursos = <?php
    $cursos = $select->countAlunosPorCurso();
    echo json_encode($cursos);
    ?>;

    // ==========================================
    // GRÁFICO DE ROSCA (DISTRIBUIÇÃO)
    // ==========================================
    const ctxPie = document.getElementById('distributionChart').getContext('2d');
    const distributionChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: totalAlunos > 0
                ? [
                    'Escola Pública - Ampla Concorrência',
                    'Escola Pública - Cotas Bairro',
                    'Escola Pública - Cotas PCD',
                    'Escola Privada - Ampla Concorrência',
                    'Escola Privada - Cotas Bairro',
                    'Escola Privada - Cotas PCD'
                ]
                : ['Nenhum aluno cadastrado'],
            datasets: [{
                data: totalAlunos > 0
                    ? [
                        alunosPublicaAC,
                        alunosPublicaCotasBairro,
                        alunosPublicaCotasPCD,
                        alunosPrivadaAC,
                        alunosPrivadaCotasBairro,
                        alunosPrivadaCotasPCD
                    ]
                    : [1],
         backgroundColor: totalAlunos > 0
    ? [
        '#005A24',  // Verde padrão do sistema - Pública AC
        '#4CAF50',  // Verde médio - Pública Cotas Bairro
        '#A5D6A7',  // Verde claro - Pública Cotas PCD
        '#FFA500',  // Laranja padrão do sistema - Privada AC
        '#FF8C00',  // Laranja escuro - Privada Cotas Bairro
        '#FFD180'   // Laranja claro - Privada Cotas PCD
    ]
    : ['#e0e0e0'],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 15,
                hoverBorderColor: '#ffffff',
                hoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            rotation: -90,
            circumference: 360,
            plugins: {
                title: {
                    display: false
                },
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(26, 60, 52, 0.95)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: systemColors.secondary,
                    borderWidth: 2,
                    padding: 16,
                    displayColors: true,
                    boxWidth: 15,
                    boxHeight: 15,
                    boxPadding: 8,
                    titleFont: {
                        family: 'Inter',
                        size: 15,
                        weight: 'bold'
                    },
                    bodyFont: {
                        family: 'Inter',
                        size: 14,
                        weight: '500'
                    },
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const percentage = totalAlunos > 0 ? ((value / totalAlunos) * 100).toFixed(1) : 0;
                            return ` ${value} candidatos (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 800,
                easing: 'easeInOutCubic'
            }
        }
    });

    // Preencher legenda customizada
    // Preencher legenda customizada
function updateCustomLegend() {
    // Calcular percentuais individuais
    function calcularPorcentagem(valor) {
        return totalAlunos > 0 ? ((valor / totalAlunos) * 100).toFixed(1) : '0';
    }

    // Dados completos com valores e percentuais
    const dadosEstatisticas = {
        publica: {
            ampla: {
                valor: alunosPublicaAC,
                percentual: calcularPorcentagem(alunosPublicaAC)
            },
            cotasBairro: {
                valor: alunosPublicaCotasBairro,
                percentual: calcularPorcentagem(alunosPublicaCotasBairro)
            },
            cotasPCD: {
                valor: alunosPublicaCotasPCD,
                percentual: calcularPorcentagem(alunosPublicaCotasPCD)
            }
        },
        privada: {
            ampla: {
                valor: alunosPrivadaAC,
                percentual: calcularPorcentagem(alunosPrivadaAC)
            },
            cotasBairro: {
                valor: alunosPrivadaCotasBairro,
                percentual: calcularPorcentagem(alunosPrivadaCotasBairro)
            },
            cotasPCD: {
                valor: alunosPrivadaCotasPCD,
                percentual: calcularPorcentagem(alunosPrivadaCotasPCD)
            }
        }
    };

    // Calcular totais para grupos
    const totalPublicaCotas = alunosPublicaCotasBairro + alunosPublicaCotasPCD;
    const totalPrivadaCotas = alunosPrivadaCotasBairro + alunosPrivadaCotasPCD;

    // Atualizar percentuais dos títulos principais
    if (totalAlunos > 0) {
        document.getElementById('public-ampla-percent').textContent =
            dadosEstatisticas.publica.ampla.percentual + '%';
        document.getElementById('public-cotas-percent').textContent =
            calcularPorcentagem(totalPublicaCotas) + '%';
        document.getElementById('private-ampla-percent').textContent =
            dadosEstatisticas.privada.ampla.percentual + '%';
        document.getElementById('private-cotas-percent').textContent =
            calcularPorcentagem(totalPrivadaCotas) + '%';
    }

    // Atualizar legendas individuais
    const legendData = [
        {
            id: 'legend-public-pcd',
            ...dadosEstatisticas.publica.cotasPCD,
            label: 'Cota (PCD)'
        },
        {
            id: 'legend-public-bairro',
            ...dadosEstatisticas.publica.cotasBairro,
            label: 'Cota (Bairro)'
        },
        {
            id: 'legend-private-pcd',
            ...dadosEstatisticas.privada.cotasPCD,
            label: 'Cota (PCD)'
        },
        {
            id: 'legend-private-bairro',
            ...dadosEstatisticas.privada.cotasBairro,
            label: 'Cota (Bairro)'
        }
    ];

    legendData.forEach(item => {
        const element = document.getElementById(item.id);
        if (element && item.valor > 0) {
            element.style.display = 'flex';
            element.querySelector('.legend-text').textContent = `${item.label} ${item.percentual}%`;
        }
    });
}

updateCustomLegend();

    // ==========================================
    // GRÁFICO DE BARRAS (CURSOS)
    // ==========================================
    const ctxBar = document.getElementById('coursesChart').getContext('2d');

    const createGradient = (ctx, chartArea) => {
        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
        gradient.addColorStop(0, '#FF8C00');
        gradient.addColorStop(0.5, '#FFA500');
        gradient.addColorStop(1, '#FFB520');
        return gradient;
    };

    const coursesChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: dadosCursos.map(curso => formatarNomeCurso(curso.nome_curso)),
            datasets: [{
                label: 'Candidatos',
                data: dadosCursos.map(curso => curso.total),
                backgroundColor: function(context) {
                    const chart = context.chart;
                    const {ctx, chartArea} = chart;
                    if (!chartArea) {
                        return systemColors.secondary;
                    }
                    return createGradient(ctx, chartArea);
                },
                borderColor: 'transparent',
                borderWidth: 0,
                borderRadius: {
                    topLeft: 10,
                    topRight: 10,
                    bottomLeft: 0,
                    bottomRight: 0
                },
                borderSkipped: false,
                hoverBackgroundColor: function(context) {
                    const chart = context.chart;
                    const {ctx, chartArea} = chart;
                    if (!chartArea) {
                        return systemColors.primary;
                    }
                    const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                    gradient.addColorStop(0, '#003D18');
                    gradient.addColorStop(0.5, '#005A24');
                    gradient.addColorStop(1, '#007830');
                    return gradient;
                },
                barThickness: 35,
                maxBarThickness: 45
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    bottom: 0
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 90, 36, 0.1)',
                        lineWidth: 1.5,
                        drawBorder: false
                    },
                    border: {
                        display: false,
                        dash: [5, 5]
                    },
                    ticks: {
                        font: {
                            family: 'Inter',
                            size: 13,
                            weight: '600'
                        },
                        color: '#1A3C34',
                        padding: 12,
                        stepSize: Math.ceil(Math.max(...dadosCursos.map(c => c.total)) / 5)
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    border: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: 'Inter',
                            size: 12,
                            weight: '600'
                        },
                        color: '#1A3C34',
                        maxRotation: 0,
                        minRotation: 0,
                        padding: 0
                    }
                }
            },
            plugins: {
                title: {
                    display: false
                },
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(26, 60, 52, 0.95)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: systemColors.secondary,
                    borderWidth: 2,
                    padding: 16,
                    displayColors: false,
                    titleFont: {
                        family: 'Inter',
                        size: 15,
                        weight: 'bold'
                    },
                    bodyFont: {
                        family: 'Inter',
                        size: 14,
                        weight: '500'
                    },
                    callbacks: {
                        title: function(context) {
                            return context[0].label;
                        },
                        label: function(context) {
                            return `Total: ${context.raw} candidato${context.raw !== 1 ? 's' : ''}`;
                        }
                    },
                    yAlign: 'bottom'
                }
            },
            animation: {
                duration: 800,
                easing: 'easeInOutQuart',
                delay: (context) => {
                    let delay = 0;
                    if (context.type === 'data' && context.mode === 'default') {
                        delay = context.dataIndex * 50;
                    }
                    return delay;
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
</script>
    <!-- Sidebar toggle -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const openSidebar = document.getElementById('openSidebar');
        const closeSidebar = document.getElementById('closeSidebar');

        openSidebar.addEventListener('click', () => {
            sidebar.classList.add('open');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        });

        closeSidebar.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        });
    </script>

    <!-- Modal handling for Results -->
    <script>
        function openReportModal(reportType, reportLabel) {
            const modal = document.getElementById('reportModal');
            const modalTitle = document.getElementById('reportModalTitle');
            modalTitle.textContent = `Gerar Relatório: ${reportLabel}`;
            modal.classList.add('show');
            setTimeout(() => {
                $('.select2-tipo').select2({
                    placeholder: "SELECIONAR TIPO DE RELATÓRIO",
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "Nenhum tipo encontrado";
                        },
                        searching: function() {
                            return "Pesquisando...";
                        }
                    }
                });
                $('.select2-curso').select2({
                    placeholder: "SELECIONAR CURSO",
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "Nenhum curso encontrado";
                        },
                        searching: function() {
                            return "Pesquisando...";
                        }
                    }
                });
            }, 100);
        }
    </script>

    <!-- Modal handling for Schools -->
    <script>
        function openSchoolReportModal() {
            const modal = document.getElementById('schoolReportModal');
            modal.classList.add('show');
            setTimeout(() => {
                $('.select2-school-tipo').select2({
                    placeholder: "SELECIONAR TIPO DE RELATÓRIO",
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "Nenhum tipo encontrado";
                        },
                        searching: function() {
                            return "Pesquisando...";
                        }
                    }
                });
                $('.select2-school-curso').select2({
                    placeholder: "SELECIONAR CURSO",
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "Nenhum curso encontrado";
                        },
                        searching: function() {
                            return "Pesquisando...";
                        }
                    }
                });
            }, 100);
        }
    </script>

    <script>
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('show');
            document.body.style.overflow = '';
            if (modalId === 'reportModal') {
                $('.select2-curso').select2('destroy');
                $('.select2-tipo').select2('destroy');
            } else if (modalId === 'schoolReportModal') {
                $('.select2-school-curso').select2('destroy');
                $('.select2-school-tipo').select2('destroy');
            } else if (modalId === 'specificReportModal') {
                $('.select2-specific').select2('destroy');
                $('.select2-user').select2('destroy');
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('reportModal');
                closeModal('schoolReportModal');
                closeModal('specificReportModal');
            }
        });
    </script>

    <!-- Form validation for Results -->
    <script>
        const reportForm = document.getElementById('reportForm');
        reportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const tipo = document.getElementById('tipo_relatorio').value;
            const curso = document.getElementById('curso_id').value;
            if (!tipo || !curso) {
                showNotification('Por favor, selecione tipo e curso.', 'error');
                return;
            }
            const submitBtn = reportForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="loading-spinner"></span> Gerando...';
            submitBtn.disabled = true;
            setTimeout(() => {
                reportForm.submit();
            }, 1000);
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            }, 5000);
        });
    </script>

    <!-- Form validation for Schools -->
    <script>
        const schoolReportForm = document.getElementById('schoolReportForm');
        schoolReportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const tipo = document.getElementById('school_tipo_relatorio').value;
            const curso = document.getElementById('school_curso_id').value;
            if (!tipo || !curso) {
                showNotification('Por favor, selecione tipo e curso.', 'error');
                return;
            }
            const submitBtn = schoolReportForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="loading-spinner"></span> Gerando...';
            submitBtn.disabled = true;
            setTimeout(() => {
                schoolReportForm.submit();
            }, 1000);
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            }, 5000);
        });
    </script>

    <!-- Notification function -->
    <script>
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
            const bgColor = type === 'error' ? 'bg-red-500' : type === 'success' ? 'bg-green-500' : 'bg-blue-500';
            notification.className += ` ${bgColor} text-white`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${type === 'error' ? 'exclamation-circle' : type === 'success' ? 'check-circle' : 'info-circle'} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
    </script>

    <!-- Initialize Select2 on page load -->
    <script>
        $(document).ready(function() {
            $('.select2-curso').select2({
                placeholder: "SELECIONAR CURSO",
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Nenhum curso encontrado";
                    },
                    searching: function() {
                        return "Pesquisando...";
                    }
                }
            });
            $('.select2-tipo').select2({
                placeholder: "SELECIONAR TIPO DE RELATÓRIO",
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Nenhum tipo encontrado";
                    },
                    searching: function() {
                        return "Pesquisando...";
                    }
                }
            });
            $('.select2-school-curso').select2({
                placeholder: "SELECIONAR CURSO",
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Nenhum curso encontrado";
                    },
                    searching: function() {
                        return "Pesquisando...";
                    }
                }
            });
            $('.select2-school-tipo').select2({
                placeholder: "SELECIONAR TIPO DE RELATÓRIO",
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Nenhum tipo encontrado";
                    },
                    searching: function() {
                        return "Pesquisando...";
                    }
                }
            });
        });
    </script>

    <!-- Movimentações modal handlers -->
    <script>
        function openUserReportModal() {
            const modal = document.getElementById('userReportModal');
            modal.classList.add('show');
            setTimeout(() => {
                $('.select2-usuario').select2({
                    placeholder: "SELECIONAR USUÁRIO",
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function() { return "Nenhum usuário encontrado"; },
                        searching: function() { return "Pesquisando..."; }
                    }
                });
            }, 100);
        }

        function closeUserModal() {
            const modal = document.getElementById('userReportModal');
            modal.classList.remove('show');
            const form = document.getElementById('userReportForm');
            if (form) form.reset();
            document.body.style.overflow = '';
            if ($('.select2-usuario').data('select2')) {
                $('.select2-usuario').select2('destroy');
            }
        }

        function openSpecificReportModal() {
            const modal = document.getElementById('specificReportModal');
            modal.classList.add('show');
            setTimeout(() => {
                $('.select2-specific').select2({
                    placeholder: "SELECIONAR TIPO DE RELATÓRIO",
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "Nenhum tipo encontrado";
                        },
                        searching: function() {
                            return "Pesquisando...";
                        }
                    }
                });

                $('.select2-user').select2({
                    placeholder: "SELECIONAR USUÁRIO",
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "Nenhum usuário encontrado";
                        },
                        searching: function() {
                            return "Pesquisando...";
                        }
                    }
                });

                // Inicializar o evento de mudança do tipo de relatório
                $('#specific_report_type').on('change', function() {
                    const reportType = $(this).val();
                    const userSelectContainer = document.getElementById('userSelectContainer');

                    if (reportType === 'movimentacoes') {
                        userSelectContainer.style.display = 'block';
                    } else {
                        userSelectContainer.style.display = 'none';
                    }
                });
            }, 100);
        }
    </script>

    <!-- Validação do formulário de relatório específico -->
    <script>
        const specificReportForm = document.getElementById('specificReportForm');
        specificReportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const reportType = document.getElementById('specific_report_type').value;

            if (!reportType) {
                showNotification('Por favor, selecione um tipo de relatório.', 'error');
                return;
            }

            const userSelectContainer = document.getElementById('userSelectContainer');
            const userId = document.getElementById('user_id')?.value;

            if (reportType === 'movimentacoes') {
                userSelectContainer.style.display = 'block';
                if (!userId) {
                    showNotification('Por favor, selecione um usuário.', 'error');
                    return;
                }
            } else {
                userSelectContainer.style.display = 'none';
            }

            const submitBtn = specificReportForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="loading-spinner"></span> Gerando...';
            submitBtn.disabled = true;

            let url = '../controllers/controller_relatorios.php?form=relatorio_pdf';

            switch (reportType) {
                case 'comissao_selecao':
                    url += '&tipo_relatorio=comissao_selecao';
                    break;
                case 'movimentacoes':
                    url += '&tipo_relatorio=movimentacoes&id_usuario=' + userId;  // Corrigido de user_id para id_usuario
                    break;
                case 'requisicoes':
                    url += '&tipo_relatorio=requisicoes';
                    break;
            }

            window.location.href = url;

            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            }, 5000);
        });
    </script>

    <!-- Validate and submit user report form -->
    <script>
        const userReportForm = document.getElementById('userReportForm');
        userReportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const usuario = document.getElementById('usuario_id').value;
            if (!usuario) {
                showNotification('Por favor, selecione um usuário.', 'error');
                return;
            }
            const submitBtn = userReportForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="loading-spinner"></span> Gerando...';
            submitBtn.disabled = true;
            setTimeout(() => { userReportForm.submit(); }, 400);
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            }, 5000);
        });
    </script>

    <!-- Modal for Specific Reports -->
    <div id="specificReportModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('specificReportModal')">×</button>
            <h2 class="font-heading">
                <i class="fas fa-file-alt mr-2 text-secondary"></i>
                Selecionar Tipo de Relatório
            </h2>
            <form id="specificReportForm" action="../controllers/controller_relatorios.php" method="POST" class="space-y-4">
                <input type="hidden" name="form" value="relatorio_pdf">
                <div class="form-group">
                    <label for="specific_report_type" class="font-semibold">
                        <i class="fas fa-list mr-1"></i>
                        Tipo de Relatório
                    </label>
                    <select name="tipo_relatorio" id="specific_report_type" required class="select2-specific">
                        <option value="" disabled selected>SELECIONAR TIPO DE RELATÓRIO</option>
                        <option value="comissao_selecao">Comissão de Seleção</option>
                        <option value="movimentacoes">Movimentações</option>
                        <option value="requisicoes">Requisições</option>
                    </select>
                </div>

                <!-- Campo de usuário (visível apenas para relatório de movimentações) -->
                <div id="userSelectContainer" class="form-group mt-4" style="display: none;">
                    <label for="user_id" class="font-semibold">
                        <i class="fas fa-user mr-1"></i>
                        Selecionar Usuário
                    </label>
                    <select name="user_id" id="user_id" class="select2-user">
                        <option value="" disabled selected>SELECIONAR USUÁRIO</option>
                        <?php
                        $usuarios = $select->select_usuarios();
                        foreach ($usuarios as $user) { ?>
                            <option value="<?= htmlspecialchars(
                                $user["id"],
                            ) ?>"><?= htmlspecialchars(
    $user["nome_user"],
) ?></option>
                        <?php }
                        ?>
                    </select>
                </div>

                <button type="submit" class="confirm-btn w-full">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Gerar Relatório
                </button>
            </form>
        </div>
    </div>
</body>
</html>
