# -*- mode: ruby -*-
# vi: set ft=ruby :

PUPPET_PATH = ENV['PUPPET_PATH']

Vagrant.configure("2") do |config|
  config.vm.box = "centos-7-puppet5.box"
  config.vm.box_url = [ "http://nexus3.priv.atolcd.com/repository/atolcd-vagrant/centos-7-puppet5.box", "https://vagrant-mirror-ovh.priv.atolcd.com/centos-7-puppet5.box" ]
  config.vm.host_name = "atolcd-webrsa-demo.hosting.priv.atolcd.com"
  config.vm.network :forwarded_port, guest:   80, host: 8080
  config.vm.synced_folder PUPPET_PATH + "/hieradata", "/var/lib/puppet/environments/vagrant/hieradata", create: true
  config.vm.synced_folder ".", "/var/lib/puppet/environments/vagrant/hieradata/vagrant", create: true
  config.vm.synced_folder ".", "/var/www/66test/public_html", create: true
  config.vm.synced_folder ".", "/var/www/58test/public_html", create: true
  config.vm.synced_folder ".", "/var/www/93test/public_html", create: true
  
  config.vm.provider :virtualbox do |vb|
    vb.memory = 1024
    vb.customize ["modifyvm", :id, "--cpus", "2"]
    vb.name = "atolcd-webrsa-demo"
  end
  config.vm.provision :puppet do |puppet|

    puppet.hiera_config_path = PUPPET_PATH + "/hiera.yaml"
    puppet.working_directory = "/etc/puppetlabs"

    puppet.environment_path  = PUPPET_PATH
    puppet.environment       = "vagrant"
    puppet.facter            = {
      "application" => "default"
    }
  end

end