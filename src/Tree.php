<?php
declare(strict_types=1);

namespace CalTree;

/**
 * ++++++++++++++++
 *  tree
 * ++++++++++++++++
 *
 * Class Tree
 * @package CalTree
 * @author huang_calvin@163.com
 * @dateTime 2023-5-31 18:46
 *
 */
class Tree implements TreeInterface
{
	/**
	 * @var array
	 */
	protected array $list = [];

	/**
	 * @var array
	 */
	protected array $parentList = [];

	/**
	 * @var string
	 */
	protected string $idField = 'id';

	/**
	 * @var string
	 */
	protected string $parentIdField = 'parent_id';

	/**
	 * @var string
	 */
	protected string $childrenField = 'children';

	/**
	 * @var string
	 */
	protected string $leafField;

	/**
	 * @var string
	 */
	protected string $sortField;

	/**
	 * @var array
	 */
	protected array $sortList;

	/**
	 * ++++++++++++++++
	 *  描述
	 * ++++++++++++++++
	 *
	 * @return TreeInterface
	 *
	 * @author huang_calvin@163.com
	 * @dateTime 2023-6-1 9:38
	 */
	public function reset(): TreeInterface
	{
		if (isset($this->list)) $this->list = [];
		if (isset($this->parentList)) $this->parentList = [];
		if (isset($this->sortList)) $this->sortList = [];
		return $this;
	}

	/**
	 * ++++++++++++++++
	 *  描述
	 * ++++++++++++++++
	 *
	 * @param array $conf
	 * @return TreeInterface
	 * @author huang_calvin@163.com
	 * @dateTime 2023-6-1 9:42
	 */
	public function setConf(array $conf): TreeInterface
	{
		if (isset($conf['id_field'])) $this->idField = $conf['id_field'];
		if (isset($conf['parent_id_field'])) $this->parentIdField = $conf['parent_id_field'];
		if (isset($conf['children_field'])) $this->childrenField = $conf['children_field'];
		if (isset($conf['leaf_field'])) $this->leafField = $conf['leaf_field'];
		if (isset($conf['sort_field'])) $this->sortField = $conf['sort_field'];
		return $this;
	}

	/**
	 * ++++++++++++++++
	 *  描述
	 * ++++++++++++++++
	 *
	 * @param array $item
	 * @return TreeInterface
	 *
	 * @author huang_calvin@163.com
	 * @dateTime 2023-6-1 9:49
	 */
	public function buildItem(array $item): TreeInterface
	{
		if (isset($this->list[$item[$this->idField]])) {
			$this->list[$item[$this->idField]] += $item;
			return $this;
		}
		$this->list[$item[$this->idField]] = $item;

		$this->list[$item[$this->parentIdField]][$this->childrenField][] = &$this->list[$item[$this->idField]];
		if (!isset($this->parentList[$item[$this->parentIdField]])) {
			$this->parentList[$item[$this->parentIdField]] = &$this->list[$item[$this->parentIdField]];
		}
		if (isset($this->sortField)) {
			$this->sortList[$item[$this->parentIdField]][] = $item[$this->sortField];
		}
		return $this;
	}

	/**
	 * ++++++++++++++++
	 *  描述
	 * ++++++++++++++++
	 *
	 * @param int $sort
	 * @return TreeInterface
	 * @author huang_calvin@163.com
	 * @dateTime 2023-6-1 10:15
	 */
	public function sort(int $sort = SORT_DESC): TreeInterface
	{
		if (!empty($this->sortList)) {
			foreach ($this->sortList as $pid => $sortArr) {
				if (isset($this->list[$pid])) {
					array_multisort($sortArr, $sort, $this->list[$pid][$this->childrenField]);
				} elseif (isset($this->parentList[$pid])) {
					array_multisort($sortArr, $sort, $this->parentList[$pid][$this->childrenField]);
				}
			}
		}
		return $this;
	}

	/**
	 * ++++++++++++++++
	 *  描述
	 * ++++++++++++++++
	 *
	 * @param bool $isLeaf
	 * @return array
	 * @author huang_calvin@163.com
	 * @dateTime 2023-6-1 10:47
	 */
	public function buildTree(bool $isLeaf = false): array
	{
		$tree = [];
		foreach ($this->parentList as &$value) {
			if (count($value) === 1) {
				$tree = array_merge($tree, $value[$this->childrenField]);
			} else {
				if (isset($this->leafField) && $isLeaf && $this->checkIsLeaf($value[$this->leafField])) {
					unset($value[$this->childrenField]);
				}
			}
		}
		return $tree;
	}

	/**
	 * ++++++++++++++++
	 *  描述
	 * ++++++++++++++++
	 *
	 * @param string|int $leaf
	 * @return bool
	 *
	 * @author huang_calvin@163.com
	 * @dateTime 2023-6-2 11:36
	 */
	protected function checkIsLeaf(string|int $leaf): bool
	{
		if (!is_numeric($leaf)) {
			return false;
		} else {
			$leaf = intval($leaf);
		}
		return $leaf === 1;
	}

	/**
	 * ++++++++++++++++
	 *  描述
	 * ++++++++++++++++
	 *
	 * @param bool $isLeaf
	 * @param int $sort
	 * @return array
	 * @author huang_calvin@163.com
	 * @dateTime 2023-6-1 10:48
	 */
	public function buildTreeWithSort(bool $isLeaf = false, int $sort = SORT_DESC): array
	{
		if (!empty($this->sortList)) {
			$tree = [];
			foreach ($this->sortList as $pid => $sortArr) {
				if (isset($this->list[$pid]) && count($this->list[$pid]) > 1) {
					if (isset($this->leafField) && $isLeaf && $this->checkIsLeaf($this->list[$pid][$this->leafField])) {
						unset($this->list[$pid][$this->childrenField]);
					} else {
						array_multisort($sortArr, $sort, $this->list[$pid][$this->childrenField]);
					}
				} elseif (isset($this->parentList[$pid])) {// 顶级
					array_multisort($sortArr, $sort, $this->parentList[$pid][$this->childrenField]);
					if (count($this->parentList[$pid]) === 1) {
						$tree = array_merge($tree, $this->parentList[$pid][$this->childrenField]);
						// array_push($tree, ...$this->parentList[$pid][$this->childrenField]);
					}
				}
			}
			return $tree;
		} else {
			return $this->buildTree($isLeaf);
		}
	}

	/**
	 * ++++++++++++++++
	 *  获取list
	 * ++++++++++++++++
	 *
	 * @param string $type
	 * @return array
	 * @throws \Exception
	 *
	 * @author huang_calvin@163.com
	 * @dateTime 2024-6-11 10:52
	 */
	public function getList(string $type = self::Tree_List_Parent): array
	{
		switch ($type) {
			case self::Tree_List_Parent:
				return unserialize(serialize($this->parentList));
			case self::Tree_List_One:
				return unserialize(serialize($this->list));
		}
		throw new \Exception('undefined list type :' . $type);
	}

	/**
	 * ++++++++++++++++
	 *  获取一项
	 * ++++++++++++++++
	 *
	 * @param int|string $id
	 * @param string $type
	 * @return array
	 * @throws \Exception
	 *
	 * @author huang_calvin@163.com
	 * @dateTime 2024-6-11 11:33
	 */
	public function getItem(int|string $id, string $type = self::Tree_Item_Id): array
	{
		switch ($type) {
			case self::Tree_Item_Id:
				if (isset($this->list[$id])) return unserialize(serialize($this->list[$id]));
				return [];
			case self::Tree_Item_Pid:
				if (isset($this->parentList[$id])) return unserialize(serialize($this->parentList[$id]));
				return [];
		}
		throw new \Exception('undefined list type :' . $type);
	}

	/**
	 * ++++++++++++++++
	 *  获取ids
	 * ++++++++++++++++
	 *
	 * @param int|string $id
	 * @return array
	 *
	 * @author huang_calvin@163.com
	 * @dateTime 2024-6-11 11:47
	 */
	public function getChildrenIds(int|string $id): array
	{
		if (!empty($this->parentList[$id][$this->childrenField])) {
			return array_column($this->parentList[$id][$this->childrenField], $this->idField);
		}
		return [];
	}

	/**
	 * ++++++++++++++++
	 *  获取ids(multi)
	 * ++++++++++++++++
	 *
	 * @param array|null $ids
	 * @return array
	 *
	 * @author huang_calvin@163.com
	 * @dateTime 2024-6-11 11:47
	 */
	public function getMultiChildrenIds(?array $ids = null): array
	{
		$data = [];
		if (empty($ids)) {
			foreach ($this->list as $item) {
				if (isset($item[$this->idField])) {
					$data[] = $item[$this->idField];
				}
			}
		} else {
			foreach ($ids as $id) {
				$data = array_merge($data, $this->getChildrenIds($id));
			}
		}
		return $data;
	}

	/**
	 * ++++++++++++++++
	 *  判断是否为空
	 * ++++++++++++++++
	 *
	 * @return bool
	 *
	 * @author huang_calvin@163.com
	 * @dateTime 2024-6-11 15:58
	 */
	public function isEmpty(): bool
	{
		return empty($this->list);
	}

	/**
	 * ++++++++++++++++
	 *  过滤已经填充的ID
	 * ++++++++++++++++
	 *
	 * @param array $ids
	 * @return array
	 *
	 * @author huang_calvin@163.com
	 * @dateTime 2024-6-11 17:26
	 */
	public function filterIds(array $ids): array
	{
		$list = [];
		foreach ($ids as $id) {
			if (!isset($this->list[$id]) || count($this->list[$id]) <= 1) {
				$list[] = $id;
			}
		}
		return $list;
	}
}