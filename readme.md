BlueRidge
============

## Overview

This dev box is a feature rich PHP, Node development env.
It provides a quite good number of tools and libraries and provisioned by **Vagrant** via *Chef*.
## Client
	
 - Angularjs, Restangular, NgTables, Stripe, UI Bootstrap, POSH 

## Server

 - Apache 2, node.js
 - PHP (5.4), Composer, PHPUnit, xDebug
 - Memcached, MongoDB (10gen), SQLite
 - NFS, GhostScript, ImageMagick, Vim, Ruby (Gems: Compass, Less, SASS)
 - Composer, Slim,PHPMailer,Stripe
 
 ## Getting Started.

 1. [VirtualBox](http://www.virtualbox.com) version 4.2.12 works best as of this writing 4.2.14 fails
 
 2. [Vagrant](http://vagrantup.com) version 1.2.2 

 3. Open a **Terminal**, cd to the devbox directory inside your working directory 
 	' cd blueridgeapp/devbox '

 4. Run `vagrant up` to provision it, should take a few minutes.

 5. ssh into the box `vagrant ssh` switch to root user and cd into the project root and fire off composer.
 		
 		$ sudo su
 		$ cd /var/www/bluridgeapp
 		$ composer update

 6. exit out of the shell and add the site hosts entry to your hosts file (`sudo vim /etc/hosts`)
 	`10.0.0.10 dev.blueridgeapp.com`
