FROM nginx:stable-alpine

ADD ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Not necessary but good to have to make sure its exists
RUN mkdir -p /var/www/html

RUN mkdir -p /etc/nginx/certs/mkcert

ADD ./docker/nginx/certs/ /etc/nginx/certs/mkcert
