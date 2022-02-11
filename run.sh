#!/bin/sh
set -e

IMAGE="thecodingmachine/php:8.0-v4-cli"
WORKDIR=$(dirname "$(realpath "$0")")
ENVIRONMENTS="-e PHP_EXTENSION_XDEBUG=1 -e XDEBUG_MODE=coverage"

docker run --rm -it --name "http-feeds-server" -v "$WORKDIR:/project" -w /project $ENVIRONMENTS "$IMAGE" "$@"