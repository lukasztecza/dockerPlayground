#!/bin/bash
CURRENT_DIR=$(dirname $0)
docker build -t docker-public-web-server "$CURRENT_DIR/dockerPublicWebServer/."
docker build -t docker-private-web-server "$CURRENT_DIR/dockerPrivateWebServer/."
docker build -t docker-public-app "$CURRENT_DIR/dockerPublicApp/."
docker build -t docker-private-app "$CURRENT_DIR/dockerPrivateApp/."
