<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Under Construction</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            font-family: 'Public Sans', sans-serif;
        }
        .coming-soon-container {
            max-width: 600px;
            padding: 2rem;
        }
        .btn-custom {
            margin: 10px;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        .construction-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="coming-soon-container">
        <div class="construction-icon">🚧</div>
        <h1 class="display-3">Under Construction</h1>
        <p class="lead">Our CRM system is currently being upgraded. We'll be back soon with amazing new features!</p>
        
    </div>
</body>
</html>
