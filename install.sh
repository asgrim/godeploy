#!/bin/bash -x
# GPLv3 License - see LICENSE
# Copyright (C) 2011 - James Titcumb
# Official GoDeploy setup script

# Prerequisite - must be root
# Why? So we can chgrp later. Must be a better way of doing this?
if [ $UID != 0 ]
then
  echo "This script must be run as root."
  exit 1
fi

# Prerequisite - git
hash git 2>&- || { echo >&2 "git not installed and is a requirement for GoDeploy. Aborting installation."; exit 1; }

# Capture install location
DEFAULT_INSTALL=`pwd`
read -p "Install directory (e.g. entering '/opt' will install to '/opt/godeploy') [default: $DEFAULT_INSTALL]: " -e inputA
if [ -n "$inputA" ]
then
  SETUP_DIR="$inputA"
else
  SETUP_DIR="$DEFAULT_INSTALL"
fi

# Determine apache's group
if [ -f /etc/debian_version ]; then
  DEFAULT_APACHE_GRP=www-data
else
  DEFAULT_APACHE_GRP=apache
fi
read -p "Apache's run group [default: $DEFAULT_APACHE_GRP]: " -e inputB
if [ -n "$inputB" ]
then
  APACHE_GRP="$inputA"
else
  APACHE_GRP="$DEFAULT_APACHE_GRP"
fi

# Change into the install directory
cd $SETUP_DIR

# Git clone the initial repo (easy updating in future) and checkout stable branch
echo -n "Installing application..."
git clone --quiet git://github.com/asgrim/godeploy.git godeploy
cd godeploy
git checkout -b master origin/master > /dev/null 2>&1
git branch -D develop > /dev/null 2>&1
cd ..
echo "Done."

# Download Zend + MAL
echo -n "Downloading libraries..."
cd godeploy/library
wget --quiet https://github.com/downloads/asgrim/godeploy/gd-libraries.tgz
# TODO - check the libraries downloaded OK (sha sum or something maybe?)
cd ../..
echo "Done."

# Extract Zend + MAL
echo -n "Extracting libraries..."
cd godeploy/library
tar zxf gd-libraries.tgz
rm gd-libraries.tgz
cd ../..
echo "Done."

# Cleanup operations
echo -n "Setting permissions and creating cache directory..."
mkdir godeploy/gitcache
chgrp -R $APACHE_GRP godeploy
chmod 775 godeploy/gitcache
chmod 775 godeploy/application/configs
echo "Done."

echo "You now need to point your Apache virtual host to $SETUP_DIR/godeploy/public"
