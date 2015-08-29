<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * 
 */

echo crypt(md5(filter_input(INPUT_POST,'pass')), filter_input(INPUT_POST,'salt'));
?><html><head><title>hash pass</title></head><body>
        <form action="getHashPassword.php" method="POST">type in salt : <input type="text" name="salt" size="10"/><br>
            type in pass <input type="text" name="pass" size="10"/>
            <input type="submit"/></form>
    </body>
</html>