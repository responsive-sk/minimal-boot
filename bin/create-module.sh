#!/bin/bash

# create-module.sh - Script for creating new Light framework modules
# Usage: ./bin/create-module.sh ModuleName

set -e

# Check if module name is provided
if [ -z "$1" ]; then
    echo "Usage: $0 ModuleName"
    echo "Example: $0 Contact"
    exit 1
fi

MODULE_NAME="$1"
MODULE_DIR="src/${MODULE_NAME}"
NAMESPACE="Light\\${MODULE_NAME}"

# Check if module already exists
if [ -d "$MODULE_DIR" ]; then
    echo "Error: Module '$MODULE_NAME' already exists in $MODULE_DIR"
    exit 1
fi

echo "Creating module: $MODULE_NAME"
echo "Directory: $MODULE_DIR"
echo "Namespace: $NAMESPACE"

# Create directory structure
mkdir -p "$MODULE_DIR"/{Handler,Factory,templates}

# Create ConfigProvider.php
cat > "$MODULE_DIR/ConfigProvider.php" << EOF
<?php

declare(strict_types=1);

namespace ${NAMESPACE};

/**
 * ${MODULE_NAME} module configuration provider.
 *
 * Provides configuration for ${MODULE_NAME} module functionality.
 */
class ConfigProvider
{
    /**
     * Return configuration for this module.
     *
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => \$this->getDependencies(),
            'templates'    => \$this->getTemplates(),
        ];
    }

    /**
     * Return dependency configuration.
     *
     * @return array<string, mixed>
     */
    public function getDependencies(): array
    {
        return [
            'delegators' => [
                // Route delegators will be added here
            ],
            'factories' => [
                // Handlers and services will be added here
            ],
        ];
    }

    /**
     * Returns the templates configuration.
     *
     * @return array<string, mixed>
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                // Template paths are managed centrally via TemplatePathProvider
                // See config/autoload/templates.global.php for configuration
            ],
        ];
    }
}
EOF

# Create RoutesDelegator.php
cat > "$MODULE_DIR/RoutesDelegator.php" << EOF
<?php

declare(strict_types=1);

namespace ${NAMESPACE};

use Mezzio\Application;
use Psr\Container\ContainerInterface;

/**
 * ${MODULE_NAME} routes delegator.
 *
 * Registers routes for the ${MODULE_NAME} module.
 */
class RoutesDelegator
{
    /**
     * @param ContainerInterface \$container
     * @param string \$serviceName
     * @param callable \$callback
     * @return Application
     */
    public function __invoke(
        ContainerInterface \$container,
        string \$serviceName,
        callable \$callback
    ): Application {
        /** @var Application \$app */
        \$app = \$callback();

        // Add ${MODULE_NAME} routes here
        // Example:
        // \$app->get('/${MODULE_NAME,,}', ${MODULE_NAME}Handler::class, '${MODULE_NAME,,}');

        return \$app;
    }
}
EOF

# Create sample Handler
HANDLER_NAME="${MODULE_NAME}Handler"
cat > "$MODULE_DIR/Handler/${HANDLER_NAME}.php" << EOF
<?php

declare(strict_types=1);

namespace ${NAMESPACE}\\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * ${MODULE_NAME} handler.
 *
 * Handles ${MODULE_NAME} related requests.
 */
class ${HANDLER_NAME} implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface \$template
    ) {
    }

    /**
     * Handle the request and return a response.
     */
    public function handle(ServerRequestInterface \$request): ResponseInterface
    {
        \$data = [
            'module' => '${MODULE_NAME}',
            'title' => '${MODULE_NAME} Page',
        ];

        return new HtmlResponse(
            \$this->template->render('${MODULE_NAME,,}::index', \$data)
        );
    }
}
EOF

# Create sample Factory
FACTORY_NAME="${MODULE_NAME}HandlerFactory"
cat > "$MODULE_DIR/Factory/${FACTORY_NAME}.php" << EOF
<?php

declare(strict_types=1);

namespace ${NAMESPACE}\\Factory;

use ${NAMESPACE}\\Handler\\${HANDLER_NAME};
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;

/**
 * Factory for ${HANDLER_NAME}.
 */
class ${FACTORY_NAME}
{
    /**
     * Create ${HANDLER_NAME} instance.
     */
    public function __invoke(ContainerInterface \$container): ${HANDLER_NAME}
    {
        \$template = \$container->get(TemplateRendererInterface::class);
        assert(\$template instanceof TemplateRendererInterface);

        return new ${HANDLER_NAME}(\$template);
    }
}
EOF

# Create sample template
cat > "$MODULE_DIR/templates/index.phtml" << EOF
<?php
/**
 * @var array \$data Template data
 * @var string \$title Page title
 * @var string \$module Module name
 */
\$this->layout('layout::default', ['title' => \$title ?? '${MODULE_NAME}']);
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1><?= \$this->escapeHtml(\$title ?? '${MODULE_NAME}') ?></h1>
            <p>Welcome to the <?= \$this->escapeHtml(\$module ?? '${MODULE_NAME}') ?> module!</p>
            
            <div class="alert alert-info">
                <strong>Module created successfully!</strong>
                <br>
                This is a sample template for the ${MODULE_NAME} module.
                <br>
                Edit <code>src/${MODULE_NAME}/templates/index.phtml</code> to customize this page.
            </div>
        </div>
    </div>
</div>
EOF

echo ""
echo "✅ Module '$MODULE_NAME' created successfully!"
echo ""
echo "📁 Created structure:"
echo "   $MODULE_DIR/"
echo "   ├── Handler/${HANDLER_NAME}.php"
echo "   ├── Factory/${FACTORY_NAME}.php"
echo "   ├── ConfigProvider.php"
echo "   ├── RoutesDelegator.php"
echo "   └── templates/index.phtml"
echo ""
echo "📝 Next steps:"
echo "   1. Add to composer.json autoload:"
echo "      \"${NAMESPACE}\\\\\": \"${MODULE_DIR}/\","
echo ""
echo "   2. Add to config/autoload/templates.global.php:"
echo "      '${MODULE_NAME,,}' => ['${MODULE_DIR}/templates'],"
echo ""
echo "   3. Add to config/autoload/paths.global.php:"
echo "      '${MODULE_NAME,,}_templates' => '${MODULE_DIR}/templates',"
echo ""
echo "   4. Register ConfigProvider in config/config.php"
echo ""
echo "   5. Run: composer dump-autoload"
