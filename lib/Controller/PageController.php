<?php
namespace OCA\FreeContent\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Controller;
use OCP\IURLGenerator;
use OCP\IUserSession;

use OCA\FreeContent\Manager;

class PageController extends Controller {

	/** @var Manager */
	protected $manager;

	/** @var IURLGenerator */
	protected $urlGenerator;

	/** IUserSession */
	protected $userSession;

	public function __construct($AppName, IRequest $request,
											Manager $manager,
											IURLGenerator $urlGenerator
											IUserSession $userSession){
		parent::__construct($AppName, $request);

		$this->manager = $manager;
		$this->urlGenerator = $urlGenerator;
		$this->userSession = $userSession;
	}

	/**
	 * CAUTION: the @Stuff turns off security checks; for this page no admin is
	 *          required and no CSRF check. If you don't know what CSRF is, read
	 *          it up in the docs or you might create a security hole. This is
	 *          basically the only required method to add this exemption, don't
	 *          add it to any other method if you don't exactly know what it does
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index() {

		try {
			$entries = $this->manager->getEntries(10, 0, $this->userSession->getUser() === null);
		} catch (\InvalidArgumentException $e) {
			return new RedirectResponse($this->urlGenerator->getBaseUrl());
		}

		return new TemplateResponse('free-content', 'index', [
			'urlGenerator' => $this->urlGenerator,
			'entries' => $entries,
		]);  // templates/show.php
	}

	/**
	 * CAUTION: the @Stuff turns off security checks; for this page no admin is
	 *          required and no CSRF check. If you don't know what CSRF is, read
	 *          it up in the docs or you might create a security hole. This is
	 *          basically the only required method to add this exemption, don't
	 *          add it to any other method if you don't exactly know what it does
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @PublicPage
	 *
	 * @param string $id
	 */
	public function show($id) {

		try {
			$entry = $this->manager->getEntry($id, $this->userSession->getUser() === null);
		} catch (\InvalidArgumentException $e) {
			return new RedirectResponse($this->urlGenerator->getBaseUrl());
		}

		return new TemplateResponse('free-content', 'show', [
			'content' => $entry['content'],
		]); 
	}

}
