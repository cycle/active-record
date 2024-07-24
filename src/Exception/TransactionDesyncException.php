<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Exception;

class TransactionDesyncException extends \LogicException implements ActiveRecordException {}
