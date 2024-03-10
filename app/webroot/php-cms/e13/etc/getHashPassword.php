<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *
 */
$pass = filter_input(INPUT_POST, 'pass');
$salt = filter_input(INPUT_POST, 'salt');
/* not used in HTTP request*/
$hash_file = "";
for ($i = 0; $i < $argc; $i++) {
        if (preg_match("/-[pP]/", $argv[$i]) == 1) {
                $pass = $argv[$i + 1];
        }
        if (preg_match("/-[sS]/", $argv[$i]) == 1) {
                $salt = $argv[$i + 1];
        }
        if (preg_match("/-[fF]/", $argv[$i]) == 1) {
                $hash_file = $argv[$i + 1];
        }
}
$encrypted = crypt(md5($pass), $salt);
$msg = "Password was encrypted with $salt : " . $encrypted . "\nYou can use it as GET_HASH_PASSWORD. Goodbye !\n";
if ($argc > 0) {
        /** command line ./configure.sh script */
        echo $msg;
        file_put_contents($hash_file, "#!/usr/bin/env bash\nexport GET_HASH_PASSWORD=" . $encrypted);
}
if ($argc == 0):
        /* no arg == HTTP REQUEST */
        ?>
        <html><head><title>hash pass</title></head><body>
                <form action="getHashPassword.php" method="POST">type in salt : <input type="text" name="salt" size="10"/><br>
                    type in pass <input type="text" name="pass" size="10"/>
                    <input type="submit"/></form>
                <p><?php echo $msg; ?></p>
            </body>
        </html>
        <?php
endif;
exit;
?>
