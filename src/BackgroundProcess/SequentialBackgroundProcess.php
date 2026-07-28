<?php
namespace Apie\Core\BackgroundProcess;

use Apie\Core\ApieLib;
use Apie\Core\Attributes\AlwaysDisabled;
use Apie\Core\Attributes\Context;
use Apie\Core\Attributes\FakeCount;
use Apie\Core\Attributes\Internal;
use Apie\Core\Attributes\Policy;
use Apie\Core\Attributes\Requires;
use Apie\Core\Attributes\RuntimeCheck;
use Apie\Core\Attributes\StaticCheck;
use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Dto\MessageAndTimestamp;
use Apie\Core\Entities\EntityWithStatesInterface;
use Apie\Core\Enums\ConsoleCommand;
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
#[StaticCheck(new Policy('staticCanViewAny', enabledOnMissingRule: true))]
#[RuntimeCheck(
    new Policy('canView', 'canViewAny')
)]
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

    #[StaticCheck(new Policy('staticReadPayload', enabledOnMissingRule: true))]
    #[RuntimeCheck(new Policy('readPayload'))]
    public function getPayload(): ItemHashmap|ItemList
    {
        return $this->payload;
    }

    #[StaticCheck(new Policy('staticReadErrors', enabledOnMissingRule: true))]
    #[RuntimeCheck(new Policy('readErrors'))]
    public function getErrors(): MessageAndTimestampList
    {
        return $this->errors;
    }

    public function getId(): SequentialBackgroundProcessIdentifier
    {
        return $this->id;
    }

    #[StaticCheck(new Policy('staticReadVersion', enabledOnMissingRule: true))]
    #[RuntimeCheck(new Policy('readVersion'))]
    public function getVersion(): int
    {
        return $this->version;
    }

    #[StaticCheck(new Policy('staticReadStep', enabledOnMissingRule: true))]
    #[RuntimeCheck(new Policy('readStep'))]
    public function getStep(): int
    {
        return $this->step;
    }

    #[StaticCheck(new Policy('staticReadRetries', enabledOnMissingRule: true))]
    #[RuntimeCheck(new Policy('readRetries'))]
    public function getRetries(): int
    {
        return $this->retries;
    }

    #[StaticCheck(new Policy('staticReadTime', enabledOnMissingRule: true))]
    #[RuntimeCheck(new Policy('readTime'))]
    public function getStartTime(): DateTimeInterface
    {
        return $this->startTime;
    }

    #[StaticCheck(new Policy('staticReadTime', enabledOnMissingRule: true))]
    #[RuntimeCheck(new Policy('readTime'))]
    public function getCompletionTime(): ?DateTimeInterface
    {
        return $this->completionTime;
    }

    #[StaticCheck(new Policy('staticReadStatus', enabledOnMissingRule: true))]
    #[RuntimeCheck(new Policy('readStatus'))]
    public function getStatus(): BackgroundProcessStatus
    {
        return $this->status;
    }

    #[StaticCheck(new Policy('staticReadResult', enabledOnMissingRule: true))]
    #[RuntimeCheck(new Policy('readResult'))]
    public function getResult(): mixed
    {
        return $this->result;
    }

    #[Internal]
    public function provideAllowedMethods(): StringList
    {
        if ($this->status === BackgroundProcessStatus::Active) {
            return new StringList(['cancel', 'runStep']);
        }

        return new StringList([]);
    }

    #[StaticCheck(new Policy('staticCancelBackgroundProcess', enabledOnMissingRule: true))]
    #[RuntimeCheck(new Policy('cancelBackgroundProcess'))]
    public function cancel(): void
    {
        if ($this->status !== BackgroundProcessStatus::Active) {
            throw new \LogicException('Process ' . $this->id . ' can not be executed!');
        }
        $this->status = BackgroundProcessStatus::Canceled;
    }

    #[StaticCheck(new Policy('staticRunStep', enabledOnMissingRule: new Requires(ConsoleCommand::class)))]
    #[RuntimeCheck(new Policy('runStep'))]
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
