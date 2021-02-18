

## Sampler Backend Dev Challenge

## How to Install
- Download and Install Docker
- Run `docker-compose up -d` to start` the container
- Run `docker exec -it sampler-books-php-fpm /bin/bash` to enter into the app directory
- Run your composer `composer install` and then the migrations and seeders via;
  `php artisan migrate 
  php artisan db:seed`
  
- Go to your browser and type in `127.0.0.1:5000/api/user` you should see all users being displayed

## API Documentation


## License

The Laravel framework is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
