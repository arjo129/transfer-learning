#!/usr/bin/env python
from __future__ import print_function
import rospy
import rosbag

import cv2
import numpy as np
import os
import sys
import argparse

class DataExtractor():

    def __init__(self, save_dir):
        self.save_dir = save_dir
        self.img_id = 1

    def extract(self, bag_file, desired_topic, start, end, interval=10, group='train'):
        print("Extracting images from {} via rostopic={}.\nSampling from t={} to {} every {}s".format(bag_file, desired_topic, start, end, interval))

        prev = None
        head = True
        with rosbag.Bag(bag_file, 'r') as bag:
            for topic, msg, t in bag.read_messages(desired_topic):
                if head:
                    start += t.secs
                    end += t.secs
                    head = False

                if t.secs < start:
                    continue

                if t.secs > end:
                    break
             

                # save images some intervals apart
                if prev is not None and t.secs - prev < interval:
                    #print prev, t.secs
                    continue
                else:
                    prev = t.secs

                cvimg = self.compressed_ros_to_cv2(msg)
                
                img_name = os.path.join(self.save_dir, str(os.path.basename(bag_file)) + '_' + str(str(self.img_id) + '.jpg'))
                cv2.imwrite(img_name, cvimg)
                print('saved to {}'.format(img_name))
                self.img_id += 1
                    

    def compressed_ros_to_cv2(self, img):
        try:
            np_arr = np.fromstring(img.data, np.uint8)
            frame = cv2.imdecode(np_arr, cv2.IMREAD_COLOR)
        except CvBridgeError as e:
            rospy.logerr(e)
        return frame 

if __name__ == '__main__':
    p = argparse.ArgumentParser()
    p.add_argument('--save-dir', dest='save_dir', type=str, required=True, help='directory to save extracted images')
    p.add_argument('--bag', '-b', dest='bag', type=str, required=True, help='directory where bag is stored')   
    p.add_argument('--start', '-s', dest='start', type=int, default=0, help='rosbag start time')
    p.add_argument('--end', '-e', dest='end', type=int, default=240, help='rosbag end time')
    p.add_argument('--interval', '-i', dest='interval', type=int, default=1, help='rosbag interval to sample')
    p.add_argument('--topics', '-t', dest='topics', type=str, nargs='+', default=['/auv/bot_cam/image_color/compressed'])

    args = p.parse_args()

    extractor = DataExtractor(args.save_dir)
    for topic in args.topics:
        extractor.extract(args.bag, topic, args.start, args.end, args.interval, 'train')
