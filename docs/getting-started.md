

ELib Base
===

Requirements
---
* MySQL (If you are using [base-docker](/docs/base-docker/) this is made available to you).


Setup
---


##### Base-Docker Quick start command

(Bootstrap a running Empathy project with elib-base installed and initialized).

<pre><code class="lang-bash">cd ./ansible
ansible-playbook ../main.yml -e "op=qs cb=backend-demo tpl=elib-base"
</code></pre>

##### Manual setup

Follow the instructions in the Empathy "[getting-started.md](/docs/empathy/docs/getting-started.md)" documentation:


Hower use the following `composer.json` configuration:

<pre><code class="lang-vim">{
    "require": {
        "mikejw/elib-base": "dev-master"
    },
    "minimum-stability": "dev"
}
</code></pre>


Database Philosophy
---

In Empathy an effort is made to decouple data definition from data manipulation, particularly
during development. There is an assumption that once your app is in production, you may want to switch to a powerful database migrations library such as [Phinx](https://phinx.org/).

Until that time it is recomended to follow the following guidelines with regard to 
three utility CLI commands and three files you may have in your project at any given time.


The three files:

<pre><code class="lang-vim">/setup.sql
/inserts.sql
/dump.sql
</code></pre>


The three commands:

<pre><code class="lang-bash">php ./vendor/bin/empathy --mysql setup
</code></pre>

This "instantiates" your database with the required schema (from `setup.sql`) and optional initial database fixtures
(from `inserts.sql`).

<pre><code class="lang-bash">php ./vendor/bin/empathy --mysql dump
</code></pre>

This saves a database dump file called `/dump.sql` of the database data, where table structure information is not 
included.

<pre><code class="lang-bash">php ./vendor/bin/empathy --mysql populate
</code></pre>


This hydrates the database from the `dump.sql` file, by first recreating the table structure
from `/setup.sql`.

The `/setup.sql` file should always contain the latest schema for your database.


Database Setup
---

Add database config settings to `/config.yml`. (db_server, db_user, db_pass, db_name.) 
If you are using "base-docker" the default settings will be fine.
(Here, the database will be called 'project'.) 

Create a file called `/setup.sql` for the data definitions and add the following to the top of the file:

<pre><code class="lang-sql">DROP DATABASE IF EXISTS project;
CREATE DATABASE project;
USE project;
</code></pre>

Next copy the contents of `./vendor/mikejw/elib-base/dd.sql` to this file at the bottom.

Between the first chunk of code (after "USE project;") and before the `CREATE` table blocks 
create the SQL statement to drop these tables:

<pre><code class="lang-sql">DROP TABLE IF EXISTS user, contact, shippingaddr;
</code></pre>


Next create a file called `inserts.sql` for data manipulation and add at the top:

<pre><code class="lang-sql">use project;
</code></pre>


Copy the contents from `./vendor/mikejw/elib-base/dm.sql` to the end of this file.


Create password
---

To create a user for yourself that's ready to use straight away, we need to generate an
encrypted password and place it in as the fifth argument to the `INSERT` statement in the `inserts.sql` file.

Run the following from the command line (you may need to connect to the Docker app container first):

<pre><code class="lang-bash">php ./vendor/bin/empathy --gen_password yourpassword
</code></pre>

Copy the output and paste it into the `inserts.sql` file in place of the existing password hash.


Initialise the database with:

<pre><code class="lang-bash">php ./vendor/bin/empathy --mysql setup
</code></pre>

Or if using "base-docker":

<pre><code class="lang-bash">docker-compose exec app php ./vendor/bin/empathy --mysql setup
</code></pre>


At this point your database should be set up, but you will still not be able to log in. Continue
to Application.


Application
---

You now need to generate default modules and controllers in your `/application` directory that inherit from
files within the elib-base repo.

Generate the default application controllers (from the root dir) with:

<pre><code class="lang-bash">php ./vendor/bin/empathy --inst_mod admin
php ./vendor/bin/empathy --inst_mod user
</code></pre>


Finally, change the `use_elib` boot options setting in your `/config.yml` to true.

<pre><code class="lang-yml">use_elib: true
</code></pre>


Also create an empty YAML configuration file in the root directory called `elib.yml`.


Backend
---

Sign into the backend with the username from `inserts.sql` and your chosen password
at `http://www.dev.org/user/login`.




