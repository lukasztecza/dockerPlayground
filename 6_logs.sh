#!/bin/bash
if [ "$1" == "" ]; then
    echo "You need to give serivce id"
else
    if [ "$2" == "" ]; then
        docker service logs --raw $1
    else
        docker service logs --raw $1 2>&1 | grep $2
    fi
fi
