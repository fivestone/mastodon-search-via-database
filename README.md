# Mastodon - Search via Database

A simple PHP shell that searches toots directly from the mastodon database via SQL.

## Description

Mastodon's elastic search costs a huge amount of additional memory but does not work well, especially for non-latin characters. Thus, as a single-user instance, I would rather search directly in the database, then write a simple page for that.

I have considered to make it suitable for small instances that contain more than one user, but rapidly realized that's a bad idea. It seems not possible to replicate the complex permission control of Mastodon into this PHP page, so I give up. It is strongly NOT recommended to open this program to any non-admin users. Actually, it should NOT be used on any instance which has more than one user.

## Getting Started

### Dependencies

* php 7.0+, with pd and pgsql extensions
* access to PostgreSQL of the mastodon instance

### Installing

* Copy the files to any folder or subfolder on the php web server.
* In config.php, Modify database info and instance name.

### Executing program

* Visit the web folder in browser, login with mastodon's email and password.

## Version History

* 0.1
    * Initial Release

## Roadmap

- [ ] query from my retoots
- [ ] hide toots from locked users.
- [ ] A better interface...

