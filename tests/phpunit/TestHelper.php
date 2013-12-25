<?php

namespace hoo\Test;

/**
 * Helper class for tests
 *
 * @licence GNU GPL v2+
 * @author Marius Hoch < hoo@online.de >
 */

class TestHelper {
	/**
	 * Whether this is labs
	 *
	 * @return bool
	 */
	public static function isLabs() {
		if ( gethostbyname( 'commsonwiki.labsdb' ) !== 'commsonwiki.labsdb' ) {
			return true;
		}

		return false;
	}
}
