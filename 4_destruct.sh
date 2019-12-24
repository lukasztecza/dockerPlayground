#!/bin/bash
docker stack rm docker-public-web-server
docker stack rm docker-private-web-server
docker stack rm docker-public-sample-app
docker stack rm docker-private-sample-app
docker stack rm docker-private-mysql
