[![Laravel Forge Site Deployment Status](https://img.shields.io/endpoint?url=https%3A%2F%2Fforge.laravel.com%2Fsite-badges%2Fb7221607-01f1-4a61-a0d9-b98c61ef1b1b&style=plastic)](https://portal.codingarena.top/welcome)

Demo: [https://portal.codingarena.top/welcome](https://portal.codingarena.top/welcome)

## Contributing and Proposals

[https://gitworkshop.dev](https://gitworkshop.dev/holgerhatgarkeinenode@einundzwanzig.space/einundzwanzig-app)

## Development

### Installation

```cp .env.example .env```

```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```
*(you need a valid Flux Pro license or send a message to [Nostr - The Ben](http://njump.me/npub1pt0kw36ue3w2g4haxq3wgm6a2fhtptmzsjlc2j2vphtcgle72qesgpjyc6))*

#### Start docker development containers

```vendor/bin/sail up -d```

### Migrate and seed the database

```./vendor/bin/sail artisan migrate:fresh --seed```

### Laravel storage link

```./vendor/bin/sail artisan storage:link```

#### Install node dependencies

```vendor/bin/sail yarn```

#### Start just in time compiler

```vendor/bin/sail yarn dev```

#### Update dependencies

```vendor/bin/sail yarn```

## Security Vulnerabilities

If you discover a security vulnerability within this project, please go to [https://gitworkshop.dev](https://gitworkshop.dev/holgerhatgarkeinenode@einundzwanzig.space/einundzwanzig-app). All security vulnerabilities will be promptly addressed.

## License

Open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
