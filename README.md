# File system routes for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/micaeldias/laravel-single-file-routes.svg?style=flat-square)](https://packagist.org/packages/micaeldias/laravel-single-file-routes)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/micaeldias/laravel-single-file-routes/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/micaeldias/laravel-single-file-routes/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/micaeldias/laravel-single-file-routes/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/micaeldias/laravel-single-file-routes/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/micaeldias/laravel-single-file-routes.svg?style=flat-square)](https://packagist.org/packages/micaeldias/laravel-single-file-routes)

Single file routes allows you to co-locate everything about a route into a single file. See the route method, URI, middleware and behaviour at a glance without the need to keep track of multiple files. 

```php
namespace App\Http\Routes\Api\User;

use Illuminate\Http\Request;
use App\Http\Routes\Api\ApiRouteGroup;
use MicaelDias\SingleFileRoutes\Routing\Route;

/**
 * @uses ApiRouteGroup
 */
class Get extends Route
{
    public static $method = 'GET';

    public static $uri = '/user/{id}';

    public static $middleware = [];

    /**
     * Handle the request.
     */
    public function __invoke(Request $request, int $id)
    {
        // return view('user', ['id' => $id]);
        // return new JsonResponse(['id' => $id]);
        // or any other response supported by Laravel.
    }
}
```

## Installation

You may install single file routes into your project with:
```bash
composer require micaeldias/laravel-single-file-routes
```

After installing, publish the assets using the `single-file-routes:install` Artisan command:

```bash
php artisan single-file-routes:install
```

## Configuration

After publishing single file routes' assets, its primary configuration file will be located at `config/single-file-routes.php`. Each configuration option includes a description of its purpose, so be sure to thoroughly explore this file.

## Usage

### Route Groups
To get started you need to create at least one route group, you can have a single one for all your routes or multiple ones such as web/api, there are no limitations on how you structure your app.

```bash
php artisan make:route-group
```

[demo make:route-group](assets/make-route-group.gif)

### Routes

Once you have at least one route group you can start creating routes, by default the route will be namespaced according to the URI, so `/api/user` would be stored under the `App\Http\Routes\Api\User` namespace. This is customisable when running the command.

```bash
php artisan make:route
```

[demo make:route](assets/make-route.gif)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Micael Dias](https://github.com/micaeldias)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
