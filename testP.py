#!/usr/bin/python
# -*- coding: UTF-8 -*-
#***************************** IMPORT & VARIABLES ******************************
import csv
import datetime
import os
import requests
import psycopg2

#  Files, path
data_1 = []
data_2 = []
data_sait = []
data = []
fresult_final = "./result_final.txt"
fopen1 = "./FileA.csv"
fopen2 = "./FileB.csv"

#***************************** PROCEDURES **************************************
def checking(filenn):
    ok = False
    try:
        w=open(filenn, "w")
    except IOError,ex:
        print(ex) 
    if os.path.isfile(filenn) and os.access(filenn, os.R_OK): ok = True
    w.close()
    return ok

def data2(file_read):
    reader = csv.reader(file_read, delimiter=',')
    for row in reader:
        data_2.append(row)
 #   print(data_2)

def data1(file_read):
    reader = csv.reader(file_read, delimiter=',')
    for row in reader:
        data_1.append(row)
 #   print(data_1)

def merged_files():
    data_1last = len(data_1)
    data_2last = len(data_2)
    i=0
    while i<data_1last:
        j=0
        while j<data_2last:
            if str(data_1[i][0]) == str(data_2[j][0]):
                data.append([data_1[i][0],data_1[i][1],data_2[j][1],data_2[j][2]])
            j=j+1
        i=i+1

def write_data(tt):
    writer = open(fresult_final, "a+")
    writer.write(str(tt)+"\n")
    writer.close()
       
def merged_files_data():
    data_1last = len(data)
    data_2last = len(data_sait)
    i=0
    print(data_1last)
    print(data_2last)
    if checking(fresult_final):    
        while i<data_1last:
            print(i,data[i][0],data[i][1],data[i][2],data[i][3])
            j=0
            while j<data_2last:
                if str(data[i][1]) == str(data_sait[j][0]['email']):
                    data[i][0] = str(data_sait[j][0]['uid'])
                j=j+1
            j=0
            while j<data_2last:
                if (str(data[i][2]) == str(data_sait[j][0]['first_name']) and str(data[i][3]) == str(data_sait[j][0]['last_name'])):
                    data[i][0] = str(data_sait[j][0]['uid'])
                j=j+1                     
            print(i,data[i][0],data[i][1],data[i][2],data[i][3])
            zap=data[i][0]+' , '+data[i][1]+' , '+data[i][2]+' , '+data[i][3]
            write_data(zap)
            print('next')
            i=i+1
    else: print("File ./results_final.txt was not created or it does not have proper access rights")
    
def process():
    response_cur = []
    url_cur = "https://sandbox.tinypass.com/api/v3/publisher/user/list"
    pload = {'api_token':'zziNT81wShznajW2BD5eLA4VCkmNJ88Guye7Sw4D','aid':'o1sRRZSLlw','offset':'0'}
    try:
        response_cur = requests.post(url_cur, data = pload)
        result = response_cur.json()['users']
        j=0
        while j<len(result):
            data_sait.append([result[j]])
            j=j+1
    except (Exception, requests.ConnectionError) as error:
        return None

# main function         
def main(): 
    try:
        with open(fopen1) as f_obj: data1(f_obj)
        with open(fopen2) as f_obj1: data2(f_obj1)
    except IOError,ex:
        print(ex)  
    merged_files()
    process()
    merged_files_data()

if __name__ == "__main__":
    main()
