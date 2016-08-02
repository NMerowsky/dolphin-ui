#!/usr/bin/python
import logging
import urllib
import urllib2
import json
import os
import re
import cgi
import warnings
import sys
import time

def main():
    try:
        url = "http://localhost/dolphin/public/api/service.php"
        print url
        data = "wkey=&sample_id=7&func=getFastqFileId"
        print data
        opener = urllib2.build_opener(urllib2.HTTPHandler())
        print opener
        mesg = opener.open(url, data=data).read()
        print mesg
        ret=str(json.loads(mesg))
        ret = re.sub (r'u\'', '\'', ret)
        ret = re.sub (r'\'', '\"', ret)
        print ret
    except Exception, ex:
        print ex

main()