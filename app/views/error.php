<?php if (!defined('BASE_URL')) require_once __DIR__ . '/../../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MesaLista - Error <?= htmlspecialchars($errorCode) ?></title>

    <!-- LOGO -->
    <link rel="icon" href="<?= BASE_URL ?>/public/assets/img/logo_1.png" />

    <!-- BOOTSTRAP -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT"
        crossorigin="anonymous" />
    <style>
        .error-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .error-content {
            text-align: center;
        }

        .error-content h1 {
            font-size: 6rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .error-content p {
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }

        .lottie-animation {
            max-width: 400px;
            margin-bottom: 2rem;
        }
    </style>
</head>

<!-- ESTILOS -->
<link rel="stylesheet" href="../Styles/index.css" />

<!-- ICONOS -->
<link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

<body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.9.6/lottie.min.js"></script>

    <div class="error-container">
        <div class="lottie-animation"></div>
        <div class="error-content">
            <h1><?= htmlspecialchars($errorCode) ?></h1>
            <p><?= htmlspecialchars($errorMessage) ?></p>
            <div class="d-flex justify-content-center gap-1">
                <a href="#" class="btn bsb-btn-5xl btn-danger rounded-pill px-4 py-2 fs-5 m-0" onclick="goBack()">Regresar</a>
                <a href="<?= BASE_URL; ?>/login" class="btn bsb-btn-5xl btn-primary rounded-pill px-4 py-2 fs-5 m-0">Login</a>
            </div>
        </div>
    </div>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
    <script>
        const animation = lottie.loadAnimation({
            container: document.querySelector(".lottie-animation"),
            renderer: "svg",
            loop: true,
            autoplay: true,
            path: "https://lottie.host/d987597c-7676-4424-8817-7fca6dc1a33e/BVrFXsaeui.json",
        });

        function goBack() {
            window.history.back();
        }
    </script>
</body>

</html>