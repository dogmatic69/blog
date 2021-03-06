<?php
    /**
     * Blog Comments admin index
     *
     * this is the page for admins to view all the posts on the site.
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
     * @subpackage    blog.views.posts.admin_index
     * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
     */
    echo $this->Form->create('Post', array('url' => array('controller' => 'posts', 'action' => 'mass', 'admin' => 'true')));
        $massActions = $this->Blog->massActionButtons(
            array(
                'add',
                'edit',
                'preview',
                'toggle',
                'copy',
                'move',
                'delete'
            )
        );
	echo $this->Infinitas->adminIndexHead($filterOptions, $massActions);
?>
<div class="table">
    <table class="listing" cellpadding="0" cellspacing="0">
        <?php
            echo $this->Blog->adminTableHeader(
                array(
                    $this->Form->checkbox('all') => array(
                        'class' => 'first',
                        'style' => 'width:25px;'
                    ),
                    $this->Paginator->sort('title'),
                    $paginator->sort(__('Category', true), 'Category.name') => array(
                        'style' => 'width:130px;'
                    ),
                    __('Tags', true),
                    $paginator->sort('Comments','comment_count') => array(
                        'style' => 'width:50px;'
                    ),
                    $paginator->sort('views') => array(
                        'style' => 'width:30px;'
                    ),
                    __('Status', true) => array(
                        'class' => 'actions'
                    )
                )
            );

            foreach($posts as $post){
                ?>
                    <tr class="<?php echo $this->Blog->rowClass(); ?>">
                        <td><?php echo $this->Form->checkbox( $post['Post']['id'] ); ?>&nbsp;</td>
                        <td title="<?php echo $post['Post']['slug']; ?>">
                            <?php echo $this->Html->link( $post['Post']['title'], array( 'action' => 'edit', $post['Post']['id'] ) ); ?>
                        </td>
                        <td>
							<?php
								if(isset($post['Category']['title'])) {
									echo $this->Html->link(
										$post['Category']['title'],
										array(
											'plugin' => 'management',
											'controller' => 'categories',
											'action' => 'edit',
											$post['Category']['id']
										)
									);
								}
                        	?>
						</td>
                        <td><?php echo implode(', ', Set::extract('/Tag/name', $post)); ?>&nbsp;</td>
                        <td><?php echo $post['Post']['comment_count']; ?>&nbsp;</td>
                        <td><?php echo $post['Post']['views']; ?>&nbsp;</td>
                        <td>
                            <?php
                                echo $this->Infinitas->status($post['Post']['active'], $post['Post']['id']),
                                    $this->Locked->display($post);
                            ?>
                        </td>
                    </tr>
                <?php
            }
        ?>
    </table>
    <?php echo $this->Form->end(); ?>
</div>
<?php echo $this->element('pagination/admin/navigation'); ?>