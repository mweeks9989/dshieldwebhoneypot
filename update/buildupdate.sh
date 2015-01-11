#!/bin/sh
#
# little script that is run on the main web server to build updates. Included for 
# simplicity here and not actually needed for the client
#

cd ..
svn update
tar czvf honeypot.tgz --exclude .svn docs/* html/* lib/* update/*
sha1sum honeypot.tgz | cut -f 1 -d' ' > honeypot.sha1
tar czvf webhoneypot.tgz --exclude .svn docs etc html lib logs templates update
sha1sum webhoneypot.tgz | cut -f 1  -d' ' > webhoneypot.sha1
cp webhoneypot* /home/live/isc/html
