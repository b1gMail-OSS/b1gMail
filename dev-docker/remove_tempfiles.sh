#!/bin/bash
rm -Rf ../src/data
mkdir ../src/data
touch ../src/data/.htaccess
touch ../src/data/index.html
rm -Rf ../src/temp
mkdir ../src/temp
mkdir ../src/temp/cache
mkdir ../src/temp/session
touch ../src/temp/.htaccess
touch ../src/temp/index.html
touch ../src/temp/cache/dummy
touch ../src/temp/session/dummy
tee ../src/temp/.htaccess <<EOF
<IfModule mod_authz_core.c>
	Require all denied
</IfModule>

<IfModule !mod_authz_core.c>
	Deny from all
</IfModule>
EOF
tee ../src/data/.htaccess <<EOF
<IfModule mod_authz_core.c>
	Require all denied
</IfModule>

<IfModule !mod_authz_core.c>
	Deny from all
</IfModule>
EOF
