## Tiny Tweet
### Test case - Database access using active row
#### :pushpin: Purpose
Implementation of tweeter-like application as an exercise on database and object oriented programming integration. Functionality shoud include login, register, view/post tweets, view/post comments, send/receive private messagges. Only login and register forms should be available for not logged-in users (publicly available), other views/forms sould be for logged-in users only (private). Additionaly I'm going to adhere to [php-fig.org recomendations](https://www.php-fig.org).

:exclamation: **SECURITY DISCLAIMER: This project is not intended for production environment** :exclamation:
Security is not a topic of this exercise so beside absolutly basic measures (using sessions) there are no other features concerning security (secure login/transport path/integrity etc.).

#### :white_check_mark: Implementation progress

- [x] Url rewriting
- [ ] Session/authentication (see disclaimer above)
- [ ] Routing
- [ ] Templates
- [ ] Database Access
- [ ] Application Logic

#### :paperclip: Notes

##### mod_rewrite
Important part to remember is that, contrary to the order of placement, `RewriteRule` matching takes precedence before `RewriteCond` matching. So if `RewriteRule` pattern does not catch desired url then `RewriteCond` is not even tried. I've learned that by reading through mod_rewrite logs first and then finding [Ruleset Processing](https://httpd.apache.org/docs/2.4/rewrite/tech.html#InternalRuleset) page. I strongly recomend using `LogLevel` directive for testing rewrite rules. Its only drawback is that it cannot be used in `.htaccess` file, so it usually means tahat you have to setup your own LAMP server. But that is the way it should be done because excessive logging decreases server's performance.
