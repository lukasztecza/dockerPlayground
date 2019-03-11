#!/bin/bash
CURRENT_DIR=$(dirname $0)
docker stack deploy -c "$CURRENT_DIR/dockerPublicWebServer/docker-compose.yml" docker-public-web-server
docker stack deploy -c "$CURRENT_DIR/dockerPrivateWebServer/docker-compose.yml" docker-private-web-server
docker stack deploy -c "$CURRENT_DIR/dockerPublicApp/docker-compose.yml" docker-public-app
docker stack deploy -c "$CURRENT_DIR/dockerPrivateApp/docker-compose.yml" docker-private-app
