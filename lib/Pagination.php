<?php

class Pagination
{
	// Merged configuration settings
	protected $config = array(
		'current_page'      => array('source' => 'query_string', 'key' => 'page'),
		'total_items'       => 0,
		'items_per_page'    => 10,
		'view'              => 'pagination/basic.html',
		'auto_hide'         => TRUE,
		'first_page_in_url' => FALSE,
	);

	// Current page number
	protected $current_page;

	// Total item count
	protected $total_items;

	// How many items to show per page
	protected $items_per_page;

	// Total page count
	protected $total_pages;

	// Item offset for the first item displayed on the current page
	protected $current_first_item;

	// Item offset for the last item displayed on the current page
	protected $current_last_item;

	// Previous page number; FALSE if the current page is the first one
	protected $previous_page;

	// Next page number; FALSE if the current page is the last one
	protected $next_page;

	// First page number; FALSE if the current page is the first one
	protected $first_page;

	// Last page number; FALSE if the current page is the last one
	protected $last_page;

	// Query offset
	protected $offset;

	// Request object
	protected $_request;

	// Route to use for URIs
	protected $_route;

	/**
	 * @param array $config
	 * @param Http_Request $request
	 * @return Pagination
	 */
	public static function factory(array $config = array(), Http_Request $request = NULL)
	{
		return new self($config, $request);
	}

	/**
	 * @param array $config
	 * @param Http_Request $request
	 */
	public function __construct(array $config = array(), Http_Request $request = NULL)
	{
		$this->_request = $request;

		// Pagination setup
		$this->setup($config);
	}

	public function setup(array $config = array())
	{
		// Overwrite the current config settings
		$this->config = $config + $this->config;

		// Only (re)calculate pagination when needed
		if ($this->current_page === NULL
			OR isset($config['current_page'])
			OR isset($config['total_items'])
			OR isset($config['items_per_page']))
		{
			// Retrieve the current page number
			if ( ! empty($this->config['current_page']['page'])) {
				// The current page number has been set manually
				$this->current_page = (int) $this->config['current_page']['page'];
			}
			else {
				$query_key = $this->config['current_page']['key'];

				switch ($this->config['current_page']['source']) {
					case 'query_string':
						$page = $this->_request->getGetVar($query_key);
						$this->current_page = $page;
						break;
				}
			}

			// Calculate and clean all pagination variables
			$this->total_items        = (int) max(0, $this->config['total_items']);
			$this->items_per_page     = (int) max(1, $this->config['items_per_page']);
			$this->total_pages        = (int) ceil($this->total_items / $this->items_per_page);
			$this->current_page       = (int) min(max(1, $this->current_page), max(1, $this->total_pages));
			$this->current_first_item = (int) min((($this->current_page - 1) * $this->items_per_page) + 1, $this->total_items);
			$this->current_last_item  = (int) min($this->current_first_item + $this->items_per_page - 1, $this->total_items);
			$this->previous_page      = ($this->current_page > 1) ? $this->current_page - 1 : FALSE;
			$this->next_page          = ($this->current_page < $this->total_pages) ? $this->current_page + 1 : FALSE;
			$this->first_page         = ($this->current_page === 1) ? FALSE : 1;
			$this->last_page          = ($this->current_page >= $this->total_pages) ? FALSE : $this->total_pages;
			$this->offset             = (int) (($this->current_page - 1) * $this->items_per_page);
		}

		// Chainable method
		return $this;
	}

	/**
	 * Generates the full URL for a certain page.
	 *
	 * @param   integer  page number
	 * @return  string   page URL
	 */
	public function url($page = 1)
	{
		// Clean the page number
		$page = max(1, (int) $page);

		// No page number in URLs to first page
		if ($page === 1 AND ! $this->config['first_page_in_url']) {
			$page = NULL;
		}

		switch ($this->config['current_page']['source']) {
			case 'query_string':

				//TODO: rewrite
				$uri = $this->_request->getUri();
				$httpUrl  = new Http_Url($uri);

				return $httpUrl->getPath() . ($page ? ('?' . $this->config['current_page']['key'] .'=' . $page) : NULL);
				break;
//			case 'route':
//				return URL::site(Request::current()->uri(array($this->config['current_page']['key'] => $page))).URL::query();
//				break;
		}

		return '#';
	}

	/**
	 * @param $page
	 * @return bool
	 */
	public function valid_page($page)
	{
		return $page > 0 AND $page <= $this->total_pages;
	}

	/**
	 * Renders the pagination links.
	 *
	 * @param mixed string of the view to use
	 * @return string
	 */
	public function render($view = NULL)
	{
//		var_dump($this->total_pages);
//		exit;


		// Automatically hide pagination whenever it is superfluous
		if ($this->config['auto_hide'] === TRUE AND $this->total_pages <= 1) {
			return '';
		}

		if ($view === NULL) {
			// Use the view from config
			$view = $this->config['view'];
		}

		// Load the view file
		$data = array_merge(get_object_vars($this), array('page' => $this));
		$coreView = Core_View::create($view, $data);

		// Pass on the whole Pagination object
		return $coreView->render();
	}

	/**
	 * Renders the pagination links.
	 *
	 * @return  string  pagination output (HTML)
	 */
	public function __toString()
	{
		return $this->render();
	}

	public function getOffset()
	{
		return $this->offset;
	}

	public function getItemsPerPage()
	{
		return $this->items_per_page;
	}

} // End Pagination