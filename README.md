# dockerPlayground
Sample images for docker that contain public web server, private web server, public app and private app, mysql db and cache app

### basic usage
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
It is possible that your local environment has multiple addresses on interface that swarm may use, if so then choose one and pass it
```
ifconfig
bash 2_init.sh 111.111.111.111 (address which you want to use from ifconfig)
```
Deploy both web servers (public and private) and apps (public and private app among others)
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
Update your `/etc/hosts` adding following line
```
127.0.0.1 www.docker-public-sample-app.com
127.0.0.1 www.docker-private-sample-app.com (this line is not needed as this app is in private network but you may want to see it is not accessible)
```
Public web server will listen on 443 port for https requests and mounts in `docker-compose.yml` file
```
./domains_certs:/etc/ssl/domains_certs
```
This means you need key and crt files for web server
```
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout localhost.key -out localhost.crt
```
Now you should be able to access public app at the following url (if you use https you will get warning - go to advanced and add exception)
```
http://www.docker-public-sample-app.com
```
If you are using curl for https add `--insecure` option to ignore self signed certificate warning
```
curl --insecure 'https://www.docker-public-sample-app.com/'
```
Note that private app is not accessible over public network
```
www.docker-private-sample-app.com (this should give 403 error)
```
You may remove corresponding record from `/etc/hosts` file
```
127.0.0.1 www.docker-private-sample-app.com (this line can be removed now)
```
As mentioned above web server expects assets for `www.docker-public-sample-app.com` to live under following directory (mounted by docker-public-web-server)
```
./assets/docker-public-sample-app/
```
there is a sample picture there `bubmblebee.jpg` which you may view hitting
```
www.docker-public-sample-app.com/assets/bumblebee.jpg
```
If you browse docker-public-sample-app you may find in
```
./public/app.php
```
following url that points to docker-private-web-server and passes private application name after slash
```
docker-private-web-server/docker-private-sample-app
```
private web server expects application name to be passed that way, note that after next slash you may pass path that will be passed to private application
```
docker-private-web-server/{docker_serivce_where_app_points_to}/{path_that_will_be_followed_to_the_docker_service_where_app_points_to}
```
Once you are done run
```
bash 4_destruct.sh
docker swarm leave --force
```

### additional usage
Beside public/private web servers and public/private sample apps there are several other services that can be used

#### docker-private-mysql
Mysql server that runs on docker private network (on dev you may want to use adminer which is added to docker-compose) and has mounted databasedir and dumps directories

#### docker-public-db-and-cache-app
App has db folder in which you can find sql that should be run in docker-private-mysql container so you may want to copy it into dumps directory of mysql container and run it

TODO following containers
####
Redis

####
Memcached

####
Sample app that will grab values from db (use cache for a read - short to test) and store values to db (db part done)

####
Rabbit

####
Sample consumer for rabbit that will call private app to store something

####
Sample producer for rabbit that will raise something whenever when called

####
Elk stack that will log everything apps and conusumers (beats, logstahs, elasticsearch, kibana - publiv but auth and ip whitelist)
