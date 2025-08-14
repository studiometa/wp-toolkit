<?php
/**
 * Bootstraps Admin related functions.
 *
 * @package Studiometa
 */

namespace Studiometa\WPToolkit\Managers;

use WP_Query;
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

        $tax_queries = [];
        $regular_vars = [];

        foreach ($this->facets as $query_var => $value) {
            $taxonomy_data = $this->parse_taxonomy_query_var($query_var);
            
            if ($taxonomy_data) {
                $tax_queries[] = $this->build_tax_query($taxonomy_data, $value);
            } else {
                $regular_vars[$query_var] = $value;
            }
        }

        // Apply regular query vars
        foreach ($regular_vars as $query_var => $value) {
            $query->query_vars[$query_var] = $value;
        }

        // Apply taxonomy queries
        if (!empty($tax_queries)) {
            $existing_tax_query = $query->get('tax_query', []);
            if (!empty($existing_tax_query)) {
                $tax_queries[] = $existing_tax_query;
            }
            $query->set('tax_query', $tax_queries);
        }
    }

    /**
     * Parse a query variable to determine if it's a taxonomy query.
     *
     * @param string $query_var The query variable name.
     * @return array|null Array with taxonomy and operator data, or null if not a taxonomy query.
     */
    private function parse_taxonomy_query_var(string $query_var): ?array
    {
        $taxonomies = get_taxonomies(['public' => true]);
        
        // Direct taxonomy match
        if (array_key_exists($query_var, $taxonomies)) {
            return ['taxonomy' => $query_var, 'operator' => 'IN'];
        }

        // Check for suffixed taxonomy queries
        $suffixes = ['__in', '__not_in', '__and', '__exists'];
        foreach ($suffixes as $suffix) {
            if (str_ends_with($query_var, $suffix)) {
                $taxonomy = substr($query_var, 0, -strlen($suffix));
                if (array_key_exists($taxonomy, $taxonomies)) {
                    $operator = match ($suffix) {
                        '__in' => 'IN',
                        '__not_in' => 'NOT IN',
                        '__and' => 'AND',
                        '__exists' => 'EXISTS',
                    };
                    return ['taxonomy' => $taxonomy, 'operator' => $operator];
                }
            }
        }

        return null;
    }

    /**
     * Build a tax_query array for a given taxonomy and value.
     *
     * @param array $taxonomy_data Array with taxonomy and operator info.
     * @param string|array $value The query value.
     * @return array The tax_query array.
     */
    private function build_tax_query(array $taxonomy_data, string|array $value): array
    {
        $tax_query = [
            'taxonomy' => $taxonomy_data['taxonomy'],
            'operator' => $taxonomy_data['operator'],
        ];

        if ($taxonomy_data['operator'] === 'EXISTS') {
            // EXISTS queries don't need terms/field
            return $tax_query;
        }

        // Handle different value types
        if (is_array($value)) {
            $terms = $value;
        } else {
            $terms = array_map('trim', explode(',', $value));
        }

        // Determine if we're dealing with term IDs or slugs
        $first_term = reset($terms);
        if (is_numeric($first_term)) {
            $tax_query['field'] = 'term_id';
            $tax_query['terms'] = array_map('intval', $terms);
        } else {
            $tax_query['field'] = 'slug';
            $tax_query['terms'] = $terms;
        }

        return $tax_query;
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
