## Tiny Tweet
### Test case - Database access using active row
#### :pushpin: Purpose
Implementation of tweeter-like application as an exercise on database and object oriented programming integration. Project goal is to get used to Active Record ORM pattern. This project is a part of "PHP back-end developer" course arranged by [CodersLab](https://coderslab.pl).  Functionality shoud include login, register, view/post tweets, view/post comments, send/receive private messagges. Only login and register forms should be available for not logged-in users (publicly available), other views/forms sould be for logged-in users only (private).

Additionaly I'm going to adhere to [php-fig.org recomendations](https://www.php-fig.org).

:exclamation: **SECURITY DISCLAIMER: This project is not intended for production environment** :exclamation:

Security is not a topic of this exercise so beside absolutly basic measures (using sessions) there are no other features concerning security (secure login/transport path/integrity etc.).

#### :wrench: Requirements
- Apache (http) server with mod_rewrite (that is allows `.htaccess` file per directory)
- php 7 - templates use null coalescing operator (`??`)
- MySQL database account with CREATE/DROP TABLE privilege
- composer (there are no external dependencies but its autoloader is utilised to manage autoloading)

#### :floppy_disk: Installation
- :one: Check if you have `composer` installed: open terminal and type `composer`, you should get its help screen. If you don't have `composer` installed open terminal and enter `sudo apt-get install composer` (for Ubuntu 16.04) or check [here](https://getcomposer.org/download/) or consult your system administrator/documentation on how to instal/use `composer`.
- :two: At command prompt go to your destination directory: `cd` to your www directory (destination dir MUST be empty or you can clone repository to temporary folder and then copy its content to destination dir)
- :three: Get repository: `git clone https://github.com/szywo/tc-tiny-tweet.git .` - don't forget the dot at the end otherwise additional tc-tiny-tweet dir will be created and content will end up there.
- :four: Prepare database: `mysql -h hostname -u user -p -D <database_name> < db_dump.sql` enter password when prompted or use phpMyAdmin's import tool
- :five: Copy `db_conf.php` out of your web root directory (default destination is parent directory of index.php's directory - path: `../db_conf.php` relative to `index.php`'s dir).
- :six: Edit copied `db_conf.php` with data required to connect to your database. Should you need to change database config script name or location, edit `script_conf.php` file.
- :seven: Run composer: 'composer update'.
- :fireworks: You should be good to go.


#### :heavy_check_mark: Implementation progress (unordered list)

- [x] Url rewriting
- [x] Session (see disclaimer above)
    - [x] Authentication
- [x] Routing
- [x] Template engine
- [ ] Page templates
    - [x] Login
    - [x] Register
    - [x] Server errors (404, 500)
    - [ ] ...
- [x] Database Access
- [ ] Active Record Classes
    - [x] User
    - [ ] Post
    - [ ] Comment
    - [ ] Message
- [ ] Application Logic
    - [x] Login
    - [ ] Register
    - [ ] ...

#### :paperclip: Notes
These are to log research effort I put into this project and also as a reference for development choices I made.

##### mod_rewrite
Important part to remember is that, contrary to the order of placement, `RewriteRule` matching takes precedence before `RewriteCond` matching. So if `RewriteRule` pattern does not catch desired url then `RewriteCond` is not even tried. I've learned that by reading through mod_rewrite logs first and then finding [Ruleset Processing](https://httpd.apache.org/docs/2.4/rewrite/tech.html#InternalRuleset) page. I strongly recomend using `LogLevel` directive for testing rewrite rules. Its only drawback is that it cannot be used in `.htaccess` file, so it usually means that you have to setup your own www server. But that is the way it should be done because excessive logging decreases server's performance.

##### Template engine
There are many high end frameworks that allows to easily separate logic from presentation but they are far too heavy for such simple project as this one. I wanted simple lightweight solution that allows nesting templates and also allows using flow control inside templates. I've tried to write something by my own but while looking for ideas I came across solution proposed by _Chad Minick_ in his article [Simple PHP Template Engine](http://chadminick.com/articles/simple-php-template-engine.html). And this is it. It is perfect for such a small project, preiod.

##### Routing
Basic routing can be made by simple `switch` but it requires nesting switches for deeper paths and also requires conditional formating of path's parts. For example _/locations/london_ and _/comment/321234_ will require separate formating/filtering on second part. Regular expressions especially with additional parenthesized subpatterns matches can help alot in such situations. They will also help in case when site tree is non uniform (branch lengths differ or contain mixed data - strings and digits). For this reason I will use _Mike Dunn's_ [RegexRouter](http://blog.moagrius.com/php/php-regexrouter/) (source available [here :octocat:](https://github.com/moagrius/RegexRouter)). Example use case:
```php
$router = new RegexRouter();
$router->route(
    '/^\/blog\/(\w+)\/(\d+)\/?$/',
    function ($category, $id) use ($copy_of_parent_scope_var) {
        print "category={$category}, id={$id}";
    }
);
$router->execute($_SERVER['REQUEST_URI']);
```
It is not perfect but it is simple and versalite enough to suit this project. My problem with this solution is that using closures makes route execution code visible in main controler (I would prefer to keep it elsewhere like separate class) and that I need to use `function () use () {}` to inherit variables from parent scope (but, I assume, other solutions might have similar problem). Only change I made in RegexRouter class is addition of namespace. I left orginal global docblock and did not add any to properties and methods as the code is so short that additional comments might decrease its legibility.

##### Sessions
As stated before security is not topic of this project, but after reading of [Securing Session INI Settings](http://php.net/manual/en/session.security.ini.php), [Session Management Basics](http://php.net/manual/en/features.session.security.management.php), [session_regenerate_id()](http://php.net/manual/en/function.session-regenerate-id.php), [session_create_id()](http://php.net/manual/en/function.session-create-id.php) and also [Truly destroying a PHP Session?](https://stackoverflow.com/a/509056/9418958) it seems reasonable to encapsulate all session data and validation in separate object. Upon initialisation object should check if sessions are enabled, secure ini parameters, validate session (detect obsolete ones, regenerate id, etc.) and finally transfer all `$_SESSION` data to its private properties (to hide validation parameters from user). Any interaction with `$_SESSION`'s data should be caried via this object and at script's end object shoud ensure storing all data in `$_SESSION`. Apart from that it shoud use custom save handlers as standard ones (AFAIK) does not allow to meet following requirement:
> If user accesses to obsolete session(expired session), deny access to it. It is recommended to remove authenticated status **from all** of the users' session because it is likely an attack.

For now my implementation touches only tip of an iceberg.

##### Dynamic properties - magic `__set` and `__get` methods
There are discussions about best practices of using or not magic `__set` and `__get` functions and there are provided [good points](https://www.masterzendframework.com/php/php-magic-methods-or-not/) not to use them ~~but in case of an object that is supposed to deliver data (like my Session) I agree with arguments provided in~~ [~~this reply~~](https://stackoverflow.com/a/6185525). On second tought I've decided to opt out from magic functions' use, especially that I'll make separate class for authentication that will just use Session class as dependency.

##### Authentication
Session management is so closely related to authentication that it is easy to forget that they are separate responsibilities. Session management should secure session and hide session internal security data (already mentioned in [Session Management Basics](http://php.net/manual/en/features.session.security.management.php)). Auhentication on the other hand should only care about login, logout and check actual login status and use session only as a way to store its data.

##### Active Record ORM (Anti-)Pattern
Unfortunately the point of this excercise was to use it. It is easy and intuitive on a first glance, but as soon as you need data from more than one table its limitations become obvious - ofcourse only if you limit implementation to just one layer and functionality to simple load/save/set/get methods and don't enhance it (like most frameworks do, to the point where they finally turn to more advanced patterns for their ORMs). There is more than a lot of articles about why Active Record is inappropriate for anything that is just a tiny bit more than forms over data:
- [AnemicDomainModel](https://martinfowler.com/bliki/AnemicDomainModel.html)
- [ORM anti-pattern series](https://www.mehdi-khalili.com/orm-anti-patterns-series)
- [How We Code: ORMs and Anemic Domain Models](http://fideloper.com/how-we-code)
- [Active Record vs Objects](https://sites.google.com/site/unclebobconsultingllc/active-record-vs-objects)
- And finally [Why active record sucks](https://kore-nordmann.de/blog/why_active_record_sucks.html)

I admit that I have strong procedural background (basic, pascal, c, x86, TI TMS320, Atmel AVR  assemblers) so it is hard to eradicate that mindset and turn to [Tell, Don't Ask](https://pragprog.com/articles/tell-dont-ask) paradigm. But Active Record even for me looks like anti-pattern. It is plain and simple but it's nothing more than persistence layer so most of domain logic will end up in fat controller.
