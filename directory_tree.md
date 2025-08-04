.
├── config
│   ├── autoload
│   │   ├── app.global.php
│   │   ├── database.global.php
│   │   ├── debugbar.local.php.dist
│   │   ├── dependencies.global.php
│   │   ├── development.local.php.dist
│   │   ├── error-handling.global.php
│   │   ├── local.php
│   │   ├── local.php.dist
│   │   ├── mezzio.global.php
│   │   ├── paths.global.php
│   │   ├── paths.production.php
│   │   ├── production.local.php
│   │   └── templates.global.php
│   ├── build.php
│   ├── build.php.dist
│   ├── config.php
│   ├── container.php
│   ├── development.config.php
│   ├── development.config.php.dist
│   └── pipeline.php
├── docs
│   ├── _book
│   │   ├── architecture.md
│   │   ├── assets.md
│   │   ├── database.md
│   │   ├── domain.md
│   │   ├── getting-started.md
│   │   ├── installation.md
│   │   ├── modules.md
│   │   └── templates.md
│   ├── _core
│   │   ├── compatibility.md
│   │   ├── database.md
│   │   ├── index.md
│   │   ├── overview.md
│   │   └── templates.md
│   ├── _layouts
│   │   ├── default.html
│   │   └── page.html
│   ├── _config.yml
│   ├── DEPLOYMENT.md
│   ├── Gemfile
│   ├── index.md
│   ├── README.md.bak
│   └── TEMPLATES.md
├── .github
│   └── workflows
│       ├── ci.yml
│       └── quality.yml
├── src
│   ├── Assets
│   │   ├── bootstrap
│   │   │   ├── src
│   │   │   │   ├── fonts
│   │   │   │   │   └── source-sans-pro
│   │   │   │   │       ├── source-sans-pro-300.woff
│   │   │   │   │       ├── source-sans-pro-300.woff2
│   │   │   │   │       ├── source-sans-pro-400.woff
│   │   │   │   │       ├── source-sans-pro-400.woff2
│   │   │   │   │       ├── source-sans-pro-600.woff
│   │   │   │   │       ├── source-sans-pro-600.woff2
│   │   │   │   │       ├── source-sans-pro-700.woff
│   │   │   │   │       ├── source-sans-pro-700.woff2
│   │   │   │   │       └── source-sans-pro.css
│   │   │   │   ├── main.js
│   │   │   │   └── style.css
│   │   │   ├── package.json
│   │   │   ├── pnpm-lock.yaml
│   │   │   └── vite.config.js
│   │   ├── critical
│   │   │   ├── bootstrap-critical.css
│   │   │   ├── inject-critical.php
│   │   │   └── tailwind-critical.css
│   │   └── main
│   │       ├── src
│   │       │   ├── fonts
│   │       │   │   └── source-sans-pro
│   │       │   │       ├── source-sans-pro-300.woff
│   │       │   │       ├── source-sans-pro-300.woff2
│   │       │   │       ├── source-sans-pro-400.woff
│   │       │   │       ├── source-sans-pro-400.woff2
│   │       │   │       ├── source-sans-pro-600.woff
│   │       │   │       ├── source-sans-pro-600.woff2
│   │       │   │       ├── source-sans-pro-700.woff
│   │       │   │       ├── source-sans-pro-700.woff2
│   │       │   │       └── source-sans-pro.css
│   │       │   ├── images
│   │       │   │   ├── icons
│   │       │   │   │   ├── checking.svg
│   │       │   │   │   ├── done.svg
│   │       │   │   │   ├── play.svg
│   │       │   │   │   ├── progress.svg
│   │       │   │   │   ├── telegram.svg
│   │       │   │   │   ├── time-forward.svg
│   │       │   │   │   ├── time.svg
│   │       │   │   │   └── youtube.svg
│   │       │   │   ├── nav
│   │       │   │   │   ├── logo.svg
│   │       │   │   │   └── logo-svgo.svg
│   │       │   │   ├── apple-touch-icon.png
│   │       │   │   ├── checking.svg
│   │       │   │   ├── digital-marketing.jpg
│   │       │   │   ├── done.svg
│   │       │   │   ├── favicon-32x32.png
│   │       │   │   ├── favicon.ico
│   │       │   │   ├── javascript.jpg
│   │       │   │   ├── php82.jpg
│   │       │   │   ├── play.svg
│   │       │   │   ├── progress.svg
│   │       │   │   ├── telegram.svg
│   │       │   │   ├── time-forward.svg
│   │       │   │   ├── time.svg
│   │       │   │   ├── web-dev.jpg
│   │       │   │   ├── welcome.jpg
│   │       │   │   └── youtube.svg
│   │       │   ├── alpine-demo.js
│   │       │   ├── main.js
│   │       │   └── style.css
│   │       ├── package.json
│   │       ├── pnpm-lock.yaml
│   │       ├── pnpm-workspace.yaml
│   │       ├── postcss.config.js
│   │       ├── tailwind.config.js
│   │       └── vite.config.js
│   ├── Auth
│   │   ├── Factory
│   │   │   └── AuthHandlerFactory.php
│   │   ├── Handler
│   │   │   └── AuthHandler.php
│   │   ├── ConfigProvider.php
│   │   └── RoutesDelegator.php
│   ├── Contact
│   │   ├── Factory
│   │   │   └── ContactHandlerFactory.php
│   │   ├── Handler
│   │   │   ├── ContactHandler.php
│   │   │   └── TestHandler.php
│   │   ├── ConfigProvider.php
│   │   └── RoutesDelegator.php
│   ├── Core
│   │   ├── Compatibility
│   │   │   ├── FunctionChecker.php
│   │   │   └── SafeFileOperations.php
│   │   ├── Database
│   │   │   ├── Connection
│   │   │   │   └── DatabaseConnectionFactory.php
│   │   │   ├── Migration
│   │   │   │   └── MigrationRunner.php
│   │   │   └── Query
│   │   │       └── QueryBuilder.php
│   │   ├── Factory
│   │   │   ├── DatabaseConnectionFactoryFactory.php
│   │   │   ├── NativePhpRendererFactory.php
│   │   │   ├── PathsFactory.php
│   │   │   └── TemplatePathProviderFactory.php
│   │   ├── Service
│   │   │   ├── ConfigBasedTemplatePathProvider.php
│   │   │   └── TemplatePathProviderInterface.php
│   │   ├── Template
│   │   │   ├── Exception
│   │   │   │   ├── TemplateException.php
│   │   │   │   ├── TemplateNotFoundException.php
│   │   │   │   └── TemplateRenderException.php
│   │   │   └── NativePhpRenderer.php
│   │   └── ConfigProvider.php
│   ├── Page
│   │   ├── Domain
│   │   │   ├── Entity
│   │   │   │   └── Page.php
│   │   │   ├── Repository
│   │   │   │   ├── InMemoryPageRepository.php
│   │   │   │   └── PageRepositoryInterface.php
│   │   │   └── Service
│   │   │       ├── PageServiceInterface.php
│   │   │       └── PageService.php
│   │   ├── Factory
│   │   │   ├── BootstrapDemoHandlerFactory.php
│   │   │   ├── DemoHandlerFactory.php
│   │   │   ├── GetPageViewHandlerFactory.php
│   │   │   ├── IndexHandlerFactory.php
│   │   │   ├── PageRepositoryFactory.php
│   │   │   ├── PageServiceFactory.php
│   │   │   └── PdoPageRepositoryFactory.php
│   │   ├── Handler
│   │   │   ├── BootstrapDemoHandler.php
│   │   │   ├── DemoHandler.php
│   │   │   ├── GetPageViewHandler.php
│   │   │   └── IndexHandler.php
│   │   ├── Infrastructure
│   │   │   └── Repository
│   │   │       └── PdoPageRepository.php
│   │   ├── ConfigProvider.php
│   │   └── RoutesDelegator.php
│   ├── Session
│   │   ├── Factory
│   │   │   └── SessionHandlerFactory.php
│   │   ├── Handler
│   │   │   └── SessionHandler.php
│   │   ├── ConfigProvider.php
│   │   └── RoutesDelegator.php
│   ├── Shared
│   │   ├── Factory
│   │   │   ├── ThemeMiddlewareFactory.php
│   │   │   ├── ThemeServiceFactory.php
│   │   │   ├── ThemeSwitchHandlerFactory.php
│   │   │   └── ThemeTemplateMiddlewareFactory.php
│   │   ├── Handler
│   │   │   └── ThemeSwitchHandler.php
│   │   ├── Middleware
│   │   │   ├── ThemeMiddleware.php
│   │   │   └── ThemeTemplateMiddleware.php
│   │   ├── Service
│   │   │   ├── ThemeAwareTemplateService.php
│   │   │   └── ThemeService.php
│   │   ├── ConfigProvider.php
│   │   └── RoutesDelegator.php
│   └── User
│       ├── Application
│       │   └── Form
│       │       ├── LoginForm.php
│       │       └── RegistrationForm.php
│       ├── Domain
│       │   ├── Entity
│       │   │   ├── User.php
│       │   │   ├── UserRole.php
│       │   │   └── UserStatus.php
│       │   ├── Repository
│       │   │   └── UserRepositoryInterface.php
│       │   └── Service
│       │       ├── AuthenticationService.php
│       │       └── UserService.php
│       ├── Factory
│       │   ├── AuthenticationMiddlewareFactory.php
│       │   ├── AuthenticationServiceFactory.php
│       │   ├── DashboardHandlerFactory.php
│       │   ├── LoginHandlerFactory.php
│       │   ├── LogoutHandlerFactory.php
│       │   ├── PdoUserRepositoryFactory.php
│       │   ├── RegisterHandlerFactory.php
│       │   └── UserServiceFactory.php
│       ├── Handler
│       │   ├── DashboardHandler.php
│       │   ├── LoginHandler.php
│       │   ├── LogoutHandler.php
│       │   └── RegisterHandler.php
│       ├── Infrastructure
│       │   └── Repository
│       │       └── PdoUserRepository.php
│       ├── Middleware
│       │   ├── AuthenticationMiddleware.php
│       │   └── PermissionMiddleware.php
│       ├── ConfigProvider.php
│       └── RoutesDelegator.php
├── templates
│   ├── components
│   │   ├── forms
│   │   └── ui
│   ├── modules
│   │   ├── auth
│   │   │   └── index.phtml
│   │   ├── contact
│   │   │   ├── contact.phtml
│   │   │   └── test.phtml
│   │   ├── page
│   │   │   ├── about.phtml
│   │   │   ├── view.phtml
│   │   │   └── who-we-are.phtml
│   │   ├── session
│   │   │   └── index.phtml
│   │   └── user
│   │       ├── dashboard.phtml
│   │       ├── login.phtml
│   │       └── register.phtml
│   ├── shared
│   │   ├── email
│   │   └── error
│   │       ├── 403.phtml
│   │       ├── 404.phtml
│   │       └── error.phtml
│   └── themes
│       ├── bootstrap
│       │   ├── layouts
│       │   │   ├── app.phtml
│       │   │   └── default.phtml
│       │   ├── pages
│       │   │   ├── demo.phtml
│       │   │   └── home.phtml
│       │   └── partials
│       └── tailwind
│           ├── layouts
│           │   ├── app.phtml
│           │   ├── default.phtml
│           │   └── demo.phtml
│           ├── pages
│           │   ├── demo.phtml
│           │   └── home.phtml
│           └── partials
├── tests
│   ├── Integration
│   │   └── Page
│   │       └── Infrastructure
│   │           └── Repository
│   │               └── PdoPageRepositoryTest.php
│   ├── Unit
│   │   ├── Core
│   │   │   └── Database
│   │   │       ├── Connection
│   │   │       │   ├── CompleteDatabaseConnectionFactoryTest.php
│   │   │       │   └── DatabaseConnectionFactoryTest.php
│   │   │       └── Query
│   │   │           ├── QueryBuilderTest.php
│   │   │           └── SimpleQueryBuilderTest.php
│   │   ├── Page
│   │   │   ├── Domain
│   │   │   │   ├── Entity
│   │   │   │   │   └── PageTest.php
│   │   │   │   └── Service
│   │   │   │       └── PageServiceTest.php
│   │   │   └── Handler
│   │   │       ├── CompleteGetPageViewHandlerTest.php
│   │   │       └── GetPageViewHandlerTest.php
│   │   ├── User
│   │   │   └── Domain
│   │   │       └── Service
│   │   │           └── UserServiceTest.php
│   │   └── SimpleTest.php
│   ├── bootstrap.php
│   └── TestCase.php
├── var
│   ├── cache
│   ├── data
│   │   └── cache
│   │       └── .gitignore
│   ├── db
│   │   ├── page.sqlite
│   │   └── user.sqlite
│   ├── logs
│   │   ├── error-log-2025-08-03.log
│   │   └── junit.xml
│   ├── migrations
│   │   ├── page
│   │   │   ├── 2025_01_02_120000_create_pages_table.sql
│   │   │   └── 2025_08_02_095213_add_author_column.sql
│   │   └── user
│   │       └── 2025_08_02_200000_create_users_table.sql
│   ├── sessions
│   ├── storage
│   └── tmp
├── build-assets.sh
├── CODECOV_SETUP.md
├── codecov.yml
├── composer.json
├── composer.lock
├── cookies.txt
├── directory_tree.md
├── .env.production
├── .gitattributes
├── .gitignore
├── .php-cs-fixer.php
├── phpcs.xml
├── phpstan.neon
├── phpunit.xml
├── .pre-commit-config.yaml
├── README.md
├── TEMPLATE_REFACTOR_PLAN.md
└── test-repository.php

120 directories, 249 files
