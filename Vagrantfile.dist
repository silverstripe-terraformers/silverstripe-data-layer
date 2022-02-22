# Based on defaults for SilverStripe Ltd projects.
# See https://silverstripe.atlassian.net/wiki/spaces/DEV/pages/401506576.

Vagrant.configure(2) do |config|
  # Webroot defaults.
  # Don't change this to public/, it will be auto-detected in the box
  WEBROOT_HOST = "."
  WEBROOT_GUEST = "/var/www/mysite/www"

  # Change this IP to avoid clashes with other running virtual machines
  # on your own host machine virtual network
  # LEGACY BOXES ONLY (< 1.3.0)
  # config.vm.network "private_network", ip: "192.168.33.4"

  # Change to a unique host name.
  # Sets automatically when using the vagrant-hostsupdater plugin.
  # Use a *.vagrant top level domain to get built-in SSL certificates
  # LEGACY BOXES ONLY (< 1.3.0)
  # config.vm.hostname = "myproject.test"

  # Handy for subsites
  #config.hostsupdater.aliases = ["mysubsite1.test", "mysubsite2.test"]

  config.vm.box = "silverstripeltd/dev-ssp"

  # Disable default mount /vagrant
  config.vm.synced_folder WEBROOT_HOST, "/vagrant", disabled: true

  # Configure webroot and mount options
  # See https://github.com/gael-ian/vagrant-bindfs
  if Vagrant.has_plugin?("vagrant-bindfs") then
    # Useful for OSX (for optimal performance)
    config.vm.synced_folder WEBROOT_HOST, "/vagrant-nfs", type: "nfs"
    config.bindfs.bind_folder "/vagrant-nfs", WEBROOT_GUEST,
    force_user:   'vagrant',
    force_group:  'vagrant',
    perms:        'u=rwX:g=rD:o=rD',
    o:            'nonempty'
  else
    # For Windows and Linux
    config.vm.synced_folder WEBROOT_HOST, WEBROOT_GUEST, type: "nfs"
  end

  # Update memory settings for Virtualbox
  # See https://www.vagrantup.com/docs/virtualbox/configuration.html#vboxmanage-customizations
  # Needs additional config for other providers, see https://www.vagrantup.com/docs/providers/
  config.vm.provider "virtualbox" do |v, override|
    v.memory = 2048
    v.cpus = 2
    # Reduce disk space by cloning from master VM
    # See https://www.vagrantup.com/docs/virtualbox/configuration.html#linked-clones
    v.linked_clone = true
  end

  # For developers using Parallels provider
  config.vm.provider "parallels" do |prl, override|
    prl.memory = 2048
    prl.cpus = 2
  end

  # Optional apt and composer cache (shared beween boxes)
  # See https://github.com/fgrehm/vagrant-cachier
  if Vagrant.has_plugin?("vagrant-cachier")
    config.cache.scope = :box
    config.cache.enable :apt
    config.cache.enable :composer
    config.cache.enable :npm
  end

  # Forward SSH agent, important for private git checkouts
  config.ssh.forward_agent = true

  # Switch to 7.4
  # config.vm.provision "shell", inline: "sudo php-switch 7.4"
end
