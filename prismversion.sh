#!/bin/sh
grep prism bower.json | egrep -o "[0-9]+\.[0-9]+(:?\.?[0-9]+)"
