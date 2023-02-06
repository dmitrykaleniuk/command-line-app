# command-line-app
Tax calculator for the transactions provided through the file (input.txt)

# How to app
1. Clone the repo.
2. Run **composer install**.
3. Create **.env** file from .env.dist.
4. Paste **API_KEY** into .env file.
5. Run the app **php bin/console calculate-tax input.txt** 

# How to run tests
1. Run **composer test-no-coverage** or **./vendor/phpunit/phpunit/phpunit tests**.

# Input file example (input.txt)

{"bin":"45717360","amount":"100.55","currency":"EUR"}
{"bin":"516793","amount":"50.00","currency":"USD"}
