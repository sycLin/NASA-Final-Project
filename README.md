# NASA-Final-Project

Final project of NASA course.

Network Administration and System Administration, NTU CSIE.

## Topic

監控機器服務架設

## Deadline

> 6/22 Final Project Presentation

## Goals

- [x] Construct two normal VMs, say VM1 and VM2.
- [x] Build a web server on another (the 3rd) VM to display all process info of VM1 and VM2.
- [ ] Users can kill processes of VM1 and VM2 through the admin page of web server.
- [ ] Zombie processes should be killed automatically and display what have been killed on the webpage.

## Useful Links

* [Setup SSH in Linux system](http://docs.oracle.com/cd/E18930_01/html/821-2426/gksja.html#gksrd)
* [Python SSH tools](https://wiki.python.org/moin/SecureShell)
* [Chilkat](https://www.chilkatsoft.com/python.asp)

## Configuration

* [Setup machines to be monitored](./config_monitored.md)
* [Setup machines to monitor](./config_monitoring.md)

## Workload Distribution (TODO)

|Done?|Job|Who?|Description|
|:-----------------|:--------|:---:|:-------|
|***YES***|Environment Setup|Steven|intall packages and servers|
|***YES***|SSH Tunnel Connection|Steven|establish ssh connection|
|***YES***|PHP code|Steven|for rendering webpages based on templates|
|***YES***|HTML templates|Steven|provide all webpage templates|
|***NO***|CSS styling|Brian|css styling for webpages|
|***NO***|Zombie Problem|冠廷|*no need to implement for now*|
|***NO***|Other functionality|冠廷|If any?|



