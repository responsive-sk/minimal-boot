.
├── bin
│   ├── build-production-secure.php
│   ├── build.sh
│   ├── clear-config-cache.php
│   ├── composer-post-install-script.php
│   └── create-module.sh
├── config
│   ├── autoload
│   │   ├── app.global.php
│   │   ├── debugbar.local.php.dist
│   │   ├── dependencies.global.php
│   │   ├── development.local.php.dist
│   │   ├── error-handling.global.php
│   │   ├── local.php
│   │   ├── local.php.dist
│   │   ├── mezzio.global.php
│   │   ├── paths.global.php
│   │   ├── paths.production.php
│   │   └── templates.global.php
│   ├── build.php
│   ├── build.php.dist
│   ├── config.php
│   ├── container.php
│   ├── development.config.php
│   ├── development.config.php.dist
│   ├── pipeline.php
│   └── twig-cs-fixer.php
├── src
│   ├── App
│   │   ├── Factory
│   │   │   ├── BootstrapDemoHandlerFactory.php
│   │   │   ├── CommunityHandlerFactory.php
│   │   │   ├── ContactHandlerFactory.php
│   │   │   ├── GetIndexViewHandlerFactory.php
│   │   │   ├── MainDemoHandlerFactory.php
│   │   │   ├── PathsExampleHandlerFactory.php
│   │   │   ├── PathsFactory.php
│   │   │   ├── TemplatePathsFactory.php
│   │   │   ├── WhatWeOfferHandlerFactory.php
│   │   │   └── WorkHandlerFactory.php
│   │   ├── Handler
│   │   │   ├── BootstrapDemoHandler.php
│   │   │   ├── CommunityHandler.php
│   │   │   ├── ContactHandler.php
│   │   │   ├── GetIndexViewHandler.php
│   │   │   ├── MainDemoHandler.php
│   │   │   ├── PathsExampleHandler.php
│   │   │   ├── WhatWeOfferHandler.php
│   │   │   └── WorkHandler.php
│   │   ├── templates
│   │   │   ├── bootstrap-demo.phtml
│   │   │   ├── community.phtml
│   │   │   ├── index.html.twig
│   │   │   ├── index.phtml
│   │   │   ├── main-demo.phtml
│   │   │   ├── what-we-offer.phtml
│   │   │   └── work.phtml
│   │   ├── ConfigProvider.php
│   │   └── RoutesDelegator.php
│   ├── Auth
│   │   ├── Factory
│   │   │   └── AuthHandlerFactory.php
│   │   ├── Handler
│   │   │   └── AuthHandler.php
│   │   ├── templates
│   │   │   └── index.phtml
│   │   ├── ConfigProvider.php
│   │   └── RoutesDelegator.php
│   ├── Contact
│   │   ├── Factory
│   │   │   └── ContactHandlerFactory.php
│   │   ├── Handler
│   │   │   ├── ContactHandler.php
│   │   │   └── TestHandler.php
│   │   ├── templates
│   │   │   ├── contact.phtml
│   │   │   └── test.phtml
│   │   ├── ConfigProvider.php
│   │   └── RoutesDelegator.php
│   ├── Core
│   │   ├── Compatibility
│   │   │   ├── FunctionChecker.php
│   │   │   └── SafeFileOperations.php
│   │   ├── Factory
│   │   │   ├── NativePhpRendererFactory.php
│   │   │   └── TemplatePathProviderFactory.php
│   │   ├── Handler
│   │   ├── Middleware
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
│   │   ├── Factory
│   │   │   ├── GetPageViewHandlerFactory.php
│   │   │   └── PageServiceFactory.php
│   │   ├── Handler
│   │   │   └── GetPageViewHandler.php
│   │   ├── Service
│   │   │   ├── PageServiceInterface.php
│   │   │   └── PageService.php
│   │   ├── templates
│   │   │   └── page
│   │   │       ├── about.html.twig
│   │   │       ├── about.phtml
│   │   │       ├── who-we-are.html.twig
│   │   │       └── who-we-are.phtml
│   │   ├── ConfigProvider.php
│   │   └── RoutesDelegator.php
│   ├── Session
│   │   ├── Factory
│   │   │   └── SessionHandlerFactory.php
│   │   ├── Handler
│   │   │   └── SessionHandler.php
│   │   ├── templates
│   │   │   └── index.phtml
│   │   ├── ConfigProvider.php
│   │   └── RoutesDelegator.php
│   ├── Shared
│   │   ├── templates
│   │   │   ├── error
│   │   │   │   ├── 404.html.twig
│   │   │   │   ├── 404.phtml
│   │   │   │   ├── error.html.twig
│   │   │   │   └── error.phtml
│   │   │   ├── layout
│   │   │   │   ├── bootstrap.phtml
│   │   │   │   ├── default.html.twig
│   │   │   │   ├── default.phtml
│   │   │   │   ├── footer.phtml
│   │   │   │   ├── header.phtml
│   │   │   │   ├── main.phtml
│   │   │   │   └── tailwind.phtml
│   │   │   └── partial
│   │   │       └── alerts.html.twig
│   │   └── ConfigProvider.php
│   └── Templates
│       ├── bootstrap
│       │   ├── src
│       │   │   ├── main.js
│       │   │   └── style.css
│       │   ├── package.json
│       │   ├── package-lock.json
│       │   ├── pnpm-lock.yaml
│       │   └── vite.config.js
│       └── main
│           ├── src
│           │   ├── images
│           │   │   ├── icons
│           │   │   │   ├── checking.svg
│           │   │   │   ├── done.svg
│           │   │   │   ├── play.svg
│           │   │   │   ├── progress.svg
│           │   │   │   ├── telegram.svg
│           │   │   │   ├── time-forward.svg
│           │   │   │   ├── time.svg
│           │   │   │   └── youtube.svg
│           │   │   ├── nav
│           │   │   │   ├── logo.svg
│           │   │   │   └── logo-svgo.svg
│           │   │   ├── apple-touch-icon.png
│           │   │   ├── checking.svg
│           │   │   ├── digital-marketing.jpg
│           │   │   ├── done.svg
│           │   │   ├── favicon-32x32.png
│           │   │   ├── favicon.ico
│           │   │   ├── javascript.jpg
│           │   │   ├── php82.jpg
│           │   │   ├── play.svg
│           │   │   ├── progress.svg
│           │   │   ├── telegram.svg
│           │   │   ├── time-forward.svg
│           │   │   ├── time.svg
│           │   │   ├── web-dev.jpg
│           │   │   ├── welcome.jpg
│           │   │   └── youtube.svg
│           │   ├── main.js
│           │   └── style.css
│           ├── package.json
│           ├── pnpm-lock.yaml
│           ├── pnpm-workspace.yaml
│           ├── postcss.config.js
│           ├── tailwind.config.js
│           └── vite.config.js
├── var
│   ├── cache
│   ├── data
│   │   └── cache
│   │       └── .gitignore
│   ├── logs
│   │   ├── error-log-2025-07-22.log
│   │   ├── error-log-2025-07-26.log
│   │   ├── error-log-2025-07-27.log
│   │   └── error-log-2025-07-28.log
│   ├── sessions
│   ├── storage
│   └── tmp
├── composer.json
├── composer.lock
├── directory_tree.md
├── .gitattributes
├── .gitignore
├── .php-cs-fixer.php
├── phpcs.xml
├── phpstan.neon
├── phpunit.xml
└── .twig-cs-fixer.cache

56 directories, 158 files
