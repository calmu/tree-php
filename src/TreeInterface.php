<?php
declare(strict_types=1);

namespace CalTree;
interface TreeInterface
{
	/**
	 * 原始id数组 [id => array()]
	 */
	const Tree_List_One = 'tree_list_one';

	/**
	 * 父ID数组 [parent_id => array()]
	 */
	const Tree_List_Parent = 'tree_list_parent';

	const Tree_Item_Id = 'tree_item_id';
	const Tree_Item_Pid = 'tree_item_id';

	public function reset(): TreeInterface;

	public function setConf(array $conf): TreeInterface;

	public function buildItem(array $item): TreeInterface;

	public function sort(int $sort = SORT_DESC): TreeInterface;

	public function buildTree(bool $isLeaf = false): array;

	public function buildTreeWithSort(bool $isLeaf = false, int $sort = SORT_DESC): array;

	public function getList(string $type = self::Tree_List_Parent): array;

	public function getItem(int|string $id, string $type = self::Tree_Item_Id): array;

	public function getChildrenIds(int|string $id): array;

	public function getMultiChildrenIds(?array $ids = null): array;

	public function isEmpty(): bool;

	public function filterIds(array $ids): array;
}