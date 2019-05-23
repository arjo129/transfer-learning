<?php
include "configuration.php";
$files = scandir ($IMAGE_ROOT_DIR); 
$fp = fopen('file_list.txt', 'w');
for($i = 0; $i < count($files); ++$i){
    echo "". $files[$i] . "\n";
    fwrite($fp, $files[$i] . "\n");
}
fclose($fp);