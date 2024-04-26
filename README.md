## Try at:

https://todo.nebojsha.com

## Quickstart:

1. git clone https://github.com/nebojsha-mitikj/todo.git
2. cd todo
3. docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
4. Rename .env.example to .env
5. ./vendor/bin/sail up -d
6. ./vendor/bin/sail artisan key:generate
7. ./vendor/bin/sail npm install
8. ./vendor/bin/sail npm run build
9. Update database env variables in .env

## App Screenshots:

![todo-app](https://github.com/nebojsha-mitikj/todo/assets/122390352/745d9de3-96d4-4441-8844-0c05923395c9)
