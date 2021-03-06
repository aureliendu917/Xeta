<?php
namespace App\View\Helper;

use Acl\Controller\Component\AclComponent;
use Cake\Controller\ComponentRegistry;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\View\Helper;
use Cake\View\View;

class AclHelper extends Helper {

/**
 * Helpers used.
 *
 * @var array
 */
	public $helpers = ['Html'];

/**
 * Acl Instance.
 *
 * @var object
 */
	public $Acl;

/**
 * Construct method.
 *
 * @param \Cake\View\View $view The view that was fired.
 * @param array $config The config passed to the class.
 */
	public function __construct(View $view, $config = []) {
		parent::__construct($view, $config);

		$collection = new ComponentRegistry();
		$this->Acl = new AclComponent($collection);
	}

/**
 * Check if the user can access to the given URL.
 *
 * @param array $params The params to check.
 *
 * @return string
 */
	public function check(array $params = []) {
		$params += ['_base' => false];

		$url = Router::url($params);
		$params = Router::parse($url);

		$user = ['Users' => $this->request->session()->read('Auth.User')];

		return $this->Acl->check($user, $this->_getPath($params));
	}

/**
 * Generate the link only if the user has access to the given url.
 *
 * @param string $title The content to be wrapped by <a> tags.
 * @param string|array|null $url Cake-relative URL or array of URL parameters, or
 * external URL (starts with http://)
 * @param array $options Array of options and HTML attributes.
 *
 * @return string
 */
	public function link($title, $url = null, array $options = []) {
		if (!$this->check($url)) {
			return '';
		}

		return $this->Html->link($title, $url, $options);
	}

/**
 * Get the action path for a given url.
 *
 * @param array  $params An array of the request params.
 * @param string $path Path.
 *
 * @return string
 */
	protected function _getPath(array $params, $path = '/:plugin/:prefix/:controller/:action') {
		$plugin = empty($params['plugin']) ? null : Inflector::camelize($params['plugin']) . '/';
		$prefix = empty($params['prefix']) ? null : $params['prefix'] . '/';
		$path = str_replace(
			[':controller', ':action', ':plugin/', ':prefix/'],
			[Inflector::camelize($params['controller']), $params['action'], $plugin, $prefix],
			'app/' . $path
		);
		$path = str_replace('//', '/', $path);

		return trim($path, '/');
	}

}
