<?php
if($pUrl) {
        echo $this->Markdown->transform(file_get_contents($pUrl));
}
?>