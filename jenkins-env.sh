#!/bin/bash

# Base env
PROJECT='webrsa'
ZULIP_STREAM='webrsa'
NEXUS_REPO_PREFIX='atolcd-webrsa'
TYPE=$(git rev-parse $(jq -r ".version" composer.json) > /dev/null 2>&1 && echo "snapshot" || echo "release")
VERSION=$(git rev-parse $(jq -r ".version" composer.json) > /dev/null 2>&1 && echo "$(git describe)" || echo "$(jq -r ".version" composer.json)")

# Compose env
NEXUS_REPO="$NEXUS_REPO_PREFIX-$TYPE"
APP_FILENAME_TAR="webrsa_${VERSION}.tgz"

case "$1" in
    PROJECT)                 echo $PROJECT; exit 0;;
    ZULIP_STREAM)            echo $ZULIP_STREAM; exit 0;;
    NEXUS_REPO)              echo $NEXUS_REPO; exit 0;;
    TYPE)                    echo $TYPE; exit 0;;
    VERSION)                 echo $VERSION; exit 0;;
    APP_FILENAME_TAR)        echo $APP_FILENAME_TAR; exit 0;;
esac

exit 1