#!/bin/bash
CURRENT_DIR=$(dirname $0)
docker build -t docker-public-web-server "$CURRENT_DIR/dockerPublicWebServer/."
docker build -t docker-private-web-server "$CURRENT_DIR/dockerPrivateWebServer/."
docker build -t docker-public-sample-app "$CURRENT_DIR/dockerPublicSampleApp/."
docker build -t docker-private-sample-app "$CURRENT_DIR/dockerPrivateSampleApp/."

docker build -t docker-private-mysql "$CURRENT_DIR/dockerPrivateMysql/."
docker build -t docker-private-redis "$CURRENT_DIR/dockerPrivateRedis/."
docker build -t docker-public-db-and-cache-app "$CURRENT_DIR/dockerPublicDbAndCacheApp/."

docker build -t docker-private-rabbitmq "$CURRENT_DIR/dockerPrivateRabbitmq/."
docker build -t docker-public-sample-producer "$CURRENT_DIR/dockerPublicSampleProducer/."
docker build -t docker-private-sample-consumer "$CURRENT_DIR/dockerPrivateSampleConsumer/."
