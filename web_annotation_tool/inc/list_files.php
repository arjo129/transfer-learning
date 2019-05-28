<?php
include "configuration.php";

# create index of images in images folder
function create_index($dir, $index_name) {
    $files = scandir ($dir); 
    $fp = fopen($index_name, 'w');
    for($i = 0; $i < count($files); ++$i){
        echo "". $files[$i] . "\n";
        fwrite($fp, $files[$i] . "\n");
    }
    fclose($fp);

    if (count($files)-2 < 0){ 
        return 0;
    }
    else {
        return count($files)-2;
    }
}

# keep track of number of images and annotations. useful for progress bar.
function create_progress($num_annotations, $num_images, $progress_name) {
    $fp = fopen($progress_name, 'w');
    
    echo "". $num_annotations . " " . $num_images . "\n";
    fwrite($fp, $num_annotations . " " . $num_images . "\n");
    
    fclose($fp);
}

$num_images = create_index($IMAGE_ROOT_DIR, 'file_list.txt');
$num_annotations = create_index($ANNOTATIONS_DIR, 'annotation_list.txt');

create_progress($num_annotations, $num_images, 'progress.txt');

?>