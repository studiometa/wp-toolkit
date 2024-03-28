<?php
/**
 * Bootstraps Admin related functions.
 *
 * @package Studiometa
 */

namespace Studiometa\WPToolkit\Managers;

use WP_Query;
use Timber\Timber;
use Twig\Environment;
use Twig\TwigFunction;
use Studiometa\WPToolkit\Managers\ManagerInterface;
use function Studiometa\WPToolkit\request;

/** Class */
class FacetsManager implements ManagerInterface
{
    /**
     * The facets filters data, from a GET or a POST request.
     *
     * @var null|array
     */
    private ?array $facets;

    /**
     * Constructor.
     *
     * @param int $pre_get_posts_priority Set the priority parameter for the `pre_get_posts` action.
     */
    public function __construct(
        public int $pre_get_posts_priority = 10,
    ) {
        $facets       = request()->get('facets');
        $this->facets = is_array($facets) ? $facets : null;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        add_filter('timber/twig', array( $this, 'add_twig_helpers' ));

        if (is_array($this->facets)) {
            add_action('pre_get_posts', array( $this, 'add_facets_to_query' ), $this->pre_get_posts_priority);
        }
    }

    /**
     * Add facets defined as get or post parameters from the request to the main WP Query.
     * For the list of available parameters, see the documentation for `WP_Query` linked below.
     *
     * @see https://developer.wordpress.org/reference/classes/wp_query/#parameters
     *
     * @param WP_Query $query The query for the current request.
     * @return void
     */
    public function add_facets_to_query(WP_Query &$query): void
    {
        if (!is_array($this->facets)) {
            return;
        }

        foreach ($this->facets as $query_var => $value) {
            $query->query_vars[ $query_var ] = $value;
        }
    }

    /**
     * Add Twig helpers to get parameters values.
     *
     * @param Environment $twig The Twig environment.
     * @return Environment
     */
    public function add_twig_helpers(Environment $twig): Environment
    {
        $twig->addFunction(
            new TwigFunction(
                'facets_get',
                fn(string $parameter) => $this->facets[ $parameter ] ?? null,
            )
        );

        return $twig;
    }
}
