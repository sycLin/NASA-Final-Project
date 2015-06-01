#!/bin/bash

while true ;
do
	git pull
	./insert.sh
	sleep 5
done

