<?php
${__FILE__} = new Index($this, __FILE__, true, dirname(__DIR__));
?>
<html>
    <head>
        <title><?php echo ${__FILE__}->lang("cgv", "shop"); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="refresh" content="5;URL=<?php echo ${__FILE__}->sitemap['e13__index']; ?>">
        <link rel="stylesheet" href=<?php echo ${__FILE__}->sitemap['etc__stylesheet.css']; ?> type="text/css">
    </head>

    <body bgcolor="#000000" text="#FFFFFF">
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif"><b><?php echo ${__FILE__}->lang("cgv", "shop"); ?></b></font></div>
    </body>
</html>
<?php exit; ?>
