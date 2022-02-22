# Installation - Upgrade

As of `1.3.0`, a few of our VM configs are now stored in our `.env` configuration, rather than in our `Vagrantfile`.

Open your `Vagrantfile` and check to see if you have the following two configs uncommented.

```
config.vm.network "private_network", ip: "192.168.33.4"
config.vm.hostname = "myproject.test"
```

These two settings need to be remove/commented out from your `Vagrantfile` and moved into your `.env` file.

Open your `.env` and add/update the following.

```
# Vagrant Settings
IP_ADDRESS=192.168.33.4
HOSTNAME=myproject.test

# Optional settings below
#VM_MEMORY=2048
#VM_CPU=2
# Default disk size is 120GB
#VM_DISK_SIZE=140
```

You should now be good to go.
