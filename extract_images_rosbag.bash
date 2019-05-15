 #!/bin/bash 

# Parameters for extracting images from ROSBAG.
# SAVE_DIR is directory is store extracted images
# BAG_DIR is location of rosbag. 
# START,END,INTERVAL is ROSBAG sampling info 
# TOPICS is rostopics that you want to extract images from

SAVE_DIR='jiangshi' 
BAG_DIR='/media/hashir/OS/Users/hashi/Desktop/Bumblebee/rosbags/queenstown-jiangshi-normal_2019-05-09-22-13-42.bag'
START=0
END=240
INTERVAL=1
# TOPICS='/auv/bot_cam/image_color/compressed /auv/front_cam/image_color/compressed'
TOPICS='/auv/front_cam/image_color/compressed'

if [ -z "$BAG_DIR" ]; then
    echo "empty BAG_DIR, please set it to proceed.";
    exit 1;
fi;

IMAGES_DIR="$SAVE_DIR/images"
ANNOTATIONS_DIR="$SAVE_DIR/annotations"
OUTPUT_MODEL_DIR="$SAVE_DIR/output_model"


echo $IMAGES_DIR
if [ -z "$SAVE_DIR" ]; then
    echo "empty SAVE_DIR, please set it to proceed.";
    exit 1;
else
    mkdir -p "$IMAGES_DIR";
    mkdir -p "$ANNOTATIONS_DIR";
    mkdir -p "$OUTPUT_MODEL_DIR";
fi;


#echo "Image output: $SAVE_DIR"
#echo "Bag read from: $BAG_DIR"
#echo "Start Time of Bag: $START"
#echo "End Time of Bag: $END"
#echo "Interval to sample from Bag: $INTERVAL"

# if TOPICS not entered, will rely on defaults provided in extract.py
if [ -z "$TOPICS" ]; then
	python scripts/extract.py --save-dir=$IMAGES_DIR --bag=$BAG_DIR --start=$START --end=$END --interval=$INTERVAL ;
else
    python scripts/extract.py --save-dir=$IMAGES_DIR --bag=$BAG_DIR --start=$START --end=$END --interval=$INTERVAL --topics $TOPICS ;
fi;

