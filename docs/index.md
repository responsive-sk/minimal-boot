---
layout: default
title: "Minimal Boot Documentation"
description: "A lightweight, PSR-15 compliant web application framework with Domain-Driven Design principles"
---

# Minimal Boot Documentation

Welcome to the Minimal Boot documentation. This guide will help you understand, install, and use the Minimal Boot framework.

## What is Minimal Boot?

Minimal Boot is a lightweight, PSR-15 compliant web application framework built on top of Mezzio. It follows Domain-Driven Design principles and provides a clean, modular architecture for building modern web applications.

## Key Features

- **Domain-Driven Design** - Clean separation of concerns with Domain, Application, and Infrastructure layers
- **Modular Architecture** - Self-contained modules with their own handlers, templates, and services
- **Native PHP Templates** - No external template engine dependencies for better performance
- **PSR-15 Middleware** - Full PSR-15 compliance for HTTP message handling
- **Repository Pattern** - Abstracted data access with interface-based design
- **Code Quality** - PHPStan Level 8 and PSR-12 code standards

## Quick Start

```bash
# Clone the repository
git clone https://github.com/responsive-sk/minimal-boot.git
cd minimal-boot

# Install dependencies
composer install

# Start development server
php -S localhost:8080 -t public/
```

## Documentation Structure

### Getting Started
- [Installation](book/installation/) - Complete installation guide
- [Getting Started](book/getting-started/) - Your first steps with Minimal Boot
- [Architecture](book/architecture/) - Understanding the framework architecture

### Core Framework
- [Core Module](core/) - Essential infrastructure services
  - [Database Layer](core/database/) - Connections, migrations, and query building
  - [Template System](core/templates/) - Native PHP templating
  - [Compatibility Layer](core/compatibility/) - Shared hosting support

### Application Development
- [Modules](book/modules/) - Working with modular components
- [Templates](book/templates/) - Template system and layouts
- [Assets & Frontend](book/assets/) - Frontend build systems and styling
- [Domain Layer](book/domain/) - Domain-Driven Design implementation

## Requirements

- PHP 8.1 or higher
- Composer 2.0 or higher
- Web server (Apache, Nginx, or PHP built-in server)

## License

This project is open source and available under the MIT License.

## Support

- [GitHub Issues](https://github.com/responsive-sk/minimal-boot/issues)
- [Documentation](https://github.com/responsive-sk/minimal-boot/tree/main/docs)
- [Examples](https://github.com/responsive-sk/minimal-boot/tree/main/examples)
