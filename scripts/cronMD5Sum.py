#!/usr/bin/env python
import os, re, string, sys
import warnings
import ConfigParser
import json
import time
import urllib,urllib2
from sys import argv, exit, stderr

class cronMD5Sum:
    url=""
    f=""
    def __init__(self, url, f ):
        self.url = url
        self.f = f
    def getAllFastqInfo(self):  
        data = urllib.urlencode({'func':'getAllFastqInfo'})
        ret = self.f.queryAPI(self.url, data)
        if (ret):
            ret=json.loads(ret)
        return ret
    def runMD5SumUpdate(self, config, clusteruser, backup_dir, file_name):
        """
        $this->username=$params['clusteruser'];
        $this->readINI();
        $backup_dir   = $params['backup_dir'];
        $file_name    = $params['file_name'];
        $command      = $this->python . " " . $this->tool_path."/checkMD5Sum.py -o $backup_dir -f $file_name -u " . $this->username ." -c ".$this->config;
        $command=str_replace("\"", "\\\"", $command);
        $command=str_replace("\\\"", "\\\\\"", $command);
        $com = $this->python . " " . $this->tool_path . "/runService.py -f ".$this->config." -u " . $this->username .
                " -o $backup_dir -k ZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZ -c \"$command\" -n stepMD5Sum_".explode(",",$file_name)[0]." -s stepMD5Sum_".explode(",",$file_name)[0];
        $com = $this->getCMDs($com);
        $retval = $this->sysback($com);
        """
        command = config['python'] + " " + config['tool_path'] + "/checkMD%Sum.py -o " + backup_dir + " -f " + file_name + " -u " + clusteruser + " -c " + config['config']
        
        com = config['python'] + " " + config['tool_path'] + "/runService.py -f " + config['config'] + " -u " + config['username'] + " -o " + backup_dir + " -k ZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZ -c \"" + command + "\" -n stepMD%Sum_ " + file.split(',')[0] + " -s stepMD%Sum_" + file.split(',')[0] 
        com = urllib.urlencode({'func':'getCMDs', 'com':str(com)})
        
        data = urllib.urlencode({'func':'runMD5SumUpdate', 'clusteruser':str(clusteruser), 'backup_dir':str(backup_dir), 'file_name':str(file_name)})
        ret = self.f.queryAPI(self.url, data)
    
def main():
    try:
        params_section = sys.argv[1]
        print params_section
    except:
        print "cronMD5Sum.py takes in 1 argument <params section>"
        sys.exit(2)
    configparse = ConfigParser.ConfigParser()
    configparse.read("../config/config.ini")
    sys.path.insert(0, configparse.get(params_section, 'DOLPHIN_TOOLS_SRC_PATH'))
    
    f = __import__('funcs').funcs()
    config = __import__('config').getConfig(params_section)
    md5sum = cronMD5Sum(config['url'], f)
    
    filelist = md5sum.getAllFastqInfo()
    print "\n"
    for file in filelist:
        clusteruser=file['clusteruser']
        backup_dir=file['backup_dir']
        file_name=file['file_name']
        print file_name
        md5sum.runMD5SumUpdate(config, clusteruser, backup_dir, file_name)
        print "\n"
    
main()