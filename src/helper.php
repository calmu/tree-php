<?php
declare(strict_types=1);

use CalTree\TreeInterface;

if (!function_exists('cal_tree')) {
	/**
	 * ++++++++++++++++
	 *  描述
	 * ++++++++++++++++
	 *
	 * @author huang_calvin@163.com
	 * @dateTime 2023-6-3 10:56
	 * @param array $conf
	 * @return TreeInterface
	 * 
	 */
	function cal_tree(array $conf = []): TreeInterface
	{
		$obj = new \CalTree\Tree();
		$obj->setConf($conf);
		return $obj;
	}
}