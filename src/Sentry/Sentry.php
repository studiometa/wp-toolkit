<?php

namespace Studiometa\WPToolkit\Sentry;

use Studiometa\WPToolkit\Sentry\Config;
use function Sentry\init as init_sentry;
use function Studiometa\WPToolkit\{env,request};
use function WeCodeMore\earlyAddAction as early_add_action;

class Sentry {
    private static Config $config;

    static public function configureWithDefaults( string $root_dir ) {
        $release = '';

        $file_path = $root_dir . '/package.json';
        if (file_exists($file_path)) {
            $package = json_decode( file_get_contents( $file_path ) );
            $release = $package->name . '@' . $package->version;
        }

        $sample_rate = (float) (env('SENTRY_SAMPLE_RATE') ?: 0);
        $traces_sample_rate = (float) (env('SENTRY_TRACES_SAMPLE_RATE') ?: $sample_rate);
        $profiles_sample_rate = (float) (env('SENTRY_PROFILES_SAMPLE_RATE') ?: $sample_rate);

        return self::configure(
            new Config(
                dsn: env('SENTRY_DSN'),
                js_loader_script: env('SENTRY_JS_LOADER_SCRIPT'),
                environment: env('SENTRY_ENV') ?: env('APP_ENV'),
                release: $release,
                traces_sample_rate: $traces_sample_rate,
                profiles_sample_rate: $profiles_sample_rate,
            ),
        );
    }

    static private function configure( Config $config ) {
        self::$config = $config;

        init_sentry($config->toArray());

        early_add_action('init', function() use ($config) {
            wp_enqueue_script('sentry-loader-script', $config->js_loader_script, []);
            $js_config = $config->getJsConfig();
            $inline_script = "window.sentryOnLoad = () => { Sentry.init({$js_config}) }";
            wp_add_inline_script('sentry-loader-script', $inline_script, 'before');
        });

        if ( $config->traces_sample_rate > 0 ) {
            // Setup Sentry performance + profiling
            // Setup context for the full transaction
            $transactionContext = new \Sentry\Tracing\TransactionContext();
            $transactionContext->setName(request()->server->get('REQUEST_URI', 'wp-cli'));
            $transactionContext->setOp('http.server');

            // Start the transaction
            $transaction = \Sentry\startTransaction($transactionContext);

            // Set the current transaction as the current span so we can retrieve it later
            \Sentry\SentrySdk::getCurrentHub()->setSpan($transaction);

            // Setup the context for the expensive operation span
            $spanContext = new \Sentry\Tracing\SpanContext();
            $spanContext->setOp('wordpress');

            // Start the span
            $span = $transaction->startChild($spanContext);

            // Set the current span to the span we just started
            \Sentry\SentrySdk::getCurrentHub()->setSpan($span);

            register_shutdown_function(function () use ($span, $transaction) {
                // Finish the span
                $span->finish();
                // Set the current span back to the transaction since we just finished the previous span
                \Sentry\SentrySdk::getCurrentHub()->setSpan($transaction);
                // Finish the transaction, this submits the transaction and it's span to Sentry
                $transaction->finish();
            });
        }
    }
}
