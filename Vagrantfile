# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

$install_requirements = <<SCRIPT
sudo apt-get update

sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'

sudo apt-get install -y vim curl python-software-properties
sudo add-apt-repository -y ppa:ondrej/php5
sudo apt-get update

sudo apt-get install -y php5 apache2 libapache2-mod-php5 php5-curl php5-gd php5-mcrypt php5-readline mysql-server-5.5 php5-mysql git-core php5-xdebug beanstalkd supervisor

cat << EOF | sudo tee -a /etc/php5/mods-available/xdebug.ini
xdebug.remote_enable = on
xdebug.remote_connect_back = on
xdebug.idekey = "vagrant"
xdebug.scream=1
xdebug.cli_color=1
xdebug.show_local_vars=1
EOF

mysql -u root -proot -e"grant all on *.* to root@'192.168.33.1' IDENTIFIED BY 'root'" mysql

sudo a2enmod rewrite

sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php5/apache2/php.ini
sed -i "s/display_errors = .*/display_errors = On/" /etc/php5/apache2/php.ini
sed -i "s/disable_functions = .*/disable_functions = /" /etc/php5/cli/php.ini
sed -i "s/#START=yes/START=yes/" /etc/default/beanstalkd
sed -i "s/127.0.0.1/0.0.0.0/" /etc/mysql/my.cnf

sudo service mysql restart
sudo service apache2 restart
sudo service beantstalkd start
sudo service supervisor start

curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

echo "Australia/Sydney" | sudo tee /etc/timezone

echo "cd /vagrant" >> /home/vagrant/.bashrc

cd /vagrant
composer install

echo "192.168.33.10 services.dev" | sudo tee -a /etc/hosts
echo "192.168.33.11 topbetta.dev" | sudo tee -a /etc/hosts
echo "192.168.33.12 serena.dev" | sudo tee -a /etc/hosts
echo "192.168.33.13 risk.dev" | sudo tee -a /etc/hosts
echo "192.168.33.14 puntersclubapi.dev" | sudo tee -a /etc/hosts

SCRIPT

$vhost_setup = <<SCRIPT
VHOST=$(cat <<EOF
<VirtualHost *:80>
  DocumentRoot "/vagrant/public"
  ServerName localhost
  ServerAlias  services.dev
  <Directory "/vagrant/public">
    AllowOverride All
    Require all granted
  </Directory>
</VirtualHost>
EOF
)
echo "${VHOST}" > /etc/apache2/sites-enabled/000-default.conf

sudo service apache2 restart

SCRIPT

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.provision "shell", inline: $install_requirements
  config.vm.provision "shell", inline: $vhost_setup
  config.vm.box = "precise64"
  config.vm.box_url = "http://files.vagrantup.com/precise64.box"
  config.vm.network "forwarded_port", guest: 80, host: 8081
  config.vm.synced_folder ".", "/vagrant", :mount_options => ["dmode=777","fmode=666"]
  config.vm.network "private_network", ip: "192.168.33.10"
end
