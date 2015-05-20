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

> If the browser shows "404 phpmyadmin not found", 

> try this command: `sudo ln -s /usr/share/phpmyadmin /var/www/html/`

## Step3: Install Python Package: Chilkat

> Download through the official site: [Link Here!](https://www.chilkatsoft.com/python.asp)

> Please download the proper version of Chilkat corresponding to your python version.

> You can check your python version by: `$python --version`

**Unpack the .tar.gz file** (ex. chilkat-9.5.0-python-2.7-x86_64-linux.tar.gz)

```sh
$tar zxvf chilkat-9.5.0-python-2.7-x86_64-linux.tar.gz
```

**Get yourself into that directory**
```sh
$cd chilkat-9.5.0-python-2.7-x86_64-linux/
```

**Put some files under site_package directory of your python** (ex. /usr/lib/python2.7/dist-packages)

```sh
$sudo cp _chilkat.so chilkat.py /usr/lib/python2.7/dist-packages/
```

> How to find site_package directory of your python?

> First, run python interactive command line in terminal by `$python`

> And then `>>>import site; site.getsitepackages()`

## Step4: configure Apache server to run python scripts

**Enable Apache cgi**
```sh
$sudo a2enmod cgi
```
**The conf file**
```sh
$sudo vim /etc/apache2/sites-enabled/000-default.conf
```
> Add the following line after `DocumentRoot /var/www/html`:

```
ScriptAlias /cgi-bin/ /var/www/html/cgi-bin/
```

> Now we can put our cgi codes inside /var/www/html/cgi-bin/

