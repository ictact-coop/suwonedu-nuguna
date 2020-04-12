# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "mozodev/xenial64-anyenv"
  config.vm.hostname = "nuguna"
  config.vm.network "private_network", ip: "192.168.37.11"
  config.vm.network "forwarded_port", guest: 8888, host: 8888, id: "php"
  config.vm.synced_folder ".", "/vagrant", type: 'nfs', fsnotify: true

  config.vm.provider "virtualbox" do |vb|
    vb.name = "nuguna"
    vb.cpus = 2
    vb.memory = "2048"
    vb.linked_clone = true
  end

  config.vm.provision 'shell', keep_color: true, privileged: false, inline: <<-SHELL
    echo "[php] add dev config";
    cp /vagrant/config/php-dev.ini ~/.anyenv/envs/phpenv/versions/5.6.40/etc/conf.d/
    mkdir -p /vagrant/docroot/files/config
    cp /vagrant/config/db.config.php /vagrant/docroot/files/config/

    echo "[mysql] copy default database to host, config to guest and start!"
    echo 'export PATH="/home/vagrant/opt/mysql/5.7.26/bin:$PATH"' >> ~/.bash_profile && source ~/.bash_profile
    mkdir -p /vagrant/dump/mysql_5_7_26
    sudo cp -r ~/sandboxes/msb_5_7_26/data/* /vagrant/dump/mysql_5_7_26/
    cp /vagrant/config/my.sandbox.cnf /vagrant/config/sb_include ~/sandboxes/msb_5_7_26/
    ~/sandboxes/msb_5_7_26/start
    ~/sandboxes/msb_5_7_26/use -e 'drop database test; create database nuguna;'
  SHELL

  # https://www.vagrantup.com/docs/vagrantfile/vagrant_settings.html#config-vagrant-plugins
  config.vagrant.plugins = "vagrant-fsnotify"
  # https://github.com/adrienkohlbecker/vagrant-fsnotify
  config.trigger.after :up do |t|
    t.name = "vagrant-fsnotify"
    t.run = { inline: "vagrant fsnotify" }
  end

end
