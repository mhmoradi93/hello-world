# WP REST Resource Kit

A small, production-minded WordPress plugin built to demonstrate how I structure custom plugin code, REST API endpoints, repository logic, validation, and capability checks.

This repository is intentionally separate from my commercial/startup projects. It is a compact code sample for technical review.

## What it demonstrates

- Modern PHP with strict types and namespaces
- Custom WordPress plugin architecture
- Custom post type registration
- Dedicated repository layer
- REST API routes for list, detail, and create operations
- Capability-based authorization
- Input sanitization and validation
- WordPress-native error handling
- PHPCS / WordPress Coding Standards configuration
- Clean separation between application bootstrapping, persistence, and HTTP concerns

## API

Namespace:

```
/wp-json/mhm/v1
```

### List resources

```http
GET /wp-json/mhm/v1/resources?page=1&per_page=10
```

### Get one resource

```http
GET /wp-json/mhm/v1/resources/123
```

### Create a resource

Requires an authenticated WordPress user with the `edit_posts` capability.

```http
POST /wp-json/mhm/v1/resources
Content-Type: application/json

{
  "title": "REST API architecture",
  "content": "A sample resource created through the custom endpoint."
}
```

## Structure

```
wp-rest-resource-kit/
├── wp-rest-resource-kit.php
├── src/
│   ├── Plugin.php
│   ├── Repository/
│   │   └── ResourceRepository.php
│   └── Rest/
│       └── ResourceController.php
├── composer.json
├── phpcs.xml.dist
└── LICENSE
```

## Installation

1. Copy the repository into `wp-content/plugins/wp-rest-resource-kit`.
2. Activate **WP REST Resource Kit** from the WordPress admin.
3. Create resources from the WordPress dashboard or the custom REST endpoint.
4. Query published resources through `/wp-json/mhm/v1/resources`.

For local code-quality tooling:

```bash
composer install
composer lint
```

## Security choices

The public GET endpoints expose only published resources. Creating resources requires `edit_posts`, and user input is sanitized before persistence. The plugin uses WordPress REST permission callbacks instead of implementing custom authentication.

## Why I built this

My recent work is centered on custom PHP / WordPress product development, including dedicated data models, REST APIs, dashboards, QR workflows, payments, access controls, and integrations. This repository provides a small reviewable example of that backend-oriented approach without publishing proprietary project code.

## Links

- Portfolio: https://mhmoradi93.github.io/
- LinkedIn: https://www.linkedin.com/in/mohammadhosseinmoradi/
- GitHub: https://github.com/mhmoradi93
