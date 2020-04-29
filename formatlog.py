#!/usr/bin/python
import fileinput
import json
import pprint
import re

for line in fileinput.input():
    print(line)
    obj = json.loads(line)
    msg = obj.get('message', '')
    if msg:
        stack = msg.split("\\n")
        obj['message'] = {'message': msg,
                          'stack': stack}
    pprint.pprint(obj)
    print('----------------------------------------------------------')
