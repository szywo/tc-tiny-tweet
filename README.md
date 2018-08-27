## Tiny Tweet
### Test case - Database access using active row
#### :pushpin: Purpose
Implementation of tweeter-like application as an exercise on database and object oriented programming integration. Functionality shoud include login, register, view/post tweets, view/post comments, send/receive private messagges. Only login and register forms should be available for not logged-in users (publicly available), other views/forms sould be for logged-in users only (private). Additionaly I'm going to adhere to [php-fig.org recomendations](https://www.php-fig.org).

:exclamation: **SECURITY DISCLAIMER: This project is not intended for production environment** :exclamation:

Security is not a topic of this exercise so beside absolutly basic measures (using sessions) there are no other features concerning security (secure login/transport path/integrity etc.).

#### :heavy_check_mark: Implementation progress (unordered list)

- [x] Url rewriting
- [x] Session (see disclaimer above)
    - [x] Authentication
- [x] Routing
- [x] Template engine
- [ ] Page templates
    - [x] Login
    - [x] Register
    - [x] 404
    - [ ] ...
- [ ] Database Access
- [ ] Application Logic

#### :paperclip: Notes
These are to log research effort I put into this project and also as a reference fordevelopment  choices I made.

##### mod_rewrite
Important part to remember is that, contrary to the order of placement, `RewriteRule` matching takes precedence before `RewriteCond` matching. So if `RewriteRule` pattern does not catch desired url then `RewriteCond` is not even tried. I've learned that by reading through mod_rewrite logs first and then finding [Ruleset Processing](https://httpd.apache.org/docs/2.4/rewrite/tech.html#InternalRuleset) page. I strongly recomend using `LogLevel` directive for testing rewrite rules. Its only drawback is that it cannot be used in `.htaccess` file, so it usually means that you have to setup your own LAMP server. But that is the way it should be done because excessive logging decreases server's performance.

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
