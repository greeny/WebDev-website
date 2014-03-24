<?php
/**
 * @author Tomáš Blatný
 */

namespace WebDev\PublicModule;

use Nette\Utils\Paginator;
use WebDev\Controls\Form;
use WebDev\Model\Comment;
use WebDev\Model\Post;

class PostPresenter extends BasePublicPresenter {

	/** @var Post */
	protected $post;

	public function renderDetail($id)
	{
		if(!$this->user->isAllowed('post', 'view')) {
			$this->flashError('Přístup zamítnut.');
			$this->redirect('Dashboard:default');
		}
		if(!$this->template->post = $this->postRepository->find($id)) {
			$this->flashError('Příspěvek nenalezen.');
			$this->redirect('list');
		}
	}

	public function actionCompose()
	{
		if(!$this->user->isAllowed('post', 'compose')) {
			$this->flashError('Přístup zamítnut.');
			$this->redirect('list');
		}
	}

	public function actionEdit($id)
	{
		if(!$this->post = $this->postRepository->find($id)) {
			$this->flashError('Příspěvek nenalezen.');
			$this->redirect('list');
		}

		$foreign = ($this->user->id !== $this->post->user->id);

		if(!$this->user->isAllowed('post', $foreign ? 'f-edit' : 'edit')) {
			$this->flashError('Přístup zamítnut.');
			$this->redirect('list');
		}
	}

	public function renderEdit()
	{
		$this->template->post = $this->post;
	}

	public function handleDelete($id)
	{
		if(!$this->post = $this->postRepository->find($id)) {
			$this->flashError('Příspěvek nenalezen.');
			$this->redirect('list');
		}

		$foreign = ($this->user->id !== $this->post->user->id);

		if(!$this->user->isAllowed('post', $foreign ? 'f-delete' : 'delete')) {
			$this->flashError('Přístup zamítnut.');
			$this->redirect('list');
		}

		$this->postRepository->delete($this->post);
		$this->flashSuccess('Příspěvek byl smazán.');
		$this->redirect('list');
	}

	public function renderList($page = 1)
	{
		$this->template->paginator = $paginator = new Paginator();
		$paginator->itemCount = $this->postRepository->countAll();
		$paginator->itemsPerPage = 20;
		$paginator->page = $page;
		if($page !== $paginator->page) {
			$this->redirect('this', array('page' => $paginator->page));
		}
		$this->template->posts = $this->postRepository->findOrderedByPage($paginator, 'time DESC');
	}

	protected function createPostForm()
	{
		$form = $this->createForm();
		$form->addText('title', 'Titulek')
			->setRequired('Prosím, zadej titulek.');

		$form->addTextArea('text', 'Text')
			->setRequired('Prosím, zadej text.');

		return $form;
	}

	public function createComponentComposePostForm()
	{
		$form = $this->createPostForm();
		$form->addPrimarySubmit('compose', 'Vytvořit');
		$form->onSuccess[] = $this->composePostFormSuccess;
		return $form;
	}

	public function composePostFormSuccess(Form $form)
	{
		$post = Post::from($form->getValues());
		$post->time = Time();
		$post->user = $this->userRepository->find($this->user->id);
		$this->postRepository->persist($post);
		$this->flashSuccess('Příspěvek byl přidán.');
		$this->redirect('detail', array('id' => $post->id));
	}

	public function createComponentEditPostForm()
	{
		$form = $this->createPostForm();
		$form->addPrimarySubmit('edit', 'Upravit');
		$form->setDefaults($this->post->getData());
		$form->onSuccess[] = $this->editPostFormSuccess;
		return $form;
	}

	public function editPostFormSuccess(Form $form)
	{
		$post = $this->postRepository->find($this->params['id']);
		$post->update($form->getValues());
		$this->postRepository->persist($post);
		$this->flashSuccess('Příspěvek byl upraven.');
		$this->redirect('detail', array('id' => $post->id));
	}

	public function createComponentAddCommentForm()
	{
		$form = $this->createForm();
        $form->addTextArea('text', 'Komentář')
                ->setRequired('Prosím, zadej text komentáře.');
		$form->addPrimarySubmit('comment', 'Okomentovat');
		$form->onSuccess[] = $this->addCommentFormSuccess;
		return $form;
	}

	public function addCommentFormSuccess(Form $form)
	{
		if($this->user->isLoggedIn()) {
			$comment = Comment::from($form->getValues());
			$comment->time = Time();
			$comment->user = $this->userRepository->find($this->user->id);
			$this->commentRepository->persist($comment);
			$post = $this->postRepository->find($this->params['id']);
			$post->addToComments($comment);
			$this->postRepository->persist($post);
			$this->flashSuccess('Komentář byl přidán.');
			$this->refresh();
		} else {
			$this->flashError('Musíš být přihlášený.');
			$this->refresh();
		}
	}
}
