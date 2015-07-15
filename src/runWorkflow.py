#!/bin/env python
import logging
from optparse import OptionParser
import json
import subprocess
import urllib,urllib2
import os
from os import path, access, R_OK
import getpass
import re
import random 
import string
import time
import sys
sys.path.insert(0, sys.path[0])
from config import *
from os.path import basename

def remove_comments(line, sep):
    for s in sep:
        line = line.split(s)[0]
    line += "\n"    
    return line

class Service:
    def __init__(self, servicename="service", command="command", waittime="60"):
      self.servicename  = servicename
      self.command      = command
      self.waittime     = waittime

def import_workflow(input_file=None):
    wf = list()

    with open(input_file, 'r') as infile:
      for line in infile.readlines():
        line=remove_comments(line, '#')
        if (len(line) < 5): continue

        servicename, command, waittime = re.split(r'[\t\,]+', line)
        wf.append(Service(servicename,command,waittime))
    return wf

def import_param(input_file=None):
    wf = list()
    whole_input = ""
    with open(input_file, 'r') as fo:
      for line in fo.readlines():
        line=remove_comments(line, '#')
        if (len(line) < 2): continue
        whole_input += line
    whole_input=re.sub('[\t\s]*[\n\r]','\n', whole_input)
    whole_input=re.sub('\n@',';@', whole_input)
    whole_input=re.sub('\n',':', whole_input)
    whole_input=re.sub(r'[\t\s]*=+[\t\s:]*', '=', whole_input)
    whole_input=re.sub(r'[\t\s]+', ',', whole_input)
    whole_input=re.sub(r':@?$', '', whole_input)
    return whole_input
        
def main():

    try:
        parser = OptionParser()
        parser.add_option('-i', '--inputparam', help='input parameters for the workflow', dest='inputparam')
        parser.add_option('-p', '--defaultparam', help='defined parameter file that will be run on cluster', dest='defaultparam')
        parser.add_option('-u', '--username', help='defined user in the cluster', dest='username')
        parser.add_option('-k', '--wkey', help='defined key for the workflow', dest='wkey')
        parser.add_option('-w', '--workflowfile', help='workflow filename', dest='workflowfile')
        parser.add_option('-d', '--dbhost', help='dbhost name', dest='dbhost')
        parser.add_option('-o', '--outdir', help='output directory in the cluster', dest='outdir')
        parser.add_option('-f', '--config', help='configuration parameter section', dest='config')
                
        (options, args) = parser.parse_args()
    except:
        print "OptionParser Error:for help use --help"
        sys.exit(2)

    INPUTPARAM      = options.inputparam
    DEFAULTPARAM    = options.defaultparam
    USERNAME        = options.username
    WKEY            = options.wkey 
    WORKFLOWFILE    = options.workflowfile
    DBHOST          = options.dbhost
    OUTDIR          = options.outdir
    CONFIG          = options.config
    
    print CONFIG
    config=getConfig(CONFIG)
    url=config['url']
    DBHOST=config['dbhost']
    LOGPATH=config['logpath']

    print url
    print DBHOST
    print LOGPATH

    #This section is just for username conversion in the cluster can be removed in the future
    if (CONFIG != "Docker"):
       com="grep "+USERNAME+" /project/umw_biocore/svcgalaxy/conv.file|awk '{print $2}'"
       USERNAME=str(os.popen(com).readline().rstrip())
    ########

    if (len(USERNAME)<3): 
        print "Error:Username doesn't exist"
        sys.exit(2)
    
    if (WKEY==None):
      WKEY="start"
    if (OUTDIR==None):
      OUTDIR="~/out"
    if (OUTDIR.find("/")==-1):
      OUTDIR="~/"+OUTDIR

    if (INPUTPARAM!=None):
        if path.isfile(INPUTPARAM) and access(INPUTPARAM, R_OK):
            INPUTPARAM = import_param(INPUTPARAM)
        else:
            INPUTPARAM = re.sub(" ", "", INPUTPARAM)
    services=import_workflow(WORKFLOWFILE)
    slen=str(len(services))    
   
    wfname = os.path.splitext(basename(WORKFLOWFILE))[0]

    opener = urllib2.build_opener(urllib2.HTTPHandler())
    data = urllib.urlencode({'func':'startWorkflow', 'inputparam':INPUTPARAM, 
                       'defaultparam':DEFAULTPARAM, 'username':USERNAME, 
                       'workflow':wfname, 'wkey':WKEY, 'outdir':OUTDIR, 'services':slen})
    trials=0
    while trials<5:
       try:
          mesg = opener.open(url, data=data).read()
          trials=10
       except:
          print "Couldn't connect to dolphin server"
          time.sleep(15)
       trials=trials+1   
    ret=str(json.loads(mesg))
    wkey = ret

    if (ret.startswith("ERROR")):
                logging.warning("ERROR:"+wfname + ":" + wkey)
                print wfname + ":" + wkey + "\n"
                print "Check the parameter files:\n"
                sys.exit(2);
    print "WORKFLOW STARTED:"+wkey+"\n"

    logging.basicConfig(filename=LOGPATH+'/'+wkey+'.log', filemode='w',format='%(asctime)s %(message)s', datefmt='%m/%d/%Y %I:%M:%S %p', level=logging.DEBUG)
    logging.info(USERNAME+":"+OUTDIR)
    logging.info(INPUTPARAM)
    logging.info('WORKFLOW STARTED')

    for service in services:
        br=1
        while ( br==1):
            print service.servicename + ":" + wkey + ":" + service.command
            trials=0
            while trials<5:
              try:
                 data = urllib.urlencode({'func':'startService', 'servicename':service.servicename, 
                        'wkey':wkey, 'command':service.command})
                 resp = opener.open(url, data=data).read()
                 print resp
                 trials=10
              except:
                 print "Couldn't connect to dolphin server"
                 print "wkey:"+wkey
                 print "service:"+service.servicename
                 time.sleep(15)
              trials=trials+1

            ret=str(json.loads(resp))
            print ret + "\n"
            if (ret.startswith("RUNNING") and float(service.waittime)>0):
                #print service.waittime+"\n"
                time.sleep(float(service.waittime))
            elif (ret.startswith("ERROR")):
                print service.servicename + ":" + ret + "\n"
                logging.warning("ERROR:"+ret)
                logging.warning("ERROR:"+service.command)
                print "Check the command:\n"
                print service.command + "\n"
                sys.exit(2);
            else:
                br=0
    br=1
    print "All the services Ended"    
    while ( br==1):
        trials=0
        while trials<5:
           try:
              data = urllib.urlencode({'func':'endWorkflow',  'wkey':wkey})
              resp = opener.open(url, data=data).read()
              trials=10
           except:
              print "Couldn't connect to dolphin server"
              time.sleep(15)
           trials=trials+1

        res=str(json.loads(resp))
        #print ret + "\n"
        if (ret.startswith("WRUNNING")):
            time.sleep(5)
        else:
            br=0

if __name__ == "__main__":
    main()

