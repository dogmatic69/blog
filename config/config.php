<?php
	/* 
	 * Short Description / title.
	 * 
	 * Overview of what the file does. About a paragraph or two
	 * 
	 * Copyright (c) 2010 Carl Sutton ( dogmatic69 )
	 * 
	 * @filesource
	 * @copyright Copyright (c) 2010 Carl Sutton ( dogmatic69 )
	 * @link http://www.infinitas-cms.org
	 * @package {see_below}
	 * @subpackage {see_below}
	 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
	 * @since {check_current_milestone_in_lighthouse}
	 * 
	 * @author {your_name}
	 * 
	 * Licensed under The MIT License
	 * Redistributions of files must retain the above copyright notice.
	 */

	 $config['Blog'] = array(
		 'allow_comments' => true,
		 'allow_ratings' => true,
		 'depreciate' => '6 months', // the time before the post is marked as old
		 'preview' => 400, // the length of the text to show on index pages
		 'before' => array(
		 ),
		 'after' => array(
			 'view_count'
		 )
	 );