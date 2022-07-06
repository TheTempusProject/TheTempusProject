# The Tempus Project
#### Rapid Prototyping Framework
###### Developer(s): Joey Kimsey

The aim of this project is to provide a simple and stable platform from which to easily add functionality. The goal being the ability to quickly build and test new projects with a lightweight ecosystem to help.

**Notice: This code is in _still_ not production ready. This framework is provided as is, use at your own risk.**
I am working very hard to ensure the system is safe and reliable enough for me to endorse its widespread use, but it still needs a lot of QA and improvements.

Currently I am in the process of testing all the systems in preparation for the first production ready release. The beta is still on-going. If you would like to participate or stay up to date with the latest, you can find more information at: https://TheTempusProject.com/beta

## Installation

Preferred method for installation is using composer.
1. Clone the directory to wherever you want to install the framework.
2. Open your terminal to the directory you previously cloned the repository.
3. Install using composer:
`php composer.phar install`
4. Open your browser and navigate to install.php (it will be in the root directory of your installation)
5. When prompted, complete the form and submit.

If you have any trouble with the installation, you can check out our FAQ page on the wiki for answers to comon issues.

If you would like a full copy of the project with all of its included dependencies you can find it at https://github.com/TheTempusProject/TempusProjectFull
Please note this repository is only up to the latest _stable_ release. Please continue to use composer update to get the latest development releases.

**Do not forget to remove install.php once you have finished installation!**

#### Currently being developed:
- [ ] Code refactoring
- [ ] Adding documentation
- [ ] Unit tests
- [ ] Edits for PSR conformity

#### Future updates
- [ ] Expansion of PDO to allow different database types
- [ ] Update installer to account for updates.
- [ ] Impliment uniformity in terms of error reporting, exceptions, logging.



chrome extention that lets me tag a stack overflow to a ticket

can i use submodules?

errors should be able to be customized
	if its in the app

need to make a right click -> syntax menu plugin for sublimetext

https://www.askapache.com/online-tools/mac-lookup/
https://www.askapache.com/online-tools/whoami/
base64-image-converter/
https://www.askapache.com/online-tools/base64-image-converter/
Advanced HTTP Request / Response Headers
https://www.askapache.com/online-tools/http-headers-tool/

https://www.askapache.com/htaccess/mod_rewrite-variables-cheatsheet/

need a little JS button that will auto-copy code

web dev bible should have user saved settings that auto-change commands based on saved preferences

/**
 * js/default.js
 *
 * This file is for 'access anywhere' javascript.
 *
 * @version  3.0
 * @author   Joey Kimsey <Joey@thetempusproject.com>
 * @link     https://TheTempusProject.com
 * @license  https://opensource.org/licenses/MIT [MIT LICENSE]
 */

/**
 * <file location>
 *
 * <brief desvription>
 *
 * @version  <version>
 * @author   <full name> <<$email>>
 * @link     <website>
 * @license  <license url> [<license name>]
 */


init.php
    test running commands from cli

testmail-server-installer

for controllers specifically:

 * @todo
 *  can i move construct and destroct somewhere else?
 *  need an alrenative for exit();

 should have routes do a check when setting up new controllers
 like home/subscribe for emaple could exist before installing the subscribe plugin

 web dev bible:

 have a git file that literally just tests all manner of nginx settings

        "loginLimit": 3,
        "logo": "images/logo.png",
        "avatarOnly": false,

        // Components::set('RECAPTCHA_SITE_KEY', Config::get('recaptcha/siteKey'));
        // Components::set('RECAPTCHA', Views::standardView('recaptcha')); // not found

initiated // is in so many controllers, i def want this removed
initialized

if literally any page loads in the background, like say... a 404 page for missing JS, it will generate a new token messing up the form checks

if we move install.php to the bin, it will be unaccessible to the web server??
if its unaccessible except theough the index.php router, we don't need to delete it because its unaccessible again

DJhBcX5e2Fl^!F1$7bk^

sudo apt-get install php-mysq
php -i | grep pdo_mysql
dpkg --get-selections | grep php | grep mysql






Pages to Test
Error
    Index
Home
    index
    profile
    login
    logout
    terms
Member
    index
Register
    index
    recover
    confirm
    resend
    reset
Rest
    ping
Usercp - TBD
plugins
    blog
        index
        rss
        comments
        post
        author
        month
        year
Admin
    Users
    Settings
    Logs
    Logins
    Installed
    Home
    Groups
    Errors
    Dependencies
    Contact
    Admin
    Admin-old



https://stackoverflow.com/questions/15166445/routing-requests-through-index-php-with-nginx


need to add a pre-install check to ensure php can write to the config directory
and uploads directory
and whatever is going to be needed to the plugin downloading
