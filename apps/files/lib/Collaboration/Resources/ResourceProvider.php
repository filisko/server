<?php
/**
 * @copyright Copyright (c) 2018 Joas Schilling <coding@schilljs.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Files\Collaboration\Resources;


use OCP\Collaboration\Resources\IProvider;
use OCP\Collaboration\Resources\IResource;
use OCP\Files\IRootFolder;
use OCP\IUser;

class ResourceProvider implements IProvider {

	/** @var IRootFolder */
	protected $rootFolder;

	/** @var array */
	protected $nodes = [];

	public function __construct(IRootFolder $rootFolder) {
		$this->rootFolder = $rootFolder;
	}

	/**
	 * Get the display name of a resource
	 *
	 * @param IResource $resource
	 * @return string
	 * @since 15.0.0
	 */
	public function getName(IResource $resource): string {
		if (isset($this->nodes[(int) $resource->getId()])) {
			return $this->nodes[(int) $resource->getId()]->getPath();
		}
		return '';
	}

	/**
	 * Can a user/guest access the collection
	 *
	 * @param IResource $resource
	 * @param IUser $user
	 * @return bool
	 * @since 15.0.0
	 */
	public function canAccess(IResource $resource, IUser $user = null): bool {
		if ($resource->getType() !== 'files') {
			return false;
		}

		if (!$user instanceof IUser) {
			return false;
		}

		$userFolder = $this->rootFolder->getUserFolder($user->getUID());
		$nodes = $userFolder->getById((int) $resource->getId());

		if (!empty($nodes)) {
			$this->nodes[(int) $resource->getId()] = array_shift($nodes);
			return true;
		}

		return false;
	}
}
