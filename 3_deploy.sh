#!/bin/bash
CURRENT_DIR=$(dirname $0)
docker stack deploy -c "$CURRENT_DIR/dockerPublicWebServer/docker-compose.yml" docker-public-web-server
docker stack deploy -c "$CURRENT_DIR/dockerPrivateWebServer/docker-compose.yml" docker-private-web-server
docker stack deploy -c "$CURRENT_DIR/dockerPublicSampleApp/docker-compose.yml" docker-public-sample-app
docker stack deploy -c "$CURRENT_DIR/dockerPrivateSampleApp/docker-compose.yml" docker-private-sample-app
