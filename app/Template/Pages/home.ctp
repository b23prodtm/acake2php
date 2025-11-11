<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur notre site</title>
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
            // Envoie une requête AJAX pour enregistrer le consentement côté serveur
            fetch('<?php echo $this->Url->build(["action" => "acceptCookies"]); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
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
