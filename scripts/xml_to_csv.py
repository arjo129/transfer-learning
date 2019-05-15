import os
import glob
import pandas as pd
import xml.etree.ElementTree as ET
import argparse

def xml_to_csv(path):
    xml_list = []
    for xml_file in glob.glob(path + '/*.xml'):
        tree = ET.parse(xml_file)
        root = tree.getroot()
        for member in root.findall('object'):
            value = (root.find('filename').text,
                     int(root.find('size')[0].text),
                     int(root.find('size')[1].text),
                     member[0].text,
                     int(member[4][0].text),
                     int(member[4][1].text),
                     int(member[4][2].text),
                     int(member[4][3].text)
                     )
            xml_list.append(value)
    column_name = ['filename', 'width', 'height', 'class', 'xmin', 'ymin', 'xmax', 'ymax']
    xml_df = pd.DataFrame(xml_list, columns=column_name)
    return xml_df


def main():
    p = argparse.ArgumentParser()
    p.add_argument('path', help='Specify Annotations directory')
    args = p.parse_args()

    image_path = ''
    if os.getcwd() in args.path:
        image_path = args.path
    else:
        image_path = os.path.join(os.getcwd(), args.path)

    xml_df = xml_to_csv(image_path)
    parent_dir = os.path.dirname(os.path.dirname(image_path))
    labels_path = os.path.join(parent_dir, 'labels.csv')
    xml_df.to_csv(labels_path, index=None)
    print('Successfully converted xml to csv.')


main()
