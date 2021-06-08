<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * @file
 */

namespace MediaWiki\Extension\GmScreen;

use MediaWiki\MediaWikiServices;

//class Hooks implements \MediaWiki\Hook\BeforePageDisplayHook {
class Hooks {
	/**
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/BeforePageDisplay
	 * @param \OutputPage $out
	 * @param \Skin $skin
	 */
	public static function onBeforePageDisplay( $out, $skin ) : void {
		if (!$out->isArticle() || !$out->isRevisionCurrent()) {
			return;
		}

		$gmscreen = new GmScreen($out);
		$gmscreen->embedGmPage($out, $skin);
	}

	/**
	 * Hook handler for hook 'SkinTemplateNavigation'
	 * @param \SkinTemplate $skinTemplate
	 * @param array &$navigation
	 */
	public static function onSkinTemplateNavigation__Universal( $skinTemplate, &$navigation ) {
		$gmscreen = new GmScreen($skinTemplate);
		$gmscreen->addTab($skinTemplate, $navigation);
	}

	public static function onChangesListSpecialPageQuery( $name, &$tables, &$fields,
		&$conds, &$query_options, &$join_conds, $opts
	) {
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$gm_ns = $config->get('GmScreenNamespace');
		$conds[] = "rc_namespace != $gm_ns";
	}
}
