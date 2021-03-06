<?php
	/**
	 * Blog Posts Controller class file.
	 *
	 * This is the main controller for all the blog posts.  It extends
	 * {@see BlogAppController} for some functionality.
	 *
	 * Copyright (c) 2009 Carl Sutton ( dogmatic69 )
	 *
	 * Licensed under The MIT License
	 * Redistributions of files must retain the above copyright notice.
	 *
	 * @filesource
	 * @copyright Copyright (c) 2009 Carl Sutton ( dogmatic69 )
	 * @link http://infinitas-cms.org
	 * @package blog
	 * @subpackage blog.controllers.posts
	 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
	 * @since 0.5a
	 */

	class PostsController extends BlogAppController {
		/**
		 * Class name.
		 *
		 * @access public
		 * @var string
		 */
		public $name = 'Posts';

		/**
		 * Helpers.
		 *
		 * @access public
		 * @var array
		 */
		public $helpers = array(
			'Filter.Filter'
		);

		/**
		 * PostsController::beforeFilter()
		 *
		 * empty
		 */
		public function beforeFilter() {
			parent::beforeFilter();
		}

		/**
		 * Index for users
		 *
		 * @param string $tag used to find posts with a tag
		 * @param string $year used to find posts in a cetain year
		 * @param string $month used to find posts in a year and month needs year
		 * @return
		 */
		public function index() {
			$titleForLayout = $year = $month = $slug = null;

			if(isset($this->params['year'])){
				$year = $this->params['year'];
				$titleForLayout = sprintf(__('Posts for the year %s', true), $year);
				if(isset($this->params['pass'][0])){
					$month = substr((int)$this->params['pass'][0], 0, 2);
					$titleForLayout = sprintf(__('Posts in %s, %s', true), __(date('F', mktime(0, 0, 0, $month)), true), $year);
				}
			}
			
			else if(isset($this->params['tag'])){
				$tag = $this->params['tag'];
				if(empty($titleForLayout)){
					$titleForLayout = __('Posts', true);
				}
				$titleForLayout = sprintf(__('%s related to %s', true), $titleForLayout, $tag);
			}

			$this->set('title_for_layout', $titleForLayout);
			
			$post_ids = array();
			if (isset($tag)) {
				$tag_id = ClassRegistry::init('Tags.Tag')->find(
					'list',
					array(
						'fields' => array(
							'Tag.id', 'Tag.id'
						),
						'conditions' => array(
							'Tag.name' => $tag
						)
					)
				);

				$post_ids = $this->Post->Tagged->find(
					'list',
					array(
						'fields' => array(
							'Tagged.foreign_key', 'Tagged.foreign_key'
						),
						'conditions' => array(
							'Tagged.tag_id' => $tag_id
						)
					)
				);
			}

			$this->Post->virtualField['body'] = sprintf('LEFT(`Post`.`body`, %s)', Configure::read('Blog.preview') + 20);

			$paginate = array(
				'fields' => array(
					'Post.id',
					'Post.title',
					'Post.slug',
					'body',
					'Post.comment_count',
					'Post.views',
					'Post.created',
					'Post.parent_id',
					'Post.ordering',
					'Post.category_id',
				),
				'conditions' => array(
					'Post.active' => 1,
					'Post.id' . ((!empty($post_ids)) ? ' IN (' . implode(',', $post_ids) . ')' : ' > 0'),
					'Post.parent_id IS NULL',
					'Post.category_id' => $this->Post->Category->getActiveIds()
				),
				'contain' => array(
					'Category',
					'Tag',
					'ChildPost' => array(
						'Category'
					)
				)
			);



			$this->paginate = $this->Post->setPaginateDateOptions(
				$paginate,
				array(
					'year' => $year,
					'month' => $month
					//'model' => 'custom_model',
					//'created' => 'custom_created_field'
				)
			);

			$posts = $this->paginate('Post');
			$this->set(compact('posts'));

			if( $this->RequestHandler->isRss() ){
				//$this->render('index');
			}
		}

		/**
		 * User view
		 *
		 * @param string $slug the slug for the record
		 * @return na
		 */
		public function view() {
			if (!isset($this->params['slug'])) {
				$this->Session->setFlash( __('Post could not be found', true) );
				$this->redirect($this->referer());
			}

			$post = $this->Post->find(
				'first',
				array(
					'fields' => array(
						'Post.id',
						'Post.title',
						'Post.slug',
						'Post.body',
						'Post.active',
						'Post.views',
						'Post.comment_count',
						'Post.rating',
						'Post.rating_count',
						'Post.created',
						'Post.modified'
					),
					'conditions' => array(
						'or' => array(
							'Post.slug' => $this->params['slug']
						),
						'Post.active' => 1
					),
					'contain' => array(
						'Category',
						'ChildPost',
						'ParentPost',
						'Tag'
					)
				)
			);

			if (!empty($post['ParentPost']['id'])) {
				$post['ParentPost']['ChildPost'] = $this->Post->find(
					'all',
					array(
						'conditions' => array(
							'Post.parent_id' => $post['ParentPost']['id']
						),
						'fields' => array(
							'Post.id',
							'Post.title',
							'Post.slug',
						),
						'contain' => false
					)
				);
			}

			/**
			 * make sure there is something found and the post is active
			 */
			if (empty($post) || !$post['Post']['active']) {
				$this->Session->setFlash('No post was found', true);
				$this->redirect($this->referer());
			}

			$this->set(compact('post'));
			$this->set('title_for_layout', $post['Post']['slug']);
		}

		/**
		 * Admin Section.
		 *
		 * All the admin methods.
		 */
		/**
		 * Admin dashboard
		 *
		 * @return na
		 */
		public function admin_dashboard() {
			$feed = $this->Post->find(
				'feed',
				array(
					'setup' => array(
						'plugin' => 'Blog',
						'controller' => 'posts',
						'action' => 'view',
					),
					'fields' => array(
						'Post.id',
						'Post.title',
						'Post.intro',
						'Post.created'
					),
					'feed' => array(
						'Core.Comment' => array(
							'setup' => array(
								'plugin' => 'Comment',
								'controller' => 'comments',
								'action' => 'view',
							),
							'fields' => array(
								'Comment.id',
								'Comment.name',
								'Comment.comment',
								'Comment.created'
							)
						)
					),
					'order' => array(
						'created' => 'DESC'
					)
				)
			);

			$this->set('blogFeeds', $feed);

			$this->set('dashboardPostCount', $this->Post->getCounts());
			$this->set('dashboardPostLatest', $this->Post->getLatest());
			$this->set('dashboardCommentsCount', $this->Post->Comment->getCounts('Blog.Post'));
		}

		/**
		 * Admin index.
		 *
		 * Uses the {@see FilterComponent} component to filter results.
		 *
		 * @return na
		 */
		public function admin_index() {
			$this->paginate['Post'] = array(
				'contain' => array('Tag', 'Category')
			);
			$posts = $this->paginate(null, $this->Filter->filter);

			$filterOptions = $this->Filter->filterOptions;
			$filterOptions['fields'] = array(
				'title',
				'body',
				'category_id' => array(null => __('All', true)) + $this->Post->generateCategoryList(),
				'active' => Configure::read('CORE.active_options')
			);

			$this->set(compact('posts', 'filterOptions'));
		}

		/**
		 * Admin add.
		 *
		 * This does some trickery for creating tags from the textarea comma
		 * delimited. also makes sure there are no duplicates created.
		 *
		 * @return void
		 */
		public function admin_add() {
			parent::admin_add();

			$parents = $this->Post->getParentPosts();
			$this->set(compact('tags', 'parents'));
		}

		public function admin_edit($id = null) {
			parent::admin_edit($id);

			$parents = $this->Post->getParentPosts();
			$this->set(compact('parents'));
		}

		public function admin_view($slug = null) {
			if (!$slug) {
				$this->Session->setFlash('That post could not be found', true);
				$this->redirect($this->referer());
			}

			$post = ((int)$slug > 0)
			? $this->Post->read(null, $slug)
			: $this->Post->find(
				'first',
				array(
					'conditions' => array(
						'Post.slug' => $slug
					)
				)
			);

			$this->set(compact('post'));
			$this->render('view');
		}
	}