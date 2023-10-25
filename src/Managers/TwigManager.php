<?php
/**
 * Bootstraps Twig Extensions and Functions.
 *
 * @package Studiometa
 */

namespace Studiometa\Managers;

use Djboris88\Twig\Extension\CommentedIncludeExtension;
use Studiometa\Ui\Extension;
use Studiometa\WPToolkit\Managers\ManagerInterface;
use Twig\Environment;
use Twig\Extension\StringLoaderExtension;
use Twig\Loader\FilesystemLoader;

/** Class */
class TwigManager implements ManagerInterface {
	/**
	 * {@inheritdoc}
	 */
	public function run() {
		add_filter( 'timber/twig', array( $this, 'add_studiometa_ui' ) );
		add_filter( 'timber/twig', array( $this, 'add_template_from_string' ) );
		add_filter( 'timber/twig', array( $this, 'add_template_include_comments' ) );
		add_filter( 'timber/output', array( $this, 'add_template_render_comments' ), 10, 3 );
		add_filter( 'timber/loader/loader', array( $this, 'add_svg_path' ), 10, 1 );
	}

	/**
	 * Add Studio Meta's UI extension.
	 *
	 * @link https://ui.studiometa.dev
	 * @param Environment $twig The Twig environment.
	 * @return Environment
	 */
	public function add_studiometa_ui( Environment $twig ): Environment {
		/**
		 * The Twig loader
		 *
		 * @var FilesystemLoader|null $loader.
		 */
		$loader = $twig->getLoader();
		$twig->addExtension(
			new Extension(
				$loader,
				get_template_directory() . '/templates',
				get_template_directory() . '/static/svg',
			)
		);

		return $twig;
	}

	/**
	 * Adds template_from_string to Twig.
	 *
	 * @link https://twig.symfony.com/doc/2.x/functions/template_from_string.html
	 * @param Environment $twig The Twig environment.
	 * @return Environment
	 */
	public function add_template_from_string( Environment $twig ): Environment {
		$twig->addExtension( new StringLoaderExtension() );
		return $twig;
	}

	/**
	 * Adds template_from_string to Twig.
	 *
	 * @link https://github.com/djboris88/twig-commented-include
	 * @param Environment $twig The Twig environment.
	 * @return Environment
	 */
	public function add_template_include_comments( Environment $twig ): Environment {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return $twig;
		}

		$twig->addExtension( new CommentedIncludeExtension() );
		return $twig;
	}

	/**
	 * Add debug comments to Timber::render
	 *
	 * @param string  $output HTML.
	 * @param mixed[] $data   Data.
	 * @param string  $file   Name.
	 * @return string
	 */
	public function add_template_render_comments( string $output, array $data, string $file ): string {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return $output;
		}

		return "\n<!-- Begin output of '" . $file . "' -->\n" . $output . "\n<!-- / End output of '" . $file . "' -->\n";
	}

	/**
	 * Add an alias for the SVG folder.
	 *
	 * @example {{ source('@svg/icon.svg') }}
	 *
	 * @param FilesystemLoader $fs The loader to extend.
	 * @return FilesystemLoader
	 */
	public function add_svg_path( FilesystemLoader $fs ): FilesystemLoader {
		$fs->addPath( get_template_directory() . '/static/svg', 'svg' );
		return $fs;
	}
}
