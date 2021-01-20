# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"


Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config.vm.box = "generic/ubuntu2004"

  config.vm.network "private_network", ip: "10.0.3.29"

  config.ssh.insert_key = false
  config.ssh.private_key_path = ["~/.vagrant.d/insecure_private_key"]

  config.vm.synced_folder "./", "/home/vagrant/src",
    create: true,
    owner: "www-data",
    group: "www-data",
    mount_options: ["dmode=775,fmode=775"]

  config.vm.provider :virtualbox do |vb|
    vb.customize ["modifyvm", :id, "--memory", "2048"]
  end

  config.vm.provision "ansible" do |ansible|
    ansible.playbook = "provision/playbook.yml"
    ansible.inventory_path = "provision/config/local"
    ansible.limit = "local"
    ansible.compatibility_mode = "2.0"
  end

end
