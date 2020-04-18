#!/usr/bin/env bash
sudo apt-get remove docker-compose
sudo apt-get install python3-pip
sudo apt-get install libffi-dev
sudo -H pip install docker-compose
