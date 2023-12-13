<?php

namespace hoo\Database;

/**
 * Class for looking up and verifying labs database names.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @licence GNU GPL v2+
 * @author Marius Hoch < hoo@online.de >
 */

class DatabaseNameLookup {

	/**
	 * Try to find the database server IP for a given database name
	 *
	 * @param string $name Name of the database
	 *
	 * @return string|null
	 */
	public function lookup( $name ) {
		$name = str_replace( '_p', '', $name ) . '.analytics.db.svc.wikimedia.cloud';

		$host = gethostbyname( $name );

		// gethostbyname returns what it has been given in case it couldn't resolve the name
		if ( $host === $name ) {
			return null;
		}

		return $host;
	}
}
