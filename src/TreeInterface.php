<?php
declare(strict_types=1);

namespace CalTree;
interface TreeInterface
{
	public function reset(): TreeInterface;

	public function setConf(array $conf): TreeInterface;

	public function buildItem(array $item): TreeInterface;

	public function sort(int $sort = SORT_DESC): TreeInterface;

	public function buildTree(bool $isLeaf = false): array;

	public function buildTreeWithSort(bool $isLeaf = false, int $sort = SORT_DESC): array;
}