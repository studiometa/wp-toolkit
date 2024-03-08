<?php

namespace Studiometa\WPToolkitTest;

use WP_UnitTestCase;
use Studiometa\WPToolkit\Managers\EmailManager;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * EmailManagerTest test case.
 */
class EmailManagerTest extends WP_UnitTestCase
{

    /**
     * Test configure PHPMailer with SMTP server.
     *
     * @return void
     */
    public function test_smtp_configuration()
    {
        $_ENV['MAIL_MAILER']     = 'smtp';
        $_ENV['MAIL_HOST']       = '127.0.0.1';
        $_ENV['MAIL_PORT']       = '1025';
        $_ENV['MAIL_USERNAME']   = 'test';
        $_ENV['MAIL_PASSWORD']   = 'test';
        $_ENV['MAIL_AUTH']       = 'true';
        $_ENV['MAIL_ENCRYPTION'] = 'tls';

        $manager = new EmailManager();
        $manager->run();

        // Trigger phpmailer configuration action.
        global $phpmailer;
        assert($phpmailer instanceof PHPMailer);
        do_action_ref_array('phpmailer_init', array( &$phpmailer ));

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
        $this->assertSame($phpmailer->Mailer, 'smtp');
        $this->assertSame($phpmailer->Host, '127.0.0.1');
        $this->assertSame($phpmailer->Port, 1025);
        $this->assertSame($phpmailer->Username, 'test');
        $this->assertSame($phpmailer->Password, 'test');
        $this->assertSame($phpmailer->SMTPAuth, true);
        $this->assertSame($phpmailer->SMTPSecure, 'tls');
		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
    }
}
