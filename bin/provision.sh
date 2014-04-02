#!/bin/bash

if [ `whoami` != "root" ]; then
	echo "You must be root to provision this";
	exit;
fi

aptitude update

# Go to this script directory
cd `cd -P "$( dirname "$0" )" && pwd`

# obtain a top level git
cd `git rev-parse --show-toplevel`

echo 'export APPLICATION_ENV=local' > /etc/profile.d/application-environment.sh

./bin/provision/db