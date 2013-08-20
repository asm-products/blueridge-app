Vagrant.configure("2") do |config|
  config.vm.hostname = "blueridgeapp"
  config.vm.box = "ninelabs-devbox"
  config.vm.box_url = 'ninelabs-devbox'#{"http://static.techeffe.net/vagrant/ubuntu-12.04.2-server-amd64-dist.box"
  config.vm.synced_folder "./", "/var/www/blueridgeapp",owner: "www-data", group: "www-data"
  config.vm.synced_folder "devbox/conf.d", "/etc/apache2/sites-available",owner: "root", group: "root"
  config.vm.synced_folder "devbox/logs", "/var/log/apache2"

  #config.vm.network :public_network
  config.vm.network :private_network, ip: "33.33.33.40"
  config.vm.network :forwarded_port, guest: 80, host: 8080
  config.vm.network :forwarded_port, guest: 27017, host: 27017
  config.vm.provision :shell, :path => "devbox/init.sh"
end