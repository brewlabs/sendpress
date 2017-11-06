#! /bin/sh
for file in `/usr/bin/find . -name '*.po'` ; do /usr/local/opt/gettext/bin/msgfmt -o ${file/.po/.mo} $file ; done