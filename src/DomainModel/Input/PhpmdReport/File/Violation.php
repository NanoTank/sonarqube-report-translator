<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input\PhpmdReport\File;

/**
 * @codeCoverageIgnore
 */
class Violation
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        private readonly int $beginLine,
        private readonly int $endLine,
        private readonly ?string $package,
        private readonly ?string $function,
        private readonly ?string $class,
        private readonly ?string $method,
        private readonly string $description,
        private readonly string $rule,
        private readonly string $ruleSet,
        private readonly string $externalInfoUrl,
        private readonly int $priority,
    ) {
    }

    public function getBeginLine(): int
    {
        return $this->beginLine;
    }

    public function getEndLine(): int
    {
        return $this->endLine;
    }

    public function getPackage(): ?string
    {
        return $this->package;
    }

    public function getFunction(): ?string
    {
        return $this->function;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getRule(): string
    {
        return $this->rule;
    }

    public function getRuleSet(): string
    {
        return $this->ruleSet;
    }

    public function getExternalInfoUrl(): string
    {
        return $this->externalInfoUrl;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
