# dockerPlayground
Sample images for docker that contain public web server, private web server, public app and private app

### Usage
Clone this repo
```
git clone https://github.com/lukasztecza/dockerPlayground.git
cd dockerPlayground
```
Build docker images
```
bash 1_build.sh
```
Init swarm manager and create external networks
```
bash 2_init.sh
```
To leave the swarm (do not do it now)
```
docker swarm leave --force
```
It is possible that your local environment has multiple addresses on interface that swarm wants to use, if so then choose one and pass it
```
ifconfig
bash 2_init.sh 111.111.111.111 (address which you want to use from ifconfig)
```
Deploy both web servers and both apps
```
bash 3_deploy.sh
```
By default docker-public-web-server points to fastcgi script on port 9000 of the app container picked by domain name
```
www.{service_name_that_will_be_pointed_to}.com
```
and assumes that front controller for this app lives in
```
/var/www/http/public/app.php
````
and assumes that assets for this repo are located on host machine under
```
./assets/{service_name_that_will_be_pointed_to}/
```
you may change mounted directory using volume in `docker-compose.yml`




### Show existing sample public app output that includes private app output and how to add more stacks

### Using for other apps
Now you can deploy stacks with your apps for instance your app could look like this
```
/docker-compose.yml
/Dockerfile
/public/app.php
```
Where docker-compose.yml (note exposed port 9000 for fastcgi, and shared volume for dev)
```yml
version: "3.5"

networks:
    public-network:
        name: docker-public-apps-network
        external: true

services:
    public-app:
        image: public-app:latest
        deploy:
            replicas: 3
            resources:
                limits:
                    cpus: "0.1"
                    memory: 16M
            restart_policy:
                condition: on-failure
        networks:
            - public-network
```
And Dockerfile
```
FROM php:7.2.10-fpm-alpine3.8

ADD /public /var/www/html/public

EXPOSE 9000
```
And app.php
```
<?php
echo 'Hello world this is public app!';
echo '<br />';
echo $_SERVER['HOSTNAME'];
```
Make sure your app has image built on your machine
```
docker image ls
```
If it is not there build image once in app directory where Dockerfile lives
```
docker build -t public-app .
```
Your service will be named public-app so to be able to see it on your local machine update your `/etc/hosts` with
```
127.0.0.1   www.public-app.com
```
Then go to your app root directory and deploy stack
```
docker stack deploy -c docker-compose.yml public-app
```
You should be able to see Hello world message here
```
http://www.public-app.com
```
You can scale the service (service name is built from stack name and name of app in docker compose)
```
docker service scale public-app_public-app=5
```
You may need to restart web-server if so just scale it down to 0 and back to 1
```
docker service scale docker-public-web-server_docker-public-web-server=0
docker service scale docker-public-web-server_docker-public-web-server=1
```
You can remove stack
```
docker stack rm public-app
```

### Other notes
From within docker container you can point to a different php container that is behind php-fpm using url
```
docker-private-web-server/{docker_container_app_points_to}/{path_that_will_be_followed_to_container_app}
```
