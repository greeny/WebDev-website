<?php

namespace WebDev;

use Kdyby\BootstrapFormRenderer\BootstrapRenderer;
use Nette\Forms\Controls\SubmitButton;
use Nette\Security\AuthenticationException;
use WebDev\Templating\Helpers;
use WebDev\Controls\Form;
use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{
	/** @var \WebDev\Model\UserRepository @inject */
	public $userRepository;

	public function createForm()
	{
		$form = new Form();
		$form->setRenderer(new BootstrapRenderer());
		return $form;
	}

	public function beforeRender()
	{
		parent::beforeRender();
		Helpers::prepareTemplate($this->template);
	}

	public function getParamByName($name)
	{
		return $this->params[$name];
	}

	public function handleLogout()
	{
		if($this->user->isLoggedIn()) {
			$this->user->logout(TRUE);
			$this->flashSuccess('Byl jsi odhlášen.');
		}
		$this->redirect(":Public:Dashboard:default");
	}

	public function flashError($message)
	{
		return $this->flashMessage($message, 'danger');
	}

	public function flashSuccess($message)
	{
		return $this->flashMessage($message, 'success');
	}

	public function refresh() {
		$this->redirect('this');
	}

	public function createComponentRegisterLoginForm()
	{
		$form = new Form();
		$form->addText('nick', '')
			->setAttribute('placeholder', 'Přezdívka')
			->setRequired('Prosím zadej přezdívku.');
		$form->addPassword('password', '')
			->setAttribute('placeholder', 'Heslo')
			->setRequired('Prosím zadej heslo.');
		$form->addSubmit('login', 'Přihlásit se')
			->onClick[] = $this->loginFormSuccess;
		$form->addSubmit('register', 'Zaregistrovat se')
			->onClick[] = $this->registerFormSuccess;

		return $form;
	}

	public function loginFormSuccess(SubmitButton $button, $register = FALSE)
	{
		$form = $button->getForm();
		$v = $form->getValues();
		try {
			$this->user->login($v->nick, $v->password);
			$this->flashSuccess($register ? 'Registrace a přihlášení proběhly úspěšně': 'Přihlášení proběhlo úspěšně.');
			$this->user->setExpiration('+14 days', FALSE, TRUE);
			$this->refresh();
		} catch(AuthenticationException $e) {
			$this->flashError($e->getMessage());
			$this->refresh();
		}
	}

	public function registerFormSuccess(SubmitButton $button)
	{
		$v = $button->getForm()->getValues();
		try {
			$this->userRepository->register($v);
			$this->loginFormSuccess($button, TRUE);
		} catch(AuthenticationException $e) {
			$this->flashError($e->getMessage());
		}
	}
}
