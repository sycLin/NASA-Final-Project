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

ssh = chilkat.CkSsh()

# unlock
success = ssh.UnlockComponent("Anything for 30-day trial")
if (success != True):
	print "hello"
	print ssh.lastErrorText()
	sys.exit()

# establish SSH connect
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
channelNum = ssh.OpenSessionChannel()
if channelNum < 0:
	print ssh.lastErrorText()
	sys.exit()

# send the command
command = "kill -9 "+Pid
success = ssh.SendReqExec(channelNum, command)
if success != True:
	print ssh.lastErrorText()
	sys.exit()

# user "ChannelReceiveToClose()" to read output
# until "channel close" is received
success = ssh.ChannelReceiveToClose(channelNum)
if success != True:
	print ssh.lastErrorText()
	sys.exit()

# pickup the accumulated output of the command:
cmdOutput = ssh.getReceivedText(channelNum, "ansi")
if cmdOutput == None:
	print ssh.lastErrorText()
	sys.exit()

# display the remote shell's command output
if cmdOutput == "":
	print "Success"
else:
	print "Failure"

# disconnect
ssh.Disconnect()

print "</body></html>"

