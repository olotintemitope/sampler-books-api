## Sampler Backend Dev Challenge

## How to Install

- git clone repo `git clone https://github.com/olotintemitope/sampler-books-api.git`
- Download and Install Docker via [https://www.docker.com/get-started][Download Docker]
- Run `docker-compose up -d` to start the container
- Run `docker exec -it sampler-books-php-fpm /bin/bash` to get into the app directory
- Run your composer `composer install` and,
  - `php artisan migrate`
  - `php artisan passport:install`
  - `php artisan db:seed`
  
- Go to your browser and type in `127.0.0.1:5000/api/user` you should see all users being displayed

## Unit Tests
The test uses sqlite DB for fast running of the test cases.
- Run `phpunit`

## API Documentation
- Visit [https://documenter.getpostman.com/view/3781859/TWDWHwRW#0b44bc32-1c1d-41c2-9d3b-27bf0bf4442a][Link to documentation]

[Link to documentation]: https://documenter.getpostman.com/view/3781859/TWDWHwRW#0b44bc32-1c1d-41c2-9d3b-27bf0bf4442a

[Download Docker]: https://www.docker.com/get-started