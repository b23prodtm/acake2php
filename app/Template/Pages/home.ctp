<!DOCTYPE html>
<?php
include CMS_INCLUDE_PATH . 'Index.php';
${__FILE__} = new Index($this, __FILE__, true, dirname(__DIR__));
?>
<html lang="fr">
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="refresh" content="5;URL=<?php echo ${__FILE__}->sitemap['e13__index']; ?>">
        <link rel="stylesheet" href=<?php echo ${__FILE__}->sitemap['etc__stylesheet.css']; ?> type="text/css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo ${__FILE__}->lang("cgv", "shop"); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .welcome-message {
            font-weight: bold;
            font-size: 24px;
            margin-top: 50px;
        }
        .cookie-banner {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: center;
            z-index: 1000;
            display: <?php echo isset($_COOKIE['cookie_consent']) ? 'none' : 'block'; ?>;
        }
        .cookie-banner button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            margin-left: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="welcome-message">
        Bienvenue sur notre site web !
    </div>

    <!-- Bannière pour l'acceptation des cookies -->
    <div class="cookie-banner" id="cookieBanner">
        Nous utilisons des cookies pour améliorer votre expérience.
        <button onclick="acceptCookies()">Accepter</button>
    </div>

    <script>
    function acceptCookies() {
        fetch('<?php $this->Url->build(["controller" => "Pages", "action" => "acceptCookies"]) ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrfToken"]').content,
            },
            body: JSON.stringify({ accept: true })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('cookieBanner').style.display = 'none';
            }
        })
        .catch(error => console.error('Erreur:', error));
    }
    </script>

</body>
</html>
