#!/bin/sh
cd ../
rm -rf build/packaging && mkdir build/packaging
rm -rf build/packages && mkdir build/packages
cp -r plugins/installer/webinstaller/* build/packaging
cp -r media/plg_installer_webinstaller build/packaging/media
cd build/packaging
zip -r ../packages/plg_installer_webinstaller.zip .
