# Kayabase API

This project is developing for the Main capabilities of the Kayabase.

Clone project to your local machine:

```bash
git clone https://github.com/kayabase/api.git
```

Install composer dependencies:

```bash
composer install
```

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

Run the sail:

```bash
./vendor/bin/sail up -d
```

Tips for sail:

`-d` option is runs the application on the background.\
`--build` option is rebuild the docker container.\
`./vendor/bin/sail down -v` is remove container from docker.

**_NOTE:_** `./vendor/bin/sail down -v` and then `./vendor/bin/sail up -d` commands are rebuild the container. 
This may need to be run if your `.env` file has been modified, and you want it implemented in a docker container.

**_NOTE:_** If you are using the `mysql` on your local machine, you should stops it to connect the docker container `mysql` bridge.