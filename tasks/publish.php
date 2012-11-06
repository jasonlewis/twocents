<?php

class TwoCents_Publish_Task {

	/**
	 * Publish a specific theme.
	 * 
	 * @param  array  $parameters
	 * @return void
	 */
	public function theme($parameters = array())
	{
		$publish = empty($parameters) ? null : array_shift($parameters);

		$path = path('twocents') . 'themes' . DS;

		$this->publish($publish, $path);
	}

	/**
	 * Publishes a themes assets.
	 * 
	 * @param  string  $publish
	 * @param  string  $path
	 * @return void
	 */
	private function publish($publish, $path)
	{
		if (file_exists($path))
		{
			ob_start();

			$published = 0;

			$publisher = new Laravel\CLI\Tasks\Bundle\Publisher;

			foreach (new FilesystemIterator($path) as $item)
			{
				if ($item->isDir() and ($publish == $item->getFilename() or is_null($publish)))
				{
					$name = $item->getFilename();

					if (file_exists($item->getPathname() . DS . 'public'))
					{
						Bundle::register($this->name($name), array(
							'location' => 'path: ' . $item->getPathname(),
							'handles'  => null,
							'auto'	   => false
						));

						ob_start();

						$publisher->publish($this->name($name));

						Bundle::disable($this->name($name));

						if (str_contains(ob_get_clean(), 'published'))
						{
							echo "Assets for '{$name}' have been published." . PHP_EOL;

							$published++;
						}
						else
						{
							echo "Could not publish assets for '{$name}'." . PHP_EOL;
						}
					}
				}
			}

			if (($string = ob_get_clean()) == '')
			{
				echo "There were no assets to publish.";
			}
			else
			{
				echo $string . "Total published: {$published}" . PHP_EOL . PHP_EOL;
			}
		}
		else
		{
			echo "Could not locate the themes directory. Please check your installation.";
		}
	}

	/**
	 * The name given to a theme when registering it as a bundle.
	 * 
	 * @param  string  $name
	 * @return string
	 */
	protected function name($name)
	{
		return "twocents/{$name}";
	}

}