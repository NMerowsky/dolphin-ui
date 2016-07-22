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
    url = "http://localhost:49616/public/api/service.php"
    data = "wkey=&runparamsid=4&func=updateRunParams"
    opener = urllib2.build_opener(urllib2.HTTPHandler())
    mesg = opener.open(url, data=data).read()
    ret=str(json.loads(mesg))
    ret = re.sub (r'u\'', '\'', ret)
    ret = re.sub (r'\'', '\"', ret)

main()