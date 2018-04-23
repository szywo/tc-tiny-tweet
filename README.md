## Tiny Tweet
### Test case - Database access using active row
#### :microscope: Goals
Implementation of tweeter-like application as an exercise on database and object oriented programming integration. Functionality shoud include login, register, view/post tweets, view/post comments, send/receive private messagges. Only login and register forms should be available for not logged-in users (publicly available), other views/forms sould be for logged-in users only (private). Additionaly I will try to adhere to [php-fig.org recomendations](https://www.php-fig.org).

:exclamation: **SECURITY DISCLAIMER: This project is not intended for production environment** :exclamation:
Security is not a topic of this exercise so beside absolutly basic measures (using sessions) there are no other features concerning security (secure login/transport path/integrity etc.).

#### :memo: Implementation progress/roadmap

- [ ] Database connectivity
- [ ] Database structure/model (tables/classes)
  - [ ] Users
  - [ ] Tweets
  - [ ] Comments
  - [ ] Private messages
- [ ] Simple authentication module (see disclaimer above)
- [ ] Pages (views + forms)
    - [ ] Main (Tweet list + form) (private)
      - [ ] Redirection to login page if user is not logged-in
      - [ ] Add tweet form
    - [x] Login form (public)
    - [x] Register form (public)
    - [ ] Tweets by user view (private)
      - [ ] User's tweets (+comment counters) list
      - [ ] Private message button/form
    - [ ] Tweet and comments (private)
      - [ ] Add comment form
    - [ ] User profile edit form (private)
    - [ ] Private messages list - sent and received (private)
    - [ ] Private message view (private)
      - [ ] \(optional) Reply form
- [ ] Error handling/logging module
