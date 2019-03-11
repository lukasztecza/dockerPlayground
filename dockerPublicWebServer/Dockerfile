FROM nginx:1.15.3-alpine

RUN rm /etc/nginx/conf.d/*
COPY front-default.conf /etc/nginx/conf.d/front-default.conf

EXPOSE 80
EXPOSE 443
