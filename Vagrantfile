Vagrant.configure("2") do |config|
  config.vm.hostname = "blueridge"
  config.vm.box = "ninelabs-devbox"
  config.vm.box_url = '~/Projects/_lib/ninelabs-devbox'
  config.vm.synced_folder "./", "/var/www/blueridge",owner: "www-data", group: "www-data"
  config.vm.synced_folder "devbox/conf.d", "/etc/apache2/sites-available",owner: "root", group: "root"
  config.vm.synced_folder "devbox/crt", "/etc/ssl/crt",owner: "root", group: "root"
  config.vm.synced_folder "devbox/logs", "/var/log/apache2"

  #config.vm.network :public_network
  config.vm.network :private_network, ip: "33.33.33.40"
  config.vm.network :forwarded_port, guest: 80, host: 8888
  config.vm.network :forwarded_port, guest: 27017, host: 27019
  config.vm.provision :shell, :path => "devbox/init.sh"
end