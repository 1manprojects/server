<?php
/**
 * @copyright Copyright (c) 2016, ownCloud, Inc.
 *
 * @author Klaus Herberth <klaus@jsxc.org>
 *
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OC\Core\Command\App;

use OC\Installer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Install extends Command {

	protected function configure() {
		$this
			->setName('app:install')
			->setDescription('install an app')
			->addArgument(
				'app-id',
				InputArgument::REQUIRED,
				'install the specified app'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$appId = $input->getArgument('app-id');

		if (\OC_App::getAppPath($appId)) {
			$output->writeln($appId . ' already installed');
			return 1;
		}

		try {
			$installer = new Installer(
				\OC::$server->getAppFetcher(),
				\OC::$server->getHTTPClientService(),
				\OC::$server->getTempManager(),
				\OC::$server->getLogger(),
				\OC::$server->getConfig()
			);
			$installer->downloadApp($appId);
			$result = $installer->installApp($appId);
		} catch(Exception $e) {
			$output->writeln('Error: ' . $e->getMessage());
			return 1;
		}

		if($result === false) {
			$output->writeln($appId . ' couldn\'t be installed');
			return 1;
		}

		$output->writeln($appId . ' installed');

		return 0;
	}
}
