<?php
namespace Apie\Core\BackgroundProcess;

use Apie\Core\ApieLib;
use Apie\Core\Attributes\AlwaysDisabled;
use Apie\Core\Attributes\Context;
use Apie\Core\Attributes\FakeCount;
use Apie\Core\Attributes\StaticCheck;
use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Dto\MessageAndTimestamp;
use Apie\Core\Entities\EntityWithStatesInterface;
use Apie\Core\Identifiers\PascalCaseSlug;
use Apie\Core\Identifiers\Ulid;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\ItemList;
use Apie\Core\Lists\MessageAndTimestampList;
use Apie\Core\Lists\StringList;
use Apie\Core\ValueObjects\DatabaseText;
use DateTimeInterface;
use ReflectionClass;
use Throwable;

#[FakeCount(0)]
class SequentialBackgroundProcess implements EntityWithStatesInterface
{
    private int $version;
    private int $step;
    private int $retries = 0;
    private DateTimeInterface $startTime;
    private ?DateTimeInterface $completionTime = null;
    private DatabaseText $className;
    private BackgroundProcessStatus $status = BackgroundProcessStatus::Active;
    private SequentialBackgroundProcessIdentifier $id;
    private mixed $result = null;
    private MessageAndTimestampList $errors;

    #[StaticCheck(new AlwaysDisabled())]
    public function __construct(
        BackgroundProcessDeclaration $backgroundProcessDeclaration,
        private ItemHashmap|ItemList $payload
    ) {
        $this->className = new DatabaseText(get_debug_type($backgroundProcessDeclaration));
        $this->version = $backgroundProcessDeclaration->getCurrentVersion();
        $this->step = 0;
        $this->startTime = ApieLib::getPsrClock()->now();
        $this->id = new SequentialBackgroundProcessIdentifier(
            new PascalCaseSlug((new ReflectionClass($backgroundProcessDeclaration))->getShortName()),
            Ulid::createRandom()
        );
        $this->errors = new MessageAndTimestampList();
    }

    public function getPayload(): ItemHashmap|ItemList
    {
        return $this->payload;
    }

    public function getErrors(): MessageAndTimestampList
    {
        return $this->errors;
    }

    public function getId(): SequentialBackgroundProcessIdentifier
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getStep(): int
    {
        return $this->step;
    }

    public function getRetries(): int
    {
        return $this->retries;
    }

    public function getStartTime(): DateTimeInterface
    {
        return $this->startTime;
    }

    public function getCompletionTime(): ?DateTimeInterface
    {
        return $this->completionTime;
    }

    public function getStatus(): BackgroundProcessStatus
    {
        return $this->status;
    }

    public function getResult(): mixed
    {
        return $this->result;
    }

    public function provideAllowedMethods(): StringList
    {
        if ($this->status === BackgroundProcessStatus::Active) {
            return new StringList(['cancel', 'runStep']);
        }

        return new StringList([]);
    }

    public function cancel(): void
    {
        if ($this->status !== BackgroundProcessStatus::Active) {
            throw new \LogicException('Process ' . $this->id . ' can not be executed!');
        }
        $this->status = BackgroundProcessStatus::Canceled;
    }

    public function runStep(#[Context()] ApieContext $apieContext): void
    {
        if ($this->status !== BackgroundProcessStatus::Active) {
            throw new \LogicException('Process ' . $this->id . ' can not be executed!');
        }
        $apieContext = $apieContext->withContext(ContextConstants::BACKGROUND_PROCESS, $this->result);
        $maxRetries = 1;
        try {
            $className = $this->className->toNative();
            $maxRetries = $className::getMaxRetries($this->version);
            $steps = array_values($className::retrieveDeclaration($this->version));
            if (isset($steps[$this->step])) {
                $this->result = call_user_func($steps[$this->step], $apieContext, $this->payload);
                $this->step++;
                $this->retries = 0;
            } else {
                $this->completionTime = ApieLib::getPsrClock()->now();
                $this->status = BackgroundProcessStatus::Finished;
            }
        } catch (Throwable $error) {
            $this->errors[] = new MessageAndTimestamp(
                $error->getMessage(),
                ApieLib::getPsrClock()->now()
            );
            $this->retries++;
            if ($this->retries >= $maxRetries) {
                $this->status = BackgroundProcessStatus::TooManyErrors;
            }
        }
    }
}
