FROM nginx:1.18.0-alpine

RUN rm /etc/nginx/conf.d/*
COPY front-default.conf /etc/nginx/conf.d/front-default.conf

EXPOSE 80
