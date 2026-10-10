<?php

declare(strict_types=1);

namespace MHM\ResourceApi\Rest;

use MHM\ResourceApi\Repository\ResourceRepository;

final class ResourceController
{
    private const NAMESPACE = 'mhm/v1';

    public function __construct(
        private readonly ResourceRepository $repository
    ) {
    }

    public function registerRoutes(): void
    {
        register_rest_route(
            self::NAMESPACE,
            '/resources',
            [
                [
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => [$this, 'index'],
                    'permission_callback' => '__return_true',
                    'args'                => [
                        'page' => [
                            'default'           => 1,
                            'sanitize_callback' => 'absint',
                            'validate_callback' => static fn ($value): bool => (int) $value >= 1,
                        ],
                        'per_page' => [
                            'default'           => 10,
                            'sanitize_callback' => 'absint',
                            'validate_callback' => static fn ($value): bool => (int) $value >= 1 && (int) $value <= 50,
                        ],
                    ],
                ],
                [
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'create'],
                    'permission_callback' => static fn (): bool => current_user_can('edit_posts'),
                    'args'                => [
                        'title' => [
                            'required'          => true,
                            'sanitize_callback' => 'sanitize_text_field',
                            'validate_callback' => static fn ($value): bool => is_string($value) && trim($value) !== '',
                        ],
                        'content' => [
                            'default'           => '',
                            'sanitize_callback' => 'wp_kses_post',
                        ],
                    ],
                ],
            ]
        );

        register_rest_route(
            self::NAMESPACE,
            '/resources/(?P<id>\d+)',
            [
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => [$this, 'show'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'id' => [
                        'sanitize_callback' => 'absint',
                    ],
                ],
            ]
        );
    }

    public function index(\WP_REST_Request $request): \WP_REST_Response
    {
        $result = $this->repository->findMany(
            (int) $request->get_param('page'),
            (int) $request->get_param('per_page')
        );

        return new \WP_REST_Response($result, 200);
    }

    public function show(\WP_REST_Request $request): \WP_REST_Response|\WP_Error
    {
        $resource = $this->repository->find((int) $request->get_param('id'));

        if ($resource === null) {
            return new \WP_Error(
                'mhm_resource_not_found',
                __('Resource not found.', 'wp-rest-resource-kit'),
                ['status' => 404]
            );
        }

        return new \WP_REST_Response($resource, 200);
    }

    public function create(\WP_REST_Request $request): \WP_REST_Response|\WP_Error
    {
        $resource = $this->repository->create(
            (string) $request->get_param('title'),
            (string) $request->get_param('content')
        );

        if (is_wp_error($resource)) {
            return $resource;
        }

        return new \WP_REST_Response($resource, 201);
    }
}
