#!/bin/sh
. $(dirname $0)/common.sh
apkg_logreset "$@"
app_install $1 $2