USER=$( id -un )
GROUP=$( id -gn )

sudo chown -R $USER:$GROUP owncloud_apps

