#!/bin/bash
docker stack rm docker-public-web-server
docker stack rm docker-private-web-server
docker stack rm docker-public-sample-app
docker stack rm docker-private-sample-app

docker stack rm docker-private-mysql
docker stack rm docker-private-redis
docker stack rm docker-public-db-and-cache-app

docker stack rm docker-private-rabbitmq
docker stack rm docker-public-sample-producer
docker stack rm docker-private-sample-consumer

CURRENT_DIR=$(dirname $0)
docker stack deploy -c "$CURRENT_DIR/dockerPublicWebServer/docker-compose.yml" docker-public-web-server
docker stack deploy -c "$CURRENT_DIR/dockerPrivateWebServer/docker-compose.yml" docker-private-web-server
docker stack deploy -c "$CURRENT_DIR/dockerPublicSampleApp/docker-compose.yml" docker-public-sample-app
docker stack deploy -c "$CURRENT_DIR/dockerPrivateSampleApp/docker-compose.yml" docker-private-sample-app

docker stack deploy -c "$CURRENT_DIR/dockerPrivateMysql/docker-compose.yml" docker-private-mysql
docker stack deploy -c "$CURRENT_DIR/dockerPrivateRedis/docker-compose.yml" docker-private-redis
docker stack deploy -c "$CURRENT_DIR/dockerPublicDbAndCacheApp/docker-compose.yml" docker-public-db-and-cache-app

docker stack deploy -c "$CURRENT_DIR/dockerPrivateRabbitmq/docker-compose.yml" docker-private-rabbitmq
docker stack deploy -c "$CURRENT_DIR/dockerPublicSampleProducer/docker-compose.yml" docker-public-sample-producer
docker stack deploy -c "$CURRENT_DIR/dockerPrivateSampleConsumer/docker-compose.yml" docker-private-sample-consumer
