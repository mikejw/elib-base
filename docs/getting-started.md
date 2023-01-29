

ELib Base
===

Requirements
---
* MySQL version ~5.7 (if you are using https://github.com/mikejw/base-docker this is made available to you).


Setup
---

Follow the instructions in the Empathy "getting-started.md" docs:
[Empathy Getting Started](https://github.com/mikejw/empathy/blob/master/docs/getting-started.md).

Hower use the following `composer.json` configuration:

    {
        "require": {
            "mikejw/elib-base": "dev-master"
        },
        "minimum-stability": "dev"
    }


Database Philosophy
---

In Empathy an effort is made to decouple data definition from data manipulation. This is achieved
with three utility commands and three files. 

NB: You are of course, once you are up and running, free to use a comprehensive database migrations
library such as the excellent [Phinx](https://phinx.org/).

The three files are:

    /setup.sql
    /inserts.sql
    /dump.sql

The commands:

    php ./vendor/bin/empathy --mysql setup

This "instantiates" your database with the required schema (from `setup.sql`) and optional initial fixtures
(from `inserts.sql`).

    php ./vendor/bin/empathy --mysql dump

This saves a database dump file called `/dump.sql`, and crucially table information is not 
saved to this file.

    php ./vendor/bin/empathy --mysql populate

This hydrates the database from the `dump.sql` file, by first recreating the table structure
from `/setup.sql`.

The `/setup.sql` file should always contain the latest schema for your database.

Database setup
---

Add database config settings to `/config.yml`. (db_server, db_user, db_pass, db_name.) 
If you are using "base-docker" the default settings will be fine.
(Here, the database will be called 'project'.) 

Create a file called `/setup.sql` for the data definitions and add the following to the top of the file:

    DROP DATABASE IF EXISTS project;
    CREATE DATABASE project;
    USE project;

Next copy the contents of ./vendor/mikejw/elib-base/dd.sql to this file at the bottom.

Between the first chunk of code (after "USE project;") and before the `CREATE` table blocks 
create the SQL statement to drop these tables:


    DROP TABLE IF EXISTS e_user, contact, shippingaddr;


Next create a file called `inserts.sql` for data manipulation and add at the top:

    use project;

Copy the contents from ./vendor/mikejw/elib-base/dm.sql to the end of this file.

Create password
---

To create a user for yourself that's ready to use straight away, we need to generate an
encrypted password and place it in as the fifth argument to the `INSERT` statement in the `insert.sql` file.

Run the following from the command line:

    php -a
    php > echo password_hash('yourpassword', PASSWORD_DEFAULT);

Copy the output and paste it into the `insert.sql` file in place of the existing password hash.


Initialise the database with:

    php ./vendor/bin/empathy --mysql setup

Or if using "base-docker":

    docker-compose exec app php ./vendor/bin/empathy --mysql setup


At this point your database should be set up, but you will still not be able to log in. Continue
to Application.


Application
---

You now need to generate default controllers in your `/application` directory that inherit from
files within ELib-Base.

Generate the default application controllers (from the root dir) with:

    php ./vendor/bin/empathy --inst_mod admin
    php ./vendor/bin/empathy --inst_mod user


Finally, change the `use_elib` boot options setting in your `/config.yml` to true.

    use_elib: true

Also create an empty YAML configuration file in the root directory called `elib.yml`.


Backend
---

Sign into the backend with username '<username>' and your password
at `/user/login`.


Passwords can be changed at `/admin/password`.

