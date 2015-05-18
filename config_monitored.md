# Configuration of the machines to be monitored

+ [back to README.md](./README.md)

> This configuration is written for Ubuntu 14.04 LTS

> The steps may differ according to different Operating Systems

> Please see to this!

## Step1: Install SSH server

**Install the SSH server with the following command**

```sh
$sudo apt-get install ssh
```

## Step2: modify the config file for SSH server

**Edit the file at /etc/ssh/sshd_config**

```sh
$sudo vim /etc/ssh/sshd_config
```

(if you don't have vim: install vim by `$sudo apt-get install vim`)

**Make sure the two following settings are set to "yes"**

```sh
StrictModes yes
PubkeyAuthentication yes
```

## Step3: run the SSH server

**To check if SSH server is running**

```sh
$sudo service ssh status
```

**To start the SSH server**

```sh
$sudo service ssh start
```

**To stop the SSH server**

```sh
$sudo service ssh stop
```

**To restart the SSH server**

```sh
$sudo service restart
```

## Step4: test your SSH server

**Use the following command on another machine to establish connection**

```sh
$ssh -l [username] [hostname]
```

(you should be required to enter password and connect successfully)




