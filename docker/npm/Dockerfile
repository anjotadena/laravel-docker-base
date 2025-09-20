FROM node:22.18.0

RUN mkdir -p /var/www/html

# Ensure npm cache dir exists and is owned by the node user
RUN mkdir -p /home/node/.npm && chown -R node:node /home/node/.npm
ENV NPM_CONFIG_CACHE=/home/node/.npm
