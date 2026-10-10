<?php

declare(strict_types=1);

namespace MHM\ResourceApi\Repository;

final class ResourceRepository
{
    public function __construct(
        private readonly string $postType
    ) {
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, total: int, pages: int}
     */
    public function findMany(int $page = 1, int $perPage = 10): array
    {
        $query = new \WP_Query(
            [
                'post_type'      => $this->postType,
                'post_status'    => 'publish',
                'posts_per_page' => $perPage,
                'paged'          => $page,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ]
        );

        $items = array_map(
            fn (\WP_Post $post): array => $this->mapPost($post),
            $query->posts
        );

        return [
            'items' => $items,
            'total' => (int) $query->found_posts,
            'pages' => (int) $query->max_num_pages,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(int $id): ?array
    {
        $post = get_post($id);

        if (
            ! $post instanceof \WP_Post
            || $post->post_type !== $this->postType
            || $post->post_status !== 'publish'
        ) {
            return null;
        }

        return $this->mapPost($post);
    }

    /**
     * @return array<string, mixed>|\WP_Error
     */
    public function create(string $title, string $content): array|\WP_Error
    {
        $postId = wp_insert_post(
            [
                'post_type'    => $this->postType,
                'post_status'  => 'publish',
                'post_title'   => $title,
                'post_content' => $content,
            ],
            true
        );

        if (is_wp_error($postId)) {
            return $postId;
        }

        $post = get_post($postId);

        if (! $post instanceof \WP_Post) {
            return new \WP_Error(
                'mhm_resource_not_created',
                __('The resource was created but could not be loaded.', 'wp-rest-resource-kit'),
                ['status' => 500]
            );
        }

        return $this->mapPost($post);
    }

    /**
     * @return array<string, mixed>
     */
    private function mapPost(\WP_Post $post): array
    {
        return [
            'id'         => (int) $post->ID,
            'title'      => get_the_title($post),
            'content'    => apply_filters('the_content', $post->post_content),
            'created_at' => get_post_time(DATE_ATOM, true, $post),
            'updated_at' => get_post_modified_time(DATE_ATOM, true, $post),
        ];
    }
}
