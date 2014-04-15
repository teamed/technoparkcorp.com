#!/bin/bash

add-apt-repository ppa:resmo/git-ftp
aptitude update
aptitude install git-ftp

git-ftp
