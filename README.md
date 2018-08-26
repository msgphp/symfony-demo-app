# Symfony Demo Application

A message driven Symfony demo application with basic user management, a REST/GraphQL API and OAuth/JWT authentication.

[![Latest Stable Version](https://poser.pugx.org/msgphp/symfony-demo-app/v/stable)](https://packagist.org/packages/msgphp/symfony-demo-app)

> [MsgPHP](https://msgphp.github.io/) is a project that aims to provide (common) message based domain layers for your application. It has a low development time overhead and avoids being overly opinionated.

## Enabled bundles

### Domain layer
[`MsgPhpUserBundle`](https://github.com/msgphp/user-bundle),
[`MsgPhpEavBundle`](https://github.com/msgphp/eav-bundle)

### ORM layer
[`DoctrineBundle`](https://github.com/doctrine/DoctrineBundle)

### Security layer
[`SecurityBundle`](https://github.com/symfony/security-bundle),
[`HWIOAuthBundle`](https://github.com/hwi/HWIOAuthBundle),
[`LexikJWTAuthenticationBundle`](https://github.com/lexik/LexikJWTAuthenticationBundle)

### API layer
[`ApiPlatformBundle`](https://github.com/api-platform/api-platform)

## Try it yourself

```bash
composer create-project msgphp/symfony-demo-app && cd symfony-demo-app/

# using built in web server / sqlite
bin/reset-env
bin/console server:run

# or using Lando / mariadb
# see https://docs.devwithlando.io
lando start
lando reset-env
```

## Documentation

- Read the [main documentation](https://msgphp.github.io/docs)
- Get support on [Symfony's Slack `#msgphp` channel](https://symfony.com/slack-invite) or [raise an issue](https://github.com/msgphp/symfony-demo-app/issues/new)

## Screenshots

### Web
![image](https://user-images.githubusercontent.com/1047696/39675143-4d22d438-5157-11e8-8bed-fe0762b0c97a.png)

### API
![image](https://user-images.githubusercontent.com/1047696/44625637-8329ef80-a90e-11e8-99a1-9dae3377cb0d.png)

### CLI
![image](https://user-images.githubusercontent.com/1047696/37556509-802f98e8-29f7-11e8-9ccd-6112a9bedfb5.png)

### Database UML
![image](https://user-images.githubusercontent.com/1047696/37556527-e7ead33a-29f7-11e8-84bd-0a4f0c64c871.png)

## Blog posts

- [Domain-driven-design: Projections in practice with API Platform and Elasticsearch](https://medium.com/@ro0NL/domain-driven-design-projections-in-practice-with-api-platform-and-elasticsearch-c785ed6d660b)
- [Adding user management to your Symfony application](https://medium.com/@ro0NL/adding-user-management-to-your-symfony-application-ceeefe2a2e9)
- [Domain-driven-design: Moving forward with API Platform and Elasticsearch](https://medium.com/@ro0NL/domain-driven-design-moving-forward-with-api-platform-and-elasticsearch-f1705614f9e2)
- [Domain-driven-design: Writing domain layers. The fast way.](https://medium.com/@ro0NL/domain-driven-design-writing-domain-layers-the-fast-way-60ef87399374)
- [Commanding a decoupled User entity](https://medium.com/@ro0NL/commanding-a-decoupled-user-entity-aee8723c43e5)
- [Decoupling the User entity with a new Symfony User Bundle](https://medium.com/@ro0NL/decoupling-the-user-entity-with-a-new-symfony-user-bundle-7d2d5d85bdf9)
- [Building a new Symfony User Bundle](https://medium.com/@ro0NL/building-a-new-symfony-user-bundle-b4fe5a9d9d80)
