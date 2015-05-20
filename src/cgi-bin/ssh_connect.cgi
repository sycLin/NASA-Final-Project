#!/usr/bin/python
import sys
import chilkat
import cgi
import cgitb

# cgi script requirements
print "Content-type:text/html\n\n"
print "<html><head><title>Python CGI, YES!</title></head><body>"

# get username and password from HTML form
form = cgi.FieldStorage()
Username = form.getvalue('id')
Password = form.getvalue('password')

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
hostname = "140.112.30.32"
port = 22

success = ssh.Connect(hostname,port)
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

#  Here are some examples of command lines for <b>Windows SSH servers</b>:

#  Get a directory listing:
# cmd1 = "dir"
cmd1 = "ls"

#  Do a nameserver lookup:
# cmd2 = "nslookup chilkatsoft.com"
cmd2 = "cd htdocs"

#  List a specific directory.  Given that the shell is CMD.EXE, backslashes must
#  be used:
# cmd3 = "dir \\temp"
cmd3 = "ls"

#  Execute a sequence of commands.  The syntax for CMD.EXE may be found
#  here: http://technet.microsoft.com/en-us/library/bb490880.aspx.  Notice how the commands
#  are separated by "&&" and the entire command must be enclosed in quotes:
# cmd4 = "\"cd \\temp&&dir\""
cmd4 = "cd print"

#  Here are two examples of command lines for <b>Linux/UNIX SSH servers</b>:

#  Get a directory listing:
# cmd5 = "ls -l /tmp"
cmd5 = "ls"

#  Run a series of commands (syntax may depend on your default shell):
# cmd6 = "cd /etc; ls -la"

#  Request a directory listing on the remote server:
#  If your server is Windows, change the string from "ls" to "dir";
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
# Don't print the whole thing out b/c it's html
# We have to parse the response
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

#  Disconnect
ssh.Disconnect()

# cgi script requirements
print "</body></html>"

