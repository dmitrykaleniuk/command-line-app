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

# Troubleshooting
Be sure that you have php 8.2 installed.

If no, install brew: /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
Add brew to the PATH

test -d ~/.linuxbrew && eval "$(~/.linuxbrew/bin/brew shellenv)"
test -d /home/linuxbrew/.linuxbrew && eval "$(/home/linuxbrew/.linuxbrew/bin/brew shellenv)"
test -r ~/.bash_profile && echo "eval \"\$($(brew --prefix)/bin/brew shellenv)\"" >> ~/.bash_profile
echo "eval \"\$($(brew --prefix)/bin/brew shellenv)\"" >> ~/.profile

Install php 8.2: brew link php@8.2 --force --overwrite
