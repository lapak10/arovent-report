<?php
require_once( 'dist/library/class-session.php' );
ND_Session :: init();
ND_Session :: destroy();
die('Logged out successfully!');