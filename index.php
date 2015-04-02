<?php
/**
 * @author Julien Klepatch <julien@julienklepatch.com>
 * @license GPL version 2.0
 *
 * Released under the GPL version 2.0, http://www.gnu.org/licenses/gpl-2.0.html
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License version 2.0 for more details.
 */

/**
 * @since 1.0
 *
 * Receive all requests and control execution flow
 *
 * - Load bootstrap.php to require all necessary files
 * - Call the right controller according to the request type
 * - (to implement) : Check if user has the right to perform action
 */

//Require all necessary files
require_once 'bootstrap.php';

//Check user is authorized to proceed (to do)
// if( ! is_user_login() {
// 	redirect( 'login.php' );
// 	exit;
// }

$request_type = get_request_type();
// if( ! current_user_can( $request_type ) ) {
// 	die();
// }

//Load header controller (shows on all page) then the specific controller related to request_type
require_once  LOCAL_APP_DIR . '\header-controller.php';
if( 'read' === $request_type ) {
	require_once  LOCAL_APP_DIR . '\read-controller.php';

} elseif( 'search' === $request_type ) {
	require_once  LOCAL_APP_DIR . '\search-controller.php';

} elseif( 'update' === $request_type ) {
 	require_once  LOCAL_APP_DIR . '\update-controller.php';

} else {
	require_once  LOCAL_APP_DIR . '\404-controller.php';
}


