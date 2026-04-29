<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ultra5G Project')</title>
    
    <!-- jQuery CDN (Required for inline scripts) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- CSS3 Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --bg-color: #f8f9fa;
            --text-color: #212529;
            --glass-bg: rgba(255, 255, 255, 0.8);
            --glass-border: rgba(255, 255, 255, 0.2);
            --card-shadow: rgba(31, 38, 135, 0.1);
        }

        [data-theme="dark"] {
            --bg-color: #0f172a;
            --text-color: #f1f5f9;
            --glass-bg: rgba(30, 41, 59, 0.8);
            --glass-border: rgba(255, 255, 255, 0.1);
            --card-shadow: rgba(0, 0, 0, 0.4);
            --secondary-color: #94a3b8;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            width: 100%;
            box-sizing: border-box;
        }


        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 30px;
            margin-top: 50px;
            box-shadow: 0 8px 32px 0 var(--card-shadow);
            animation: fadeIn 0.8s ease-out;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }
            .card {
                padding: 20px;
                margin-top: 30px;
            }
            h2 {
                font-size: 1.5rem;
            }
            h3 {
                font-size: 1.2rem;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        a {
            text-decoration: none;
            color: inherit;
            border: none;
            outline: none;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.4);
            filter: brightness(1.1);
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .status-online {
            background-color: #d4edda;
            color: #155724;
        }

        footer {
            margin-top: auto;
            text-align: center;
            padding: 20px;
            color: var(--secondary-color);
            font-size: 0.9em;
        }

        /* Theme Toggle */
        .theme-toggle-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-color);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1000;
            box-shadow: 0 4px 12px var(--card-shadow);
            transition: all 0.3s ease;
        }

        .theme-toggle-btn:hover {
            transform: scale(1.1);
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @yield('styles')
</head>
<body>
    <button class="theme-toggle-btn" id="theme-toggle" title="Toggle Dark/Light Mode">
        <i class="fas fa-moon"></i>
    </button>

    <main class="container">
        @yield('content')
    </main>

    <footer>
        &copy; {{ date('Y') }} SUNUltra5G Project. All rights reserved.
    </footer>

    @yield('scripts')

    <script>
        $(document).ready(function() {
            const themeToggle = $('#theme-toggle');
            const themeIcon = themeToggle.find('i');
            const currentTheme = localStorage.getItem('theme') || 'light';

            if (currentTheme === 'dark') {
                $('html').attr('data-theme', 'dark');
                themeIcon.removeClass('fa-moon').addClass('fa-sun');
            }

            themeToggle.on('click', function() {
                const isDark = $('html').attr('data-theme') === 'dark';
                
                if (isDark) {
                    $('html').removeAttr('data-theme');
                    localStorage.setItem('theme', 'light');
                    themeIcon.removeClass('fa-sun').addClass('fa-moon');
                } else {
                    $('html').attr('data-theme', 'dark');
                    localStorage.setItem('theme', 'dark');
                    themeIcon.removeClass('fa-moon').addClass('fa-sun');
                }
            });
        });
    </script>
</body>
</html>
