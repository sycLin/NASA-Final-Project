#!/bin/bash

# eject hypertext related files 
sudo cp -r /var/www/html/webMS/* ./src/htdocs/

# eject cgi-bin
sudo cp /var/www/html/cgi-bin/* ./src/cgi-bin/

