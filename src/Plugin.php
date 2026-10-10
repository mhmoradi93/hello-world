<?php

declare(strict_types=1);

namespace MHM\ResourceApi;

use MHM\ResourceApi\Repository\ResourceRepository;
use MHM\ResourceApi\Rest\ResourceController;

final class Plugin
{
    public const POST_TYPE = 'mhm_resource';

    public function boot(): void
    {
        add_action('init', [$this, 'registerPostType']);
        add_action('rest_api_init', [$this, 'registerRestRoutes']);
    }

    public function registerPostType(): void
    {
        register_post_type(
            self::POST_TYPE,
            [
                'labels' => [
                    'name'          => __('Resources', 'wp-rest-resource-kit'),
                    'singular_name' => __('Resource', 'wp-rest-resource-kit'),
                    'add_new_item'  => __('Add New Resource', 'wp-rest-resource-kit'),
                    'edit_item'     => __('Edit Resource', 'wp-rest-resource-kit'),
                ],
                'public'       => false,
                'show_ui'      => true,
                'show_in_menu' => true,
                'show_in_rest' => false,
                'supports'     => ['title', 'editor'],
                'map_meta_cap' => true,
            ]
        );
    }

    public function registerRestRoutes(): void
    {
        $repository = new ResourceRepository(self::POST_TYPE);
        $controller = new ResourceController($repository);

        $controller->registerRoutes();
    }
}
