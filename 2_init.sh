#!/bin/bash
if [ "$1" == "" ]; then
    docker swarm init
else
    docker swarm init --advertise-addr $1
fi

if ! docker network ls | grep "docker-public-apps-network"; then
    docker network create -d overlay docker-public-apps-network
fi
if ! docker network ls | grep "docker-private-apps-network"; then
    docker network create -d overlay docker-private-apps-network
fi
