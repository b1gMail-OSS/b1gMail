**THIS SETUP IS ONLY MEANT FOR DEVELOPMENT PURPOSES!**

> Make sure you have Docker installed on your system.

In order to start a development server, simply run `docker compose up`. This will start an apache server with php (on `localhost:5000`), a mysql database, and phpmyadmin (on `localhost:3100`).

The credentials for the database that you will later also need when installing b1gmail:

- Host: `db` (Yes, you don't need to use any IP address or similar. Just `db` is sufficient.)
- Database name: `b1gmail`
- Username: `user`
- Password: `password`

In order to log in to phpmyadmin, use the following credentials:

- Username: `root`
- Password: `root`

Important when recreating the containers: We don't only store data in the database, but also in `/src/temp/` and `/src/data/`. You might need to delete files and folders in these directories. You can delete everything from `/src/data/` and `/src/temp/` except for the following files:

- `/src/data/.htaccess`
- `/src/data/index.html`
- `/src/temp/.htaccess`
- `/src/temp/index.html`
- `/src/temp/cache/dummy`
- `/src/temp/session/dummy`
- `/src/serverlib/config.inc.php`

A script for automation is included. If you want to use a "hacky" approach for now: Simply delete these `/src/data/` and `/src/temp/` directories and then use git to revert the changes.
