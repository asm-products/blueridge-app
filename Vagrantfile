Vagrant.configure("2") do |config|
  config.vm.provider "virtualbox" do |v|
    v.name = "blueridge"
  end
  config.vm.box = "devbox"
  config.vm.box_url = "http://static.techeffe.net/vagrant/ubuntu-12.04.2-server-amd64-dist.box"

  config.vm.synced_folder "./", "/var/www/blueridgeapp"
  config.vm.synced_folder "devbox/conf.d", "/etc/apache2/sites-available"
  config.vm.synced_folder "devbox/logs", "/var/log/apache2"


  config.vm.network :public_network
  config.vm.network :forwarded_port, guest: 80, host: 8080
  config.vm.network :forwarded_port, guest: 27017, host: 27017

  config.vm.provision :chef_solo do |chef|
    chef.cookbooks_path = "devbox/cookbooks"    
    chef.add_recipe "mospired"
    chef.json = {
      "app" => {
        "php_timezone" => "America/New_York"
        }
      }
    end
  end
