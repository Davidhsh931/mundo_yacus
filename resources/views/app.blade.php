<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mundo Yacus</title>
@vite(['resources/js/app.js', "resources/js/Pages/" . str_replace('\\', '/', $page['component']) . ".vue"])
</head>
<body class="font-sans antialiased">
    @inertia <!-- aquí Inertia cargará tus Pages Vue -->
</body>
</html>