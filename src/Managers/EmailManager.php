<?php
/**
 * Configure emails.
 *
 * @package Studiometa\WPToolkit
 */

namespace Studiometa\WPToolkit\Managers;

use PHPMailer\PHPMailer\PHPMailer;
use function Studiometa\WPToolkit\env;

/** Class **/
class EmailManager implements ManagerInterface {
	/**
	 * {@inheritdoc}
	 */
	public function run() {
		if ( 'smtp' === env( 'MAIL_MAILER' ) ) {
			add_action( 'phpmailer_init', array( $this, 'configure_smtp' ), 10, 1 );
		}
	}

	/**
	 * Configure SMTP server to send mails.
	 *
	 * @param  PHPMailer $mailer The PHPMailer instance.
	 * @return void
	 */
	public function configure_smtp( PHPMailer $mailer ): void {
		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$mailer->Host       = env( 'MAIL_HOST' );
		$mailer->Port       = (int) env( 'MAIL_PORT' );
		$mailer->Username   = env( 'MAIL_USERNAME' );
		$mailer->Password   = env( 'MAIL_PASSWORD' );
		$mailer->SMTPAuth   = 'true' === env( 'MAIL_AUTH' );
		$mailer->SMTPSecure = env( 'MAIL_ENCRYPTION' );
		$mailer->IsSMTP();
		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	}
}
