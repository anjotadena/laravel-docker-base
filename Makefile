up:
	docker-compose up
up-build:
	docker-compose up --build
up-d:
	docker-compose up -d
up-d-build:
	docker-compose up -d --build
migration:
ifeq ($(name),)
	echo "Can't create migration!"
else
	docker-compose run --rm artisan make:migration $(name)
endif
