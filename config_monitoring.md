# Configuration of the machine to monitor

+ [back to README.md](./README.md)

> This configuration is written for Ubuntu 14.04 LTS

> The steps may differ according to different Operatin Systems

> Please see to this!

## Step1: Install LAMP environment

**To install Apache**
```sh
$sudo apt-get install apache2
```
**To install MySQL**
```sh
$sudo apt-get install mysql-server
```

**To install PHP**
```sh
$sudo apt-get install php5 libapache2-mod-php5 php5-mcrypt
```

**Restart Apache server**
```sh
$sudo /etc/init.d/apache2 restart
```
(you can now check `http://localhost/` to see if your apache server works!)

## Step2: Install PHPMyAdmin

```sh
$sudo apt-get install phpmyadmin
```

(after restarting apache server by `$sudo service apache2 restart`, check out `http://localhost/phpmyadmin`)


