#!/bin/sh
grep prismjs bower.json | egrep -o "[0-9]+\.[0-9]+(:?\.?[0-9]+)"
