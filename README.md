# Laravel Docker Workflow (WIP)
[![CircleCI](https://circleci.com/gh/anjotadena/laravel-docker-base/tree/master.svg?style=svg)](https://circleci.com/gh/anjotadena/laravel-docker-base/tree/master)[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

This is my basic workflow for using Docker and Docker Compose with Laravel development.

## Boilerplate features
- API authentication with Sanctum (WIP)
- Jetstream or React or Angular Frontend scaffold (WIP)
- SCSS or tailwind css styling (WIP)
- Permissions
- Role
- Database backup schedule midnight (WIP)
- CI/CD with circleCI
- Unit/Feature Testing
- Redis ready for caching and queuing

## Getting Started

Make sure you have [Docker for windows installed](https://docs.docker.com/docker-for-windows/install/) or  [Docker for mac installed](https://docs.docker.com/docker-for-mac/install/)  on your system, and then copy this directory to a desired location on your development machine. 

## Building Containers
Navigate in your terminal to the project directory, and spin up the containers for the full web server stack by running `docker-compose up -d --build`. 

## Docker Commands

Install laravel dependencies
- `docker-compose run --rm composer install`

Install node dependencies
- `docker-compose run --rm npm install`

Generate laravel key
- `docker-compose run --rm artisan key:generate`

Migrate database
- `docker-compose run --rm artisan migrate`

Database seeding
- `docker-compose run --rm artisan db:seed`

Queue work
- `docker-compose run --rm artisan queue:work --tries=3`

Scheduler
- `docker-compose run --rm scheduler`

PHPunit testing
- `docker-compose run --rm phpunit`

## Container network
Available ports to the host machine:
- **nginx** - `:80`
- **mysql** - `:3306`
- **php** - `:9000`
- **redis** - `:6379`

## Permissions Issues

During the PHP service setup portions of this course, there are portions that reference permission issues and utilizing some commands in the `docker/php/Dockerfile` to work around them. That solution might not work for every system, and if your machine throws an error after implementing them, **please see the updated `docker/php/Dockerfile` and `docker/php/www.conf`.**. 

Using these updates, rebuilding your Docker network with `docker-compose up -d --build` should resolve any permissions issues during site loads, composer installations, or artisan commands.

## Certbot

Renew certbot
- `docker-compose -f docker-compose.prod.yml run certbot renew`


## Contact Info

If you have any questions, please feel free to message me on [LinkedIn](https://www.linkedin.com/in/73ch801/) or send me an email directly at [tadena.anjo@gmail.com](mailto:tadena.anjo@gmail.com). 
