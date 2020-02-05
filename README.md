# LARGE FILE PROCESSOR (Laravel MVC Framework)

### Controllers:

1. IndexController.php

### Models:

1. IndexModel.php

### Views:

None

### Additional Files:

1. /app/Services/LineByLineFileReader.php

### MySQL Database:

None

## Installation

1. Clone the project: git clone
2. cd <project-root-directory> (the folder containing the /app/ directory)
3. Clone laradock: git clone https://github.com/Laradock/laradock.git
4. Follow overview/instructions here: https://laradock.io/
5. Spin up the project containers: docker-compose up -d nginx mysql workspace
6. SSH into workspace container& run Composer update
7. Rename file <project-root>/.env-example to .env
8. php artisan key:generate
9. Open project directory /public/images/tesing to see screenshots from my testing process
10. Run the project in your browser: http://localhost/?url=VALID_DATA_URL

## API Reference

Not required

## Contributors

Donal Lynch <donal.lynch.msc@gmail.com>

## License

© 2020 Donal Lynch Software, Inc.