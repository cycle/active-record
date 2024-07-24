<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Exception\Transaction;

use Cycle\ActiveRecord\Exception\ActiveRecordException;

class TransactionException extends \RuntimeException implements ActiveRecordException {}
