# Production Provision/Deployment Info

## Provisioning
The provisiong script is ansible which is python. To run this you need python, preferrably python3
installed with python3-pip or "pip". I recommend you install a virtualenvironment as well but you can
also just install ansible with `pip install ansible` globally if you have python3/pip installed.


### SSH Config
The way that ansible connects to the host(s) for production is that it utilizes a common ssh 
config that I created. Right now all production systems have animal tags as sequential tags did
not make sense. Ideally the production instances would be considered ephemeral. This means that
you may take one down and prop a new one up with Ubuntu 22.04 one day to test out the next LTS.
Or maybe you decide to switch it out to CentOS, or god forbid IIS. You can do so more incrementally
and name this next box another random animal name. Once that is stable you can start decomissioning
old boxes. This makes it safer to update. The load balancer, AWS ELB, will perform health checks and if
the instance goes down or does not respond well to the health check (which you can also define in AWS console)
then it will not direct traffic there. 

Make sure you have a file, called `~/.ssh/config`. In this file append to it these values for deployment to production


```
Host host-name-here
    hostname 127.0.0.1
    user ubuntu
    AddKeysToAgent yes
    ForwardAgent yes
    identityFile ~/.ssh/privatekey.pem
```

Note that there is a `super` and `deploy` suffix for the appropriate actions. When you administer the configuration of 
the server you can use the super suffix one to do privileged maintenance on the box. Also note that there
is a key, some_key_prod.pem, which is the key for the main ubuntu user on the box. There is also a seperate username-prod-deploy.pem
that is used for deployments. The nginx service, php-fpm service and application files all are owned by the `username`
user on the server(s). When and if you add a new animal named server, please update this config accordingly. To get the SSH keys
I will place them for now in this repo, but if you ever for some reason make this repo public, please make 100% certain that you
rev-parse or whatever is need to completely remove all traces from the history of these files. For now they are in the 
`provision/ssh-keys`. Note do not rename them as currently there is a provision action that copies the deploy keys to the
 user on the servers by the host name/file name. ssh-add the .pem keys. Make sure to use -K on OSX only to add to keychain.
This also enables the deployment script to utilize your keys for github on the server without having to transfer them. This is
called "SSH Agent Forwarding".


### Running the provision scripts
These scripts are idempotent and can be used as many times as you want in a row without breaking server. If anything
you may rely on them to bring the server back to a running state. It will skip over any tasks that do not need to be run
 by checking the current state of the server before running those tasks. This does not need to be ran though now that the servers
are already up. Only if you need to create fresh servers.

1. The first step which unfortunately is manual for now is creating the user on the server if they do not exist.
If they exist skip to #2. Use `useradd -G www-data username` to add the user. Do this from a super user or ubuntu with sudo.

2. The second step in creating a fresh server is to deploy to the server. See information below on how to do that.

3. Third step is to run the deployment script. This should "just work" if you followed all above information

`ansible-playbook -i provision/config/production -l production provision/playbook.yml`

sit back and wait. Everything should "just work". If you have added a server, make sure to edit the `provision/production`
file at the top to add more servers to the provision script. For now it provisions 2 servers at a time.


### Running deployment scripts
This project uses [Deployer](https://deployer.org). Head there and install their phar file as they instruct. The end goal
is to have a `dep` command. Once you have installed it go to the root of this project in terminal and run

`dep deploy production`

this will deploy to the `production` 'stage'. You will need to say `Y` 1 time for each server before the script starts. Add
`-v` or `-vvv` to debug in times of errors.


### Notes
The user account is allowed to restart php8.0-fpm.service and nginx.service. It can also do reloads. You will need to 
run the full command to make it work as it has to be exactly as it is configured in the sudoers file.

`sudo /usr/bin/systemctl reload nginx.service` for instance or `sudo $(which systemctl) restart nginx` even.
