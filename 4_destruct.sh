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
