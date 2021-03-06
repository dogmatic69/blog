<?php
    /**
     * Blog Comments view
     *
     * this is the page for users to view blog posts
     *
     * Copyright (c) 2009 Carl Sutton ( dogmatic69 )
     *
     * Licensed under The MIT License
     * Redistributions of files must retain the above copyright notice.
     *
     * @filesource
     * @copyright     Copyright (c) 2009 Carl Sutton ( dogmatic69 )
     * @link          http://infinitas-cms.org
     * @package       blog
     * @subpackage    blog.views.posts.view
     * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
     */

	$eventData = $this->Event->trigger('blogBeforeContentRender', array('_this' => $this, 'post' => $post));
	?><div class="beforeEvent"><?php
		foreach((array)$eventData['blogBeforeContentRender'] as $_plugin => $_data){
			echo '<div class="'.$_plugin.'">'.$_data.'</div>';
		}
		?></div>
		<div class="wrapper">
			<div class="introduction <?php echo $this->layout; ?>">
				<h2>
					<?php
						$eventData = $this->Event->trigger('blog.slugUrl', array('type' => 'posts', 'data' => $post));
						$urlArray = current($eventData['slugUrl']);
						echo $this->Html->link(
							$post['Post']['title'],
							$urlArray
						);
					?><small><?php echo $this->Time->niceShort($post['Post']['created']); ?></small>
				</h2>
				<div class="content <?php echo $this->layout; ?>">
					<p><?php echo $post['Post']['body']; ?></p>
				</div>
			</div>
			<?php
				echo $this->element(
					'modules/post_tag_cloud',
					array(
						'plugin' => 'blog',
						'tags' => $post['Tag'],
						'title' => 'Tags'
					)
				);

				echo $this->element(
					'modules/comment',
					array(
						'plugin' => 'comments',
						'content' => $post,
						'modelName' => 'Post',
						'foreign_id' => $post['Post']['id']
					)
				);
			?>
		</div>
		<div class="afterEvent">
			<?php
				$eventData = $this->Event->trigger('blogAfterContentRender', array('_this' => $this, 'post' => $post));
				foreach((array)$eventData['blogAfterContentRender'] as $_plugin => $_data){
					echo '<div class="'.$_plugin.'">'.$_data.'</div>';
				}
			?>
		</div>