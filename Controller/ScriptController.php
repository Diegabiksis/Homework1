<?php

namespace Controller;

use Model\ConfigManager;
use View\ScriptView;

class ScriptController
{
	public function Execute(array $flags = null): void
	{
		$configManager = new ConfigManager($flags);
		$config = $configManager->GetConfig();
		(new ScriptView())->Output($configManager->GetInput(), $config->GetFormat(), $config->GetConditions(), $config->GetResult());
	}
}
