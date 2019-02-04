#!/bin/sh

mkdir -p dist
rm -rf dist/*
rsync -av backend-php/ dist/api/ --exclude vendor
cd dist/api/
composer install --no-dev
cd .. && cd ..
cd frontend/
npm i && ember build --environment=production
cd ..
cp -rf frontend/dist/* dist/
cp -f .htaccess dist/
