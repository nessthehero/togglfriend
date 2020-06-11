# Toggl Friend

## Setup

You will need node on your computer.

You will need to set up an Apache server with PHP. There is no minimum version of PHP but I recommend at least 7.

The server should be serving this repo.

You will need composer. [https://getcomposer.org/](https://getcomposer.org/)

## Init

Run the init script from the command line. You may need to run it as an administrator. It should be run from an interpreter that understands Bash.

If this isn't possible, perform the following steps:

1. Modify the permissions of /data so that anything can write to it. (chmod 0777)
2. Run npm install in the /scripts folder.
3. Run composer install in the /php folder.

## Firebase

Create a new project on Firebase, go to project settings and paste the config object into togglfriend.config.js. Resave the example file to create it.

## PHP Config

Rename config.example.php to config.php under /php and fill in your toggl API key.

## ENV

Save .env.example as .env and replace the values with your username, password, URL of Captains Log login form, and path to the server you want the job code output file saved as, ending in `.json`.

## Crontab

Set up a crontab that looks like the following:

`0 * * * 1-5 /usr/local/bin/node /PATH/TO/YOUR/SERVER/scripts/fetch.js 2>/tmp/cronlog.log`

The first part, `0 * * * 1-5` ensures the script is run once an hour on every weekday, Mon-Fri.

The first path should be pointed at your node bin script. To find this, run `which node` in a terminal.

The second path is the fetch.js node script in this project.

The last part is what logs any output to a log file. 

