<?php
require ("../include/php_index.inc.php");
$r = new Index(filter_input(INPUT_SERVER,'PHP_SELF'));
?>
<html>
    <head>
        <title><?php echo $r->lang("cgv", "shop"); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="refresh" content="5;URL=<?php echo $GLOBALS['e13__index']; ?>">
        <link rel="stylesheet" href=<?php echo $GLOBALS['etc__stylesheet.css']; ?> type="text/css">
    </head>

    <body bgcolor="#000000" text="#FFFFFF">
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif"><b><?php echo $r->lang("cgv", "shop"); ?></b></font></div>
    </body>
</html>