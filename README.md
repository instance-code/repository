# Laravel-repository

| **Laravel/lumen**  |  **laravel-repository/lumen-repository** |
|---|---|
| ^11.*  | ^dev  |

`instance-code/repository` is a Laravel package which created to manage your large  Laravel app using repository. Repository is like a Laravel package. This package is supported and tested in Laravel 5.*

With one big added bonus that the original package didn't have: **tests**.


## Install

To install through Composer, by run the following command:

``` bash
composer require instance-code/repository
```

## Lumen config
``` bash
 //bootstrap\app.php
 Add : $app->register(InstanceCode\Repository\Providers\RepositoryServiceProvider::class);
```

## Laravel config
``` bash
  //bootstrap\app.php
 ->withProviders([
    \InstanceCode\Repository\Providers\CommandServiceProvider::class,
])
```

## Setup repository
``` 
  // publish vendor
  php artisan vendor:publish --tag=repository

  Create repository
  // Create folder default to app\repositories
	php artisan make:repository {name} {--m}

  Create Service 
  // Create folder default to app\repositories
	php artisan make:service {name}
    
  // example: php artisan make:repository User
  // example: php artisan make:service User
    
  // add bindings to App\Providers\RepositoryServiceProvider Or config/repository.php to register repository
  /*
   * Default binding
   * [ RepoInterface::class => Repository::class ]
   */
  'bindings' => [
      \App\Repositories\User\UserInterface::class => \App\Repositories\User\UserRepository::class,
  ],


```

## Publish config
``` 
	php artisan vendor:publish --tag=repository
```

The package will automatically register a service provider and alias.

Optionally, publish the package's configuration file by running:

### Autoloading



**Tip: don't forget to run `composer dump-autoload` afterwards.**

## Documentation
### Visit: [Website](https://instance.asia)
### Response resource
```
use InstanceCode\Repository\Facades\UseRepository;
/**
 * $data: String | Object | Array
 * response interface: $data['status'] | $data['messages'] | $data['body']
*/
return UseRepository::response($data);

```
You'll find installation instructions and full documentation on : comming son....
 
 
## Credits ....


## About Instance-code/repository

instance-code/repository is a freelance web developer specialising on the Laravel/lumen framework.


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
