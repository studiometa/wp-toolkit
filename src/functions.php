<?php

namespace Studiometa\WPToolkit;

use Studiometa\WPToolkit\Helpers\RequestHelper;

/**
 * Get the Request instance.
 * @return Symfony\Component\HttpFoundation\Request
 */
function request() {
	return RequestHelper::get();
}
