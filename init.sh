
# Ask for the administrator password upfront
sudo -v

# Keep-alive: update existing `sudo` time stamp until `init` has finished
while true; do sudo -n true; sleep 60; kill -0 "$$" || exit; done 2>/dev/null &

sudo chmod -R 0777 data/

cd scripts/

npm install

cd ../php

composer install
