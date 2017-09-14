<?php
if (!$i_sitemap) { require '../include/php_index.inc.php'; }
$r = new Index(filter_input(INPUT_SERVER,'PHP_SELF'));
session_destroy();
?>
<html>
    <head>
        <title><?php echo $r->lang("logoff", "admin"); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="refresh" content="2;URL=<?php echo $GLOBALS['e13__index']; ?>">
        <link rel="stylesheet" href=<?php echo $GLOBALS['etc__stylesheet.css']; ?> type="text/css">
    </head>

    <body bgcolor="#000000" text="#FFFFFF">
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif"><b><?php echo $r->lang("logoff", "admin"); ?></b></font></div>
    </body>
</html>
