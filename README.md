Start the development docker:

Make a copy of .env.example to .env, then bring up the docker compose:

`./vendor/bin/sail up`

Run the hot module replacement:

`./vendor/bin/sail npm run dev`

Laravel Sail is just a frontend to docker compose, you can pass it commands to execute in the app container:

`./vendor/bin/sail artisan test`

`./vendor/bin/sail composer dump-autoload`

The site is available at: http://localhost:8400/

The database frontend Adminer is available at: http://localhost:8401
