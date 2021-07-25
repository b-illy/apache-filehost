# apache-filehost

## Overview
A PHP-based filehost running under an apache2 webserver

This filehost is designed to be invite-only and so uses access keys and includes detailed logging


## Setup
To setup the filesystem, run `sudo bash init.sh`

To add keys, run `sudo python3 generate_key.py` (AFTER setting up filesystem) - make sure to edit this script if not using /var/www/html
