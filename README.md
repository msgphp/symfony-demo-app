# Symfony Demo Application

A Symfony demo application with basic user management, a REST/GraphQL API and OAuth/JWT authentication.

[![Build status][travis:img]][travis]
[![Latest Stable Version][packagist:img]][packagist]

> MsgPHP is a project that aims to provide reusable domain layers for your application. It has a low development time
overhead and avoids being overly opinionated.

## Enabled Bundles

Concern  | Bundles
---      | ---
Domain   | [`MsgPhpUserBundle`], [`MsgPhpEavBundle`]
ORM      | [`DoctrineBundle`]
Security | [`SecurityBundle`], [`HWIOAuthBundle`], [`LexikJWTAuthenticationBundle`]
Web API  | [`ApiPlatformBundle`]

## Features

- Register, Login, Forgot Password, My Profile
- One-Time-Login tokens
- Register invitations
- Primary e-mail and secondary e-mails

## Try it Yourself

Using [Symfony CLI][appsrv:sf]:

```bash
composer create-project msgphp/symfony-demo-app
cd symfony-demo-app

# Database and Elasticsearch must be running
# Change DATABASE_URL and ELASTICSEARCH_HOST in .env.local, if needed

bin/console doctrine:database:create --if-not-exists
bin/console doctrine:schema:update --force
bin/console doctrine:fixtures:load -n

bin/console projection:initialize-types --force
bin/console projection:synchronize

symfony server:start
symfony open:local
``` 

Using [Docker][appsrv:docker]:

```bash
# assuming composer is not installed on the local machine
git clone git@github.com:msgphp/symfony-demo-app.git
cd symfony-demo-app
cp .env.local.dist .env.local

make build start install db-fixtures api-sync

# open https://localhost:8443
```

# Documentation

- Read the [main documentation](https://msgphp.github.io/docs)
- Get support on [Symfony's Slack `#msgphp` channel](https://symfony.com/slack-invite) or [raise an issue](https://github.com/msgphp/symfony-demo-app/issues/new)

# Contributing

See [`CONTRIBUTING.md`](CONTRIBUTING.md)

# Screenshots

`v1.x` | 08-2018
--- | ---
![screen:login] | ![screen:profile]
![screen:api] | ![screen:cli]
![screen:uml] |

# Blog Posts

- [Domain-driven-design: Projections in practice with API Platform and Elasticsearch](https://medium.com/@ro0NL/domain-driven-design-projections-in-practice-with-api-platform-and-elasticsearch-c785ed6d660b)
- [Adding user management to your Symfony application](https://medium.com/@ro0NL/adding-user-management-to-your-symfony-application-ceeefe2a2e9)
- [Domain-driven-design: Moving forward with API Platform and Elasticsearch](https://medium.com/@ro0NL/domain-driven-design-moving-forward-with-api-platform-and-elasticsearch-f1705614f9e2)
- [Domain-driven-design: Writing domain layers. The fast way.](https://medium.com/@ro0NL/domain-driven-design-writing-domain-layers-the-fast-way-60ef87399374)
- [Commanding a decoupled User entity](https://medium.com/@ro0NL/commanding-a-decoupled-user-entity-aee8723c43e5)
- [Decoupling the User entity with a new Symfony User Bundle](https://medium.com/@ro0NL/decoupling-the-user-entity-with-a-new-symfony-user-bundle-7d2d5d85bdf9)
- [Building a new Symfony User Bundle](https://medium.com/@ro0NL/building-a-new-symfony-user-bundle-b4fe5a9d9d80)

[travis]: https://travis-ci.com/msgphp/symfony-demo-app
[travis:img]: https://img.shields.io/travis/com/msgphp/symfony-demo-app/master.svg?style=flat-square
[packagist]: https://packagist.org/packages/msgphp/symfony-demo-app
[packagist:img]: https://img.shields.io/packagist/v/msgphp/symfony-demo-app.svg?style=flat-square
[appsrv:sf]: https://symfony.com/doc/current/setup/symfony_server.html
[appsrv:docker]: https://github.com/ro0NL/symfony-docker
[`MsgPhpUserBundle`]: https://github.com/msgphp/user-bundle
[`MsgPhpEavBundle`]: https://github.com/msgphp/eav-bundle
[`DoctrineBundle`]: https://github.com/doctrine/DoctrineBundle
[`SecurityBundle`]: https://github.com/symfony/security-bundle
[`HWIOAuthBundle`]: https://github.com/hwi/HWIOAuthBundle
[`LexikJWTAuthenticationBundle`]: https://github.com/lexik/LexikJWTAuthenticationBundle
[`ApiPlatformBundle`]: https://github.com/api-platform/api-platform
[screen:login]: https://user-images.githubusercontent.com/1047696/45264235-c79eaa80-b439-11e8-87b2-4e3551bdee09.png
[screen:profile]: https://user-images.githubusercontent.com/1047696/45264184-c9b43980-b438-11e8-97e8-55b5150c7b6b.png
[screen:api]: https://user-images.githubusercontent.com/1047696/45264192-ea7c8f00-b438-11e8-9aa3-9bf490c4f2d1.png
[screen:cli]: https://user-images.githubusercontent.com/1047696/45264197-0b44e480-b439-11e8-83c3-45753ef79dbc.png
[screen:uml]: https://user-images.githubusercontent.com/1047696/45264216-62e35000-b439-11e8-9c04-f835f46a857b.png
