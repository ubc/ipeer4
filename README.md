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

## Authorization System

Roles & permissions are managed using the laravel-permission library.

We have two broad types of roles & permissions: System or course.  System
permissions apply to the entire app. Course permissions apply only inside the
course you're enroled in. For example, an admin is a system role and has access
to every. A student is a course role and they can only see courses they're
enroled in. In older iPeer 3, there is no such separation, and there are only
system roles. So we had issues where some users (TAs) were students in some
courses but instructors in others needed to have two separate accounts for each
of those roles. The course-specific roles are meant to address this.

### Custom fields in roles & permissions

Permission:

* desc - text description field
* is_template - boolean indicating whether this is a course permission template

Role:

* desc - text description field
* is_system - boolean indicating whether this is a system role
* is_template - booleaning indicating whether this is a course role template
* course_id - if a course-specific role, which course it's for

## Environment Variables

When seeding the database, a default admin user is created, the default admin user credentials can be specified via these env vars:

* ADMIN_USERNAME - default admin user's username
* ADMIN_PASSWORD - default admin user's password
