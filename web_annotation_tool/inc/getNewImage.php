<?php

include 'xmlVocReadAnnotationsFile.php';
include 'configuration.php';

# $service_requested = $_GET["info"];

# Search the xml file in a $dir
function getXmlFile($dir, $filename) {
	$xml_filepath = null;
    #$files = scandir($dir);
    $results = null;	
										
	if ( file_exists($dir.DIRECTORY_SEPARATOR.$filename) ) 
	{
		$xml_filepath = $dir.DIRECTORY_SEPARATOR.$filename;
		return $xml_filepath;
	}


    return $xml_filepath;
}

#get images in text file
function getImages($dir) {
	$fp = fopen('file_list.txt', 'r');
	$lst = [];
	
	while(! feof($fp))  {
		$fname = str_replace(array("\n", "\r"), '', fgets($fp));
		if(strpos(strtoupper($fname), '.JPG') !== false){
			array_push($lst, $dir.DIRECTORY_SEPARATOR.$fname);
		}
	}
	return $lst;
}

function getMetadata($image) {
	$delimiter = "/";
	$item = explode($delimiter, $image);
	$nbItems = count($item);
		# Should be A/C type / MSN / Image name
	if ($nbItems>=3)
	{
		$image_name = $item[$nbItems-1];
		$msn = $item[$nbItems-2];
		$type = $item[$nbItems-3];
		$image_info = array("type" => $type, "msn" => $msn, 
			   "name" => $image_name);
		return $image_info;
	}
	throw "Malformatted string ".$image;
}

# get current annotation progress in percentage
function getProgress() {
	if (file_exists('progress.txt')) {
		$fp = fopen('progress.txt', 'r');
		$row = fgets($fp);
		$delimiter = " ";

		list($num_annotations, $num_images) = explode($delimiter, $row, 2);

		fclose($fp);

		if ($num_images == 0) {
			return 0;
		}
		else {
			# return progress percentage to 3dp
			return round(($num_annotations*100)/$num_images, 3);
		}
	}
	else {
		return 0;
	}
}

#$log = 'file.log';
#file_put_contents($log, "INFO - Start the loop\n");

/*
foreach($images as $file) 
{	
	#echo $file;
	file_put_contents($log, $file);
	# Process file
	if ( (strpos(strtoupper($file), '.JPG') !== false) && (strstr($file, $COLLECTION_NAME)) )
	{
		# echo $file . "<br>";
		$delimiter = "/";
		$item = explode($delimiter, $file);
		$nbItems = count($item);
		# Should be A/C type / MSN / Image name
		if ($nbItems>=3)
		{

			$image_name = $item[$nbItems-1];
			$msn = $item[$nbItems-2];
			$type = $item[$nbItems-3];
			$image_info = array("type" => $type, "msn" => $msn, 
			   "name" => $image_name);

			# Add the image in the list
			$list_of_images[$image_index] = $image_info;
			$image_index = $image_index + 1;			
			
			# Try to find the annotation
			$id = str_replace(array(".jpg",".JPG"),".jpg", $image_name);
			$xml_filename = str_replace(array(".jpg",".JPG"), ".xml", $id);
			$xml_filepath = getXmlFile($ANNOTATIONS_DIR, $xml_filename);

			if ($xml_filepath != null)
			{
				$list_of_annotated_images[$annotated_image_index] = $image_info;
				$annotated_image_index = $annotated_image_index + 1;
			}
			else
			{
				$list_of_not_annotated_images[$not_annotated_image_index] = $image_info;
				$not_annotated_image_index = $not_annotated_image_index + 1;
			}
		}									
	}		
}
*/
$file = 'file.log';
file_put_contents($file, "INFO - getNewImage.php\n");

# Show the list
/*echo "Annotated images:<br>";
foreach( $list_of_annotated_images as $image_info ) 
{
	echo $image_info["type"] . "/" . $image_info["msn"] . "/" . $image_info["name"] . "<br>";	
}

echo "<br>All images:<br>";
foreach( $list_of_images as $image_info ) 
{
	echo $image_info["type"] . "/" . $image_info["msn"] . "/" . $image_info["name"] . "<br>";	
}

echo "Number of images :" . count($list_of_images) ."<br>";*/



# New image 80%
$random_new = rand(0, 99);
file_put_contents($file, "Random index = ".$random_new."\n",FILE_APPEND | LOCK_EX);

$not_found = true; //TODO: This will be slow as more things get annotated
$images = getImages($IMAGE_ROOT_DIR);
$image_info = null;
while ($not_found) {
	$choice = random_int(0, count($images)-1);
	$image_info = getMetadata($images[$choice]);
	$xml_filename = str_replace(array(".jpg",".JPG"), ".xml", $image_info["name"]);
	$xml_filepath = getXmlFile($ANNOTATIONS_DIR, $xml_filename);
	if($xml_filepath === null)
		$not_found = false;
}

$current_progress = getProgress();
/*
# Not annotated 80%
if ( ($random_new < $ratio_new_old) && (count($list_of_not_annotated_images)>0))
{
	file_put_contents($file, "Not annotated 80%\n",FILE_APPEND | LOCK_EX);
	# Get a random number 
	$random_index = rand(0, count($list_of_not_annotated_images)-1);
	$image_info = $list_of_not_annotated_images[$random_index];
}
# Annotated 20%
else
{
	file_put_contents($file, "Annotated 20%\n",FILE_APPEND | LOCK_EX);
	# If exist
	if (count($list_of_annotated_images)>0)
	{
		# Get a random number 
		$random_index = rand(0, count($list_of_annotated_images)-1);
		$image_info = $list_of_annotated_images[$random_index];
	}
	else
	{
		file_put_contents($file, "Force not annotated\n",FILE_APPEND | LOCK_EX);
		# Get a random number 
		$random_index = rand(0, count($list_of_not_annotated_images)-1);
		$image_info = $list_of_not_annotated_images[$random_index];
	}
}
*/

#	$random_index = rand(0, count($list_of_images)-1);
#	$image_info = $list_of_images[$random_index];

$url = $IMAGE_WEB_DIR."/".$image_info["type"] . "/" . $image_info["msn"] . "/" . $image_info["name"];

# Remove extension
$id = str_replace(array(".jpg",".JPG"),".jpg", $image_info["name"]);

# Get the xml file, replace .jpg by xml
$xml_filename = str_replace(array(".jpg",".JPG"), ".xml", $id);			

# Try to find the annotation
$xml_filepath = getXmlFile($ANNOTATIONS_DIR, $xml_filename);

if ($xml_filepath != null)
{
	# echo "xml_filepath" . $xml_filepath;
	$annotations = [];
	$xml = new xmlVocReadAnnotationsFile($xml_filepath);
	
	file_put_contents($file, "xml_filepath ".$xml_filepath."\n",FILE_APPEND | LOCK_EX);
	
	if (!$xml->hasError())
	{
		file_put_contents($file, "Parse XML\n",FILE_APPEND | LOCK_EX);
		$xml->parseXML();
		if (!$xml->hasError())
		{
			$annotations = $xml->getAnnotations();
			file_put_contents($file, "Annotations ".serialize($annotations)."\n",FILE_APPEND | LOCK_EX);
		}
	}	
	else
	{
		file_put_contents($file, "An error occurs\n",FILE_APPEND | LOCK_EX);
		$annotations = [];
	}
}
else
{	
	file_put_contents($file, "No annotations found.\n",FILE_APPEND | LOCK_EX);
	$annotations = [];
}

file_put_contents($file, "Annotations ".serialize($annotations)."\n",FILE_APPEND | LOCK_EX);

file_put_contents($file, "URL image = ".$url."\n",FILE_APPEND | LOCK_EX);

# Prepare message to send
$data = array ("url" => $url, "id" => $id, "folder" => $image_info["type"] . "/" . $image_info["msn"], 
				"annotations" => $annotations, "current_progress" => $current_progress);
	
file_put_contents($file, "Annotations ".serialize($data)."\n",FILE_APPEND | LOCK_EX);
	
header('Content-Type: application/json');
echo json_encode($data);

?>