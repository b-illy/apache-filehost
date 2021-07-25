echo "NOTE - make sure not to put any unrelated files in this directory"

if [ "$EUID" -ne 0 ]; then
	echo "Re-run me as root"
	exit
fi

echo -n "Do you want to set this directory up as the webserver root? (y/n) "
read choice

if [ "$choice" != "y" ]; then
	echo -n "Enter desired directory (e.g. /var/www/html): "
	read dir
	mkdir -p $dir && mv * $dir && cd $dir
fi

# make sure permissions set properly on all files
chown -f root:root *
chmod -f 774 *

# create directories/files
mkdir -p {data,uploads}
touch data/{keys,log}.txt

# set correct permissions for newly created directories/files
chown -R -f www-data:www-data {data,uploads}
chmod -R -f 770 {data,uploads}

# move .htaccess files to correct locations
mv -f htaccess-root.htaccess 	  .htaccess
mv -f htaccess-data.htaccess 	  data/.htaccess
mv -f htaccess-uploads.htaccess   uploads/.htaccess

echo "Done!"
