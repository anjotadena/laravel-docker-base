FROM httpd:alpine

ADD ./docker/httpd-vhosts.conf /usr/local/apache2/conf/extra/httpd-vhosts.conf

#
RUN sed -i 's,#Include conf/extra/httpd-vhosts.conf,Include conf/extra/httpd-vhosts.conf,g' /usr/local/apache2/conf/httpd.conf

# Not necessary but good to have to make sure its exists
RUN mkdir -p /var/www/html
