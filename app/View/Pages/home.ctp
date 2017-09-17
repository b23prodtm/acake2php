<!-- Voila l'endroit ou je souhaite que mes vues soient affichées //-->
<?php
        
        $nouvelleAdresse = 'index.php/e14/index'; //Nouvelle adresse
        if (filter_input(INPUT_SERVER, 'QUERY_STRING') && (filter_input(INPUT_SERVER, 'QUERY_STRING') != '')) {
                $nouvelleAdresse .= '?' . substr(filter_input(INPUT_SERVER, 'QUERY_STRING'), 0, 2048);
        } //RÃ©cupÃšre les paramÃštres
        if (!headers_sent()) {
                header('Location: ' . $nouvelleAdresse); //Redirection HTTP
                header('HTTP/1.1 301 Moved Permanently');
                header('Status: 301 Moved Permanently');
                header('Content-Type: text/html; charset=UTF-8');
        }
        $nouvelleAdresse = htmlspecialchars($nouvelleAdresse, ENT_QUOTES); //Encode les caractÃšres HTML spÃ©ciaux
        echo '<!DOCTYPE html>' . "\n",
        '<html xmlns="http://www.w3.org/1999/xhtml">' . "\n",
        '<head>' . "\n",
        '<meta charset="UTF-8" />' . "\n",
        '<meta http-equiv="refresh" content="0; url=' . $nouvelleAdresse . '" />' . "\n", //Redirection HTML
        '<title>Redirection</title>' . "\n",
        '<meta name="robots" content="noindex" />' . "\n",
        '</head>' . "\n",
        "\n",
        '<body>' . "\n",
        '<p><a href="' . $nouvelleAdresse . '">Redirection</a></p>' . "\n",
        '</body>' . "\n",
        '</html>' . "\n";
        exit;
?>

