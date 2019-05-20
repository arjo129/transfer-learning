<h1>How to retrain last few layers of Object Detection models using transfer learning</h1>

credit: 
- https://medium.com/coinmonks/part-1-2-step-by-step-guide-to-data-preparation-for-transfer-learning-using-tensorflows-object-ac45a6035b7a
- https://github.com/chewwt/shiny-octo-umbrella
- https://github.com/tzutalin/labelImg

<h3>Requirements</h3>
Tensorflow, object-detection, labelImg, jupyter notebook.

Recommended to use virtualenv with python3 for labelling using labelImg and training via Tensorflow.

Either pip3 install labelImg or build labelImg from source (recommended). To build from source, after cloning this repo,

`pip3 install pyqt5 lxml`

`make qt5py3`

<h3>How to extract images from rosbag</h3>
open extract_images_rosbag.bash and edit the 5 variables (or run python scripts/extract.py and enter arguments in command line). 'images', 'annotations'
and 'output_model' directories will be created to facillitate later development work.

<h3>How to label/annotate images offline</h3>

1. source venv3/bin/activate (py3 has pyqt5 dependency for labelimg)

2. edit predefined_classes.txt. run python labelImg/labelImg.py [IMAGE_PATH] predefined_classes.txt

3. Create bounding boxes for each image and label accordingly. Save before pressing next.

4. Save xml annotations to the annotations folder.

<h3>How to label/annotate images on LAN</h3>

1. Install dependencies. sudo apt-get install php php-dom

2. Add the images to annotate folder to web_annotation/data/[YOUR_FOLDER_NAME]. eg: web_annotations/data/jiangshi

3. Edit configuration file found in web_annotation_tool/inc/configuration.php and the label classes in web_annotation_tool/resources/list_of_tags.json

4. Run the webserver (in web_annotation_tools directory). `php -S 0.0.0.0:8000`

5. Connect to the webpage via http://[HOST_IP]:8000, where [HOST_IP] is the IP address of the server hosting the webpage

6. Draw a rectangle over the image and select one of the labels that matches the picture. Annotations saved in annotations folder. 

<h3>How to convert from Pascal VOC xml files to a single CSV format (required for tensorflow)</h3>

1. Run `python scripts/xml_to_csv.py [ANNOTATIONS_PATH] ` to create csv label

<h3>How to split train/test set</h3>

1. Run jupyter notebook. navigate and open split_labels.ipynb. currently 80% train 20% test split.

2. export object_detection PYTHON_PATH slim

3. edit generate_tf_tfrecord.py to include label id for objects. ex: if row == 'bat' id = 1.

4. Create train data:
  python generate_tfrecord.py --csv_input=train_labels.csv  --image_dir=images --output_path=train.record

5. Create test data:
  python generate_tfrecord.py --csv_input=test_labels.csv --image_dir=images --output_path=test.record

<h3> How to retrain last few layers of object detection model </h3>

1. create a label_map.pbtxt. item{
				    id:1
				    name: bat}

2. edit pipeline.config from original model. 

    2.1 edit numofclasses to 2 (or number of objects u are detecting)
    
    2.2 configure the PATH_TO_BE_CONFIGURED/ in train.config in pipeline.config
    
    2.3 edit num_examples to be number of objects in test.record

3. run object_detection/model_main.py. python object_detection/model_main.py     --pipeline_config_path=/home/hashir/Desktop/bbauv/ml_models/ssdlite_mobilenet_v2_coco_2018_05_09/pipeline.config     --model_dir=/home/hashir/Desktop/bbauv/ml_models/ml_training/bat_wolf     --num_train_steps=50000     --sample_1_of_n_eval_examples=1

4. you can open another terminal to monitor progress on tensorboard. tensorboard --logdir=/home/hashir/Desktop/bbauv/ml_models/ml_training/bat_wolf

5. export the model you have just trained. choose one of the checkpoints generated
    python object_detection/export_inference_graph.py     --input_type=image_tensor     --pipeline_config_path=/home/hashir/Desktop/bbauv/ml_models/ssdlite_mobilenet_v2_coco_2018_05_09/pipeline.config     --trained_checkpoint_prefix=/home/hashir/Desktop/bbauv/ml_models/ml_training/bat_wolf/model.ckpt-472    --output_directory=/home/hashir/Desktop/bbauv/ml_models/ml_training/bat_wolf/output_model/

6. Test if the model is accurate on the test images. run jupyter notebook on object_detection/object_detection_tutorial.ipynb. change the necessary variables (model, images) to get it working.




