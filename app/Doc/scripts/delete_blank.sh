#!/bin/bash

for file in `find app -regex "^\(.*\.\(php\|ctp\|po\|inc\|inc\.default\)\|.*/eps-.*\.sql\)$" | grep -v "\.svn"`; do
	ssed -i -R "s/[\t ]+$//g" $file
done