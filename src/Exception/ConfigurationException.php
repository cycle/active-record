<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Exception;

use Cycle\ActiveRecord\Contract\ActiveRecordException;

class ConfigurationException extends \RuntimeException implements ActiveRecordException {}
