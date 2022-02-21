FROM nginx:stable-alpine

ADD ./docker/nginx/default.prod.conf /etc/nginx/conf.d/default.conf

# Not necessary but good to have to make sure its exists
RUN mkdir -p /var/www/html

ADD ./src/ /var/www/html
