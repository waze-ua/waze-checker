#!/bin/sh
. ./build.conf

mkdir -p dist
rm -rf dist/*
rsync -r backend-php/ dist/api/ --exclude vendor
cd dist/api/
composer install --no-dev

#database config
sed -i "/'hostname' =>/c 'hostname' => '$hostname'," application/config/database.php
sed -i "/'username' =>/c 'username' => '$username'," application/config/database.php
sed -i "/'password' =>/c 'password' => '$password'," application/config/database.php
sed -i "/'database' =>/c 'database' => '$database'," application/config/database.php
sed -i "/'dbdriver' =>/c 'dbdriver' => '$dbdriver'," application/config/database.php

#console utility config
sed -i "/private \$country =/c private \$country = $countryId;" application/controllers/WmeData.php

cd .. && cd ..
cd frontend/
npm i && ember build --environment=production
cd ..
cp -rf frontend/dist/* dist/
cp -f .htaccess dist/

#userscript
userscript="wme-checker-$identifier.js"
cp -f userscript.js dist/"$userscript"
  
sed -i "/\/\/ @name/c \/\/ @name  WME $identifier checker" dist/"$userscript"
sed -i "/\/\/ @description/c \/\/ @description  $identifier checker" dist/"$userscript"
sed -i "/\/\/ @downloadURL/c \/\/ @downloadURL  $url\/$userscript" dist/"$userscript"
sed -i "/\/\/ @namespace/c \/\/ @downloadURL  $url\/$userscript" dist/"$userscript"
sed -i "/const url =/c const url = '$url\/'" dist/"$userscript"
sed -i "/const identifier =/c const identifier = '$identifier'" dist/"$userscript"
sed -i "/const countryID =/c const countryID = '$countryId'" dist/"$userscript"