<?php
defined('BASEPATH') or exit('No direct script access allowed');

class mymenu
{
	protected $menus = [];
	protected $currentUrl;
	protected $foundActive = false;
	protected $foundOpen = false;

	public function __construct($menus)
	{
		$this->menus = $menus;
		$this->currentUrl = current_url();
		$this->foundActive = false;
		$this->foundOpen = false;
	}

	function getArrayMenu()
	{
		return $this->menus;
	}

	function printMenu($menus = [])
	{
		$menus = $menus ?: $this->getArrayMenu();
		$html = '';

		foreach ($menus as $item) {
			if (!isset($item['visible']) || $item['visible'] === true) {
				$href     = isset($item['href']) ? $item['href'] : '#';
				$hasChild = isset($item['child']) && is_array($item['child']) && !empty($item['child']);

				// Only leaf items (no children) can be marked active.
				// Parent items only get menu-open, never active.
				$isActive = $hasChild ? false : $this->_isItemActive($item, $href);

				// A parent gets menu-open when any descendant exactly matches the current URL.
				$hasActiveChild = $hasChild ? $this->_hasActiveChild($item['child']) : false;

				$liClass = '';
				$aClass  = '';

				if ($isActive && !$this->foundActive) {
					$aClass = 'active';
					$this->foundActive = true;
				}

				if ($hasActiveChild && !$this->foundOpen) {
					$liClass = 'menu-open';
					$this->foundOpen = true;
				}

				$iClass    = isset($item['icon_class']) ? $item['icon_class'] : '';
				$hrefClass = isset($item['class'])     ? $item['class']     : '';

				$html .= "<li class='nav-item {$liClass}'>";
				$html .= "  <a href='{$href}' class='nav-link {$aClass} {$hrefClass}'>";

				if (isset($item['iconImg'])) {
					$html .= img([
						'src'   => $item['iconImg'],
						'class' => 'nav-icon align-middle',
						'style' => 'width: 22px; height: 22px; margin-right: 5px;',
					]);
				} elseif (isset($item['icon'])) {
					$html .= "<i class='nav-icon align-middle {$item['icon']} {$iClass}'></i>";
				} else {
					$html .= "<i class='nav-icon align-middle fa fa-circle-o {$iClass}'></i>";
				}

				if ($hasChild) {
					$parentBadge = isset($item['parentBadge']) ? $item['parentBadge'] : '';
					$html .= "<p class='text align-middle'>{$item['title']} <i class='nav-arrow fa fa-chevron-right'></i>{$parentBadge}</p>";
				} else {
					$html .= "<p class='text align-middle'>{$item['title']}</p>";
				}
				$html .= '  </a>';

				if ($hasChild) {
					$html .= '<ul class="my-menu nav nav-treeview" style="padding-left: 10px;">';
					$html .= $this->printMenu($item['child']);
					$html .= '</ul>';
				}

				$html .= '</li>';
			}
		}

		return $html;
	}

	/**
	 * Check if a leaf menu item should be marked `active`.
	 * Only called for items that have NO children.
	 * Supports: explicit 'active' boolean, callable, or exact URL match.
	 */
	private function _isItemActive($item, $href)
	{
		// Priority 1: Explicit boolean
		if (isset($item['active']) && is_bool($item['active'])) {
			return $item['active'];
		}

		// Priority 2: Callable
		if (isset($item['active']) && is_callable($item['active'])) {
			$CI = get_instance();
			return call_user_func($item['active'], $CI, $href, $this->currentUrl);
		}

		// Priority 3: Exact URL match only
		if (!empty($href) && $href !== '#' && strpos($href, 'javascript') === false) {
			$result = $this->_urlsMatch($this->currentUrl, $href);

			return $result;
		}

		return false;
	}

	/**
	 * Recursively check whether any visible descendant href exactly matches
	 * the current URL. Pure — no state mutation.
	 */
	private function _hasActiveChild($children)
	{
		foreach ($children as $child) {
			if (!isset($child['visible']) || $child['visible'] === true) {
				$href = isset($child['href']) ? $child['href'] : '#';

				if (!empty($href) && $href !== '#' && strpos($href, 'javascript') === false) {
					if ($this->_urlsMatch($this->currentUrl, $href)) {
						return true;
					}
				}

				if (isset($child['child']) && is_array($child['child'])) {
					if ($this->_hasActiveChild($child['child'])) {
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Normalise a URL to a lowercase path with no trailing slash.
	 */
	private function _normalisePath($url)
	{
		$parsed = parse_url($url);
		$path   = isset($parsed['path']) ? $parsed['path'] : $url;
		$path   = strtok($path, '?');
		$path   = rtrim($path, '/');
		if (empty($path) || $path[0] !== '/') {
			$path = '/' . $path;
		}
		return strtolower($path);
	}

	/**
	 * Strict exact-path comparison only.
	 *
	 * Parent open-state is handled entirely via _hasActiveChild => menu-open class.
	 * No prefix/parent matching here — that was the root cause of the original bug.
	 *
	 *   current=/laporan          menu=/laporan           => MATCH
	 *   current=/laporan/missing  menu=/laporan/missing   => MATCH
	 *   current=/laporan          menu=/laporan/missing   => NO MATCH
	 *   current=/laporan/missing  menu=/laporan           => NO MATCH
	 */
	private function _urlsMatch($currentUrl, $menuHref)
	{
		$current = $this->_normalisePath($currentUrl);
		$menu    = $this->_normalisePath($menuHref);

		if (empty($current) || empty($menu) || $current === '/' || $menu === '/') {
			return false;
		}

		$match = ($current === $menu);
		return $match;
	}
}
