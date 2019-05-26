<?php
include "configuration.php";
// create index of images in images folder
function create_index($dir, $index_name) 
{
    $files = scandir ($dir); 
    $fp = fopen($index_name, 'w');
    for($i = 0; $i < count($files); ++$i){
        echo "". $files[$i] . "\n";
        fwrite($fp, $files[$i] . "\n");
    }
    fclose($fp);
}

create_index($IMAGE_ROOT_DIR, 'file_list.txt');
create_index($ANNOTATIONS_DIR, 'annotation_list.txt');

?>