# Symfony Demo Application

A message driven Symfony demo application with basic user management, a REST/GraphQL API and OAuth/JWT authentication.

[![Build status][travis:img]][travis]
[![Latest Stable Version][packagist:img]][packagist]

> [MsgPHP](https://msgphp.github.io/) is a project that aims to provide (common) message based domain layers for your application. It has a low development time overhead and avoids being overly opinionated.

# Enabled Bundles

## Domain Layer
[`MsgPhpUserBundle`](https://github.com/msgphp/user-bundle),
[`MsgPhpEavBundle`](https://github.com/msgphp/eav-bundle)

## ORM Layer
[`DoctrineBundle`](https://github.com/doctrine/DoctrineBundle)

## Security Layer
[`SecurityBundle`](https://github.com/symfony/security-bundle),
[`HWIOAuthBundle`](https://github.com/hwi/HWIOAuthBundle),
[`LexikJWTAuthenticationBundle`](https://github.com/lexik/LexikJWTAuthenticationBundle)

## API Layer
[`ApiPlatformBundle`](https://github.com/api-platform/api-platform)

# Try it Yourself

```bash
composer create-project msgphp/symfony-demo-app && cd symfony-demo-app/

# using built in web server / sqlite
bin/reinstall
bin/console server:run

# or using Lando
# see https://docs.devwithlando.io
lando start
lando ssh -c bin/reinstall
```

# Documentation

- Read the [main documentation](https://msgphp.github.io/docs)
- Get support on [Symfony's Slack `#msgphp` channel](https://symfony.com/slack-invite) or [raise an issue](https://github.com/msgphp/symfony-demo-app/issues/new)

# Contributing

See [`CONTRIBUTING.md`](CONTRIBUTING.md)

# Screenshots

## Web

![image](https://user-images.githubusercontent.com/1047696/45264235-c79eaa80-b439-11e8-87b2-4e3551bdee09.png)

![image](https://user-images.githubusercontent.com/1047696/45264184-c9b43980-b438-11e8-97e8-55b5150c7b6b.png)

## API

![image](https://user-images.githubusercontent.com/1047696/45264192-ea7c8f00-b438-11e8-9aa3-9bf490c4f2d1.png)

## CLI

![image](https://user-images.githubusercontent.com/1047696/45264197-0b44e480-b439-11e8-83c3-45753ef79dbc.png)

## Database UML

![image](https://user-images.githubusercontent.com/1047696/45264216-62e35000-b439-11e8-9c04-f835f46a857b.png)

# Blog Posts

- [Domain-driven-design: Projections in practice with API Platform and Elasticsearch](https://medium.com/@ro0NL/domain-driven-design-projections-in-practice-with-api-platform-and-elasticsearch-c785ed6d660b)
- [Adding user management to your Symfony application](https://medium.com/@ro0NL/adding-user-management-to-your-symfony-application-ceeefe2a2e9)
- [Domain-driven-design: Moving forward with API Platform and Elasticsearch](https://medium.com/@ro0NL/domain-driven-design-moving-forward-with-api-platform-and-elasticsearch-f1705614f9e2)
- [Domain-driven-design: Writing domain layers. The fast way.](https://medium.com/@ro0NL/domain-driven-design-writing-domain-layers-the-fast-way-60ef87399374)
- [Commanding a decoupled User entity](https://medium.com/@ro0NL/commanding-a-decoupled-user-entity-aee8723c43e5)
- [Decoupling the User entity with a new Symfony User Bundle](https://medium.com/@ro0NL/decoupling-the-user-entity-with-a-new-symfony-user-bundle-7d2d5d85bdf9)
- [Building a new Symfony User Bundle](https://medium.com/@ro0NL/building-a-new-symfony-user-bundle-b4fe5a9d9d80)

[travis]: https://travis-ci.org/msgphp/symfony-demo-app
[travis:img]: https://img.shields.io/travis/msgphp/symfony-demo-app/master.svg?style=flat-square
[packagist]: https://packagist.org/packages/msgphp/symfony-demo-app
[packagist:img]: https://img.shields.io/packagist/v/msgphp/symfony-demo-app.svg?style=flat-square
