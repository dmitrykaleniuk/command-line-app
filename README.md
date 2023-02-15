# command-line-app
Tax calculator for the transactions provided through the file (input.txt)


# How to run app
1. Clone the repo.
2. Run **composer install**.
3. Create **.env** file from .env.dist.
4. Paste **API_LAYER_KEY** into .env file. 
You can generate api_key here: https://apilayer.com/docs/article/managing-api-keys.
5. Run the app **php bin/console calculate-tax input.txt** 
Note: you need to provide input.txt file with the transactions data. 
For further information you can check file example below.


# Input file example (input.txt)

{"bin":"45717360","amount":"100.55","currency":"EUR"}

{"bin":"516793","amount":"50.00","currency":"USD"}


# How to run tests
1. Run **composer test-no-coverage** or **./vendor/phpunit/phpunit/phpunit tests**.
Note: **.env** file should be created to run tests. 
To run the tests you can insert any dummy data for API_LAYER_KEY in .env file.
Example: API_LAYER_KEY=dummy
Dependencies are mocked.


# Tips
Be sure that you have php 8.2 installed.
