# Configuration of the machines to be monitored

+ [back to README.md](./README.md)

> this configuration is written for Ubuntu 14.04 LTS

> the steps may differ according to different Operating Systems

> Please see to this!

## Step1: Install SSH server

**Install the SSH server with the following command**
`sudo apt-get install ssh`

## Step2: modify the config file for SSH server

**Edit the file at /etc/ssh/sshd_config**
`sudo vim /etc/ssh/sshd_config`
(if you don't have vim: install vim by `sudo apt-get install vim`)

**Make sure the two following settings are set to "yes"**
```
StrictModes yes
PubkeyAuthentication yes
```
## Step3: run the SSH server

**To check if SSH server is running**
`sudo service ssh status`

**To start the SSH server**
`sudo service ssh start`

**To stop the SSH server**
`sudo service ssh stop`

**To restart the SSH server**
`sudo service restart`

## Step4: test your SSH server

**Use the following command on another machine to establish connection**
`ssh -l [username] [hostname]`
(you should be required to enter password and connect successfully)




