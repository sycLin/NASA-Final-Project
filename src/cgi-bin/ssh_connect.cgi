#!/usr/bin/python
import sys
import chilkat
import cgi
import cgitb

class ProcInfo:
	def __init__(self, init_list):
		self.pid = int(init_list[0])
		self.user = init_list[1]
		self.priority = init_list[2]
		self.nice = init_list[3] # NICE value
		self.virtual = init_list[4] # virtual memory
		self.resident = init_list[5] # physical memory
		self.shared = init_list[6] # shared memory
		self.state = init_list[7] # R(running), S(sleeping), Z(zombie)
		self.cpu = float(init_list[8]) # percentage of cpu use
		self.memory = float(init_list[9]) # percentage of memory use
		# self.time needs calculation
		tmp = init_list[10]
		tmp_list = tmp.split(":")
		self.time = float(float(tmp_list[0])*60 + float(tmp_list[1]))
		# done calculating total cpu time
		self.command = init_list[11]

# cgi script requirements
print "Content-type:text/html\n\n"
# print "<html><head><title>Python CGI, YES!</title></head><body>"

# get username and password from HTML form
form = cgi.FieldStorage()
Host = form.getvalue('Host')
Username = form.getvalue('Username')
Password = form.getvalue('Password')
SettingsProcType = form.getvalue('settings_proc_type')
SettingsSortedBy = form.getvalue('settings_sortedby')
SettingsCount =  form.getvalue('settings_count')
SettingsPort = 22

#  Important: It is helpful to send the contents of the
#  ssh.LastErrorText property when requesting support.

ssh = chilkat.CkSsh()

#  Any string automatically begins a fully-functional 30-day trial.
success = ssh.UnlockComponent("Anything for 30-day trial")
if (success != True):
    print(ssh.lastErrorText())
    sys.exit()

#  Connect to an SSH server:

#  Hostname may be an IP address or hostname:

success = ssh.Connect(Host, SettingsPort)
if (success != True):
    print(ssh.lastErrorText())
    sys.exit()

#  Wait a max of 5 seconds when reading responses..
ssh.put_IdleTimeoutMs(5000)

#  Authenticate using login/password:
success = ssh.AuthenticatePw(Username, Password);
if (success != True):
    print(ssh.lastErrorText())
    sys.exit()

#  Open a session channel.  (It is possible to have multiple
#  session channels open simultaneously.)

channelNum = ssh.OpenSessionChannel()
if (channelNum < 0):
    print(ssh.lastErrorText())
    sys.exit()

#  The SendReqExec method starts a command on the remote
#  server.   The syntax of the command string depends on the
#  default shell used on the remote server to run the command.
#  On Windows systems it is CMD.EXE.  On UNIX/Linux
#  systems the user's default shell is typically defined in /etc/password.


success = ssh.SendReqExec(channelNum,"top -n 1 -b")
if (success != True):
    print(ssh.lastErrorText())
    sys.exit()

#  Call ChannelReceiveToClose to read
#  output until the server's corresponding "channel close" is received.
success = ssh.ChannelReceiveToClose(channelNum)
if (success != True):
    print(ssh.lastErrorText())
    sys.exit()

#  Let's pickup the accumulated output of the command:

cmdOutput = ssh.getReceivedText(channelNum,"ansi")
if (cmdOutput == None ):
    print(ssh.lastErrorText())
    sys.exit()

#  Display the remote shell's command output:
#  print(cmdOutput)

# create ProcInfo objects
proc_list = []
lines = cmdOutput.splitlines()
# discard the first 7 lines (including "PID USER....")
for i in range(7):
	lines.pop(0)
for i in lines:
	newProc = ProcInfo(i.split())
	# deal with proc_type filter
	if SettingsProcType == "all":
		proc_list.append(newProc)
	elif newProc.state == SettingsProcType:
		proc_list.append(newProc)
# sort by the desired method
if SettingsSortedBy == "cpu":
	proc_list.sort(key = lambda x: x.cpu, reverse=True)
elif SettingsSortedBy == "memory":
	proc_list.sort(key = lambda x: x.memory, reverse=True)
elif SettingsSortedBy == "totaltime":
	proc_list.sort(key = lambda x: x.time, reverse=True)
else:
	print "no!! You shouldn't be here!!"
# output according to SettingsCount
if SettingsCount == "all":
	SettingsCount = 500
print "<table id='processlist' border=1>"
print "<td class='title'>PID</td>"
print "<td class='title'>User</td>"
print "<td class='title'>State</td>"
print "<td class='title'>CPU</td>"
print "<td class='title'>MEM</td>"
print "<td class='title'>TIME</td>"
print "<td class='title'>Command</td>"
print "<tr>"
for i in range(int(SettingsCount)):
	print "<td class='content'>"+str(proc_list[i].pid)+"</td>",
	print "<td class='content'>"+proc_list[i].user+"</td>",
	print "<td class='content'>"+proc_list[i].state+"</td>",
	print "<td class='content'>"+str(proc_list[i].cpu)+"</td>",
	print "<td class='content'>"+str(proc_list[i].memory)+"</td>",
	print "<td class='content'>"+str(proc_list[i].time)+"</td>",
	print "<td class='content'>"+proc_list[i].command+"</td>",
	print "<tr>"
print "</table>"
# Don't print the whole thing out b/c it's html
# We have to parse the response
"""
tmp = ""
for i in range(len(cmdOutput)):
	if cmdOutput[i] == '\n':
		print tmp,
		tmp = ""
		print "<br />"
	elif cmdOutput[i] == ' ':
		print tmp,
		tmp = ""
	else:
		tmp = tmp + cmdOutput[i]
if tmp != "":
	print tmp
	tmp = ""
"""
#  Disconnect
ssh.Disconnect()

# cgi script requirements
# print "</body></html>"

