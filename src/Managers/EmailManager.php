<?php
/**
 * Configure emails.
 *
 * @package Studiometa\WPToolkit
 */

namespace Studiometa\WPToolkit\Managers;

use WP_Error;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use PHPMailer\PHPMailer\PHPMailer;
use function Studiometa\WPToolkit\env;

/** Class **/
class EmailManager implements ManagerInterface {
	/**
	 * Logger instance.
	 *
	 * @var LoggerInterface|null;
	 */
	private $logger;

	/**
	 * Class constructor.
	 *
	 * @param LoggerInterface|null $logger A logger instance to log mail actions.
	 */
	public function __construct( ?LoggerInterface $logger = null ) {
		if ( ! is_null( $logger ) ) {
			$this->logger = $logger;
		} elseif ( env( 'MAIL_LOG' ) ) {
			$this->logger = new Logger( 'email' );
			$this->logger->pushHandler( new StreamHandler( env( 'MAIL_LOG' ) ) );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function run() {
		if ( 'smtp' === env( 'MAIL_MAILER' ) ) {
			add_action( 'phpmailer_init', array( $this, 'configure_smtp' ) );
		}

		if ( $this->logger ) {
			add_action( 'wp_mail_succeeded', array( $this, 'log_success' ) );
			add_action( 'wp_mail_failed', array( $this, 'log_failure' ) );
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

	/**
	 * Log successfully sent mails.
	 *
	 * @param array{to: string[], subject: string, message: string, headers: string[], attachments: string[]} $mail_data An array containing the email recipient(s), subject, message, headers, and attachments.
	 *
	 * @return void
	 */
	public function log_success( array $mail_data ): void {
		if ( $this->logger ) {
			$this->logger->info( 'Mail sent', $mail_data );
		}
	}

	/**
	 * Log failure happening when sending mails.
	 *
	 * @param  WP_Error $error The error sent.
	 *
	 * @return void
	 */
	public function log_failure( WP_Error $error ): void {
		if ( $this->logger ) {
			/**
			 * Mail data.
			 *
			 * @var array
			 */
			$mail_data = $error->get_error_data();
			$this->logger->error( $error->get_error_message(), $mail_data );
		}
	}
}
