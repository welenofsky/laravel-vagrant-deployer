Using the ansible provisioning script
=================================================

## Required Packages

* python 3 / pip (python3)
* Python virtualenv (python3-venv)
* Vagrant
* Virtualbox
* php 8.0+
* deployer

## Configuration file

### config/
Edit the config files for the respective environment. The files live in config folder. They should be self-explanatory. These files are for currently only contain the DB credentials and variables used for provisioning (like for the home folder name). For local virtualmachine the local config is already created and requires no edits

## Setup

To setup just bring the box up with dep box:build
```
dep box:build
```
Now you should have a development environment with its database setup and configured to allow connections from your host machine.


## How To

### How do I access this box?
It uses the static IP address `10.0.3.29`. To SSH into the box use
```
vagrant ssh
```
I also recommend setting a domain name for this box like mvs.test in your /etc/hosts file

### How can I remove this box
```
dep box:destroy
```

### How to see mail sent from this server?
This server uses Mailcatcher. So just go to the IP of the box in your web browser and append the port :1080 to it. For example. sitename.test:1080


### Is SSL Setup?
Not at the moment because I am working on the SSL differences between prd and local atm. If you need ssl though its as easy as uncommenting the ssl block in the apache task file in this directory under the apache folder. Or contact JW.

### What about DB access
Use your favorite database client from your host machine. Mine at the moment is DBeaver Community Edition. You can connect to this MySQL 8 server using the credentials in the local config file. When we deploy to production though this is not the case and access is disabled remotely for DB.

