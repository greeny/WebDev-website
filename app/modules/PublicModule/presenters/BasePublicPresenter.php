<?php
/**
 * @author Tomáš Blatný
 */

namespace WebDev\PublicModule;

use WebDev\BasePresenter;

class BasePublicPresenter extends BasePresenter {

	/** @var \WebDev\Model\PostRepository @inject */
	public $postRepository;

	/** @var \WebDev\Model\CommentRepository @inject */
	public $commentRepository;
}
 