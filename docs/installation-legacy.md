# Installation - Legacy

These instructions are for folks who might be running < `1.3.0` of `silverstripeltd/dev-ssp`

Prepare your Vagrantfile
```
$ cp Vagrantfile.dist Vagrantfile
```

Open up your `Vagrantfile`.

Uncomment and update the follow two lines:

```
# config.vm.network "private_network", ip: "192.168.33.4"
# config.vm.hostname = "myproject.test"
```

Update your `/etc/hosts` file if required.

```
$ sudo vi /etc/hosts

# Add line
192.168.33.4   myproject.test
```

Build your VM.
```
$ vagrant up
```
