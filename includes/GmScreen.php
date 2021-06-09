<?php
namespace MediaWiki\Extension\GmScreen;

use MediaWiki\MediaWikiServices;
use ParserOptions;
use RawMessage;
use Title;
use WikiPage;

class GmScreen {
	private $config;
	private $gm_ns;
	private $gm_text;
	private $user;
	private $title;
	private $gm_title;
	private $permissionManager;

	public function __construct( $context ) {
		$config = $context->getConfig();
		$this->config = $config;
		$this->gm_ns = $config->get('GmScreenNamespace');
		$this->gm_text = htmlspecialchars($config->get('GmScreenText'));
		$this->user = $context->getUser();
		$this->title = $context->getTitle();
		$this->permissionManager = MediaWikiServices::getInstance()->getPermissionManager();
		if ($this->gm_ns) {
			$this->gm_title = Title::makeTitle($this->gm_ns, $this->title->mDbkeyform);
		}
	}

	public function isAuth() {
		return $this->gm_ns != 0 &&
		       $this->permissionManager->userCan('read', $this->user, $this->gm_title);
	}

	public function addTab($skinTemplate, &$navigation) {
		if (!$this->isAuth()) {
			return;
		}

		if ($this->title->inNamespaces(NS_MAIN, NS_TALK)) {
			$selected = false;
		} else if ($this->title->inNamespace($this->gm_ns)) {
			$subjectId = $this->gm_title->getNamespaceKey('');
			unset($navigation['namespaces'][$subjectId]);
			unset($navigation['namespaces'][$subjectId . "_talk"]);

			$title = Title::makeTitle(NS_MAIN, $this->title->mDbkeyform);
			$userCanRead = $this->permissionManager->quickUserCan( 'read', $this->user, $title );
			$navigation['namespaces'] = [
				'main' => $skinTemplate->tabAction($title, 'nstab-main', false, '', $userCanRead),
				'talk' => $skinTemplate->tabAction($title->getTalkPage(), ['nstab-talk', 'talk'], false, '', $userCanRead),
			] + $navigation['namespaces'];
			$navigation['namespaces']['main']['context'] = 'subject';

			$navigation['namespaces']['talk']['context'] = 'talk';

			$selected = true;
		} else {
			return;
		}

		$tab_action = $skinTemplate->tabAction($this->gm_title, '', $selected, '', true);
		$tab_action['text'] = $this->gm_text;
		$navigation['namespaces']['gm'] = $tab_action;
	}

	public function embedGmPage($out, $skin) {
		if (!$this->isAuth() ||
		    !$this->config->get('GmScreenEmbedPage') ||
		    !$this->title->inNamespace(NS_MAIN)) {
			return;
		}

		$id = $this->gm_title->getArticleId();
		if ($id <= 0) {
			return;
		}
		$wikiPage = WikiPage::newFromId($id);

		$out->addModuleStyles('ext.gmScreen');
		$out->addHTML('<div class="gmscreen-embed">');
		$out->addHTML('<h1><span class="mw-headline">' . $this->gm_text . '</span>');
		$out->addHTML('<span class="mw-editsection"><span class="mw-editsection-bracket">[</span>');
		$linkRenderer = MediaWikiServices::getInstance()->getLinkRenderer();
		$out->addHTML( $linkRenderer->makeLink(
                        $this->gm_title,
                        $out->msg('editlink')->text(),
                        [],
                        [ 'action' => 'edit' ]
                ));
		$out->addHTML('<span class="mw-editsection-bracket">]</span></span>');
		$out->addHTML("</h1>");
		$out->addHTML($wikiPage->getParserOutput($out->parserOptions(), null, true)->getText());
		$out->addHTML("</div>");
	}
}
