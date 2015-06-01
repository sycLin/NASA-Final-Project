#!/usr/bin/python
import sys
import chilkat
import cgi
import cgitb

class UserInfo:
	def __init__(self, init_list):
		bias = 0
		self.name = init_list[0]
		self.ip = init_list[1]
		self.login = init_list[2]
		self.idle = init_list[3+bias]
		self.what = init_list[6+bias]
		s = init_list[3].decode("big5")
		
		#if not init_list[3].find(u'\u6708'):
		#	self.idle = 'uuuuu'
		#s = '月'
		#self.login = str(len(init_list[3]))
		#if init_list[3].list()
		
# cgi script requirements
print "Content-type:text/html\n\n"
# print "<html><head><title>Python CGI, YES!</title></head><body>"

# get variables from HTML form
form = cgi.FieldStorage()
Host = form.getvalue('Host')
Username = form.getvalue('Username')
Password = form.getvalue('Password')
SettingsCount =  form.getvalue('settings_count')
ShowIP = form.getvalue('settings_ip')
ShowLogin = form.getvalue('settings_login')
ShowIdle = form.getvalue('settings_idle')
ShowWhat = form.getvalue('settings_what')
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
command = "w -h | tr -s ' ' | cut -d ' ' -f 1,3,4,5,6,7,8,9"
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

# parsing user list 
user_list = []
lines = cmdOutput.splitlines()

for i in lines:
	newUser = UserInfo(i.split())
	user_list.append(newUser)

Showlist = "<td class='title'>User</td>"
if ShowIP == "1":
	Showlist += "<td class='title'>IP</td>"
if ShowLogin == "1":
	Showlist += "<td class='title'>Login Time</td>"
if ShowIdle == "1":
	Showlist += "<td class='title'>Idle time</td>"
if ShowWhat == "1":
	Showlist += "<td class='title'>What</td>"
print "<table id='userlist' border=1>"
print Showlist
print "<tr>"

for i in range(int(SettingsCount)):
	print "<td class='content'>"+user_list[i].name+"</td>",
	if ShowIP == "1":
		print "<td class='content'>"+user_list[i].ip+"</td>",
	if ShowLogin == "1":
		print "<td class='content'>"+user_list[i].login+"</td>",
	if ShowIdle == "1":
		print "<td class='content'>"+user_list[i].idle+"</td>",
	if ShowWhat == "1":
		print "<td class='content'>"+user_list[i].what+"</td>"
	print "<tr>"
print "</table>"





# disconnect
ssh.Disconnect()

# print "</body></html>"

