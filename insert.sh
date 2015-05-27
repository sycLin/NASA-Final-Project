#!/bin/bash

# insert hypertext related files 
sudo cp -r ./src/htdocs/* /var/www/html/webMS/

# insert cgi-bin
sudo cp ./src/cgi-bin/* /var/www/html/cgi-bin/
