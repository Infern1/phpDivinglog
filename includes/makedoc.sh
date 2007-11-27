#!/usr/local/bin/bash

TITLE="phpDivinglog documentation"
PACKAGES="phpdivinglog"

PATH_PROJECT=$PWD
PATH_DOCS=$PWD/doc
PATH_PHPDOC=phpdoc
IGNORE=$PWD/includes/jpgraph/*,jpg*,jpgraph*,*smarty*,*pear*,*gd_image*,*imgdata*,*prod.inc.php*

OUTPUTFORMAT=HTML
CONVERTER=frames
TEMPLATE=earthli
PRIVATE=off

# make documentation
$PATH_PHPDOC -d $PATH_PROJECT -t $PATH_DOCS -ti "$TITLE" -dn $PACKAGES \
-i $IGNORE -o $OUTPUTFORMAT:$CONVERTER:$TEMPLATE -pp $PRIVATE

