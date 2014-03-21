<?php

namespace WebDev\PublicModule;

class DashboardPresenter extends BasePublicPresenter
{
	public function renderDefault()
	{
		$this->template->posts = $this->postRepository->getLast();
	}
}
