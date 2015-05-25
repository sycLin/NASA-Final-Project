#!/usr/bin/python
import sys
import chilkat
import cgi
import cgitb

# cgi script requirements
print "Content-type:text/html\n\n"
print "<html><head><title>Python CGI, YES!</title></head><body>"

# get variables from HTML form
form = cgi.FieldStorage()
Host = form.getvalue('Host')
Username = form.getvalue('Username')
Password = form.getvalue('Password')
Pid = form.getvalue('Pid')
Port = 22

# establish SSH connect
ssh = chilkat.CkSsh()

success = ssh.Connect(Host, Port)
if success != True:
	print ssh.lastErrorText()
	sys.exit()

# set timeout = 5 seconds
ssh.put_IdleTimeoutMs(5000)

# Authenticate using login/password:
success = ssh.AuthenticatePw(Username, Password)
if success != True:
	print ssh.lastErrorText()
	sys.exit()

# open a session channel (we can have plenty of channels)
