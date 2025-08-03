<?php

declare(strict_types=1);

namespace Minimal\User\Application\Form;

/**
 * Login form data and validation.
 */
class LoginForm
{
    /** @var array<string, string> */
    private array $errors = [];

    public function __construct(
        private string $emailOrUsername = '',
        private string $password = '',
        private bool $rememberMe = false
    ) {
    }

    /**
     * Create from request data.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $emailOrUsername = $data['email_or_username'] ?? '';
        $password = $data['password'] ?? '';

        return new self(
            emailOrUsername: is_string($emailOrUsername) ? $emailOrUsername : '',
            password: is_string($password) ? $password : '',
            rememberMe: !empty($data['remember_me'])
        );
    }

    public function getEmailOrUsername(): string
    {
        return $this->emailOrUsername;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function isRememberMe(): bool
    {
        return $this->rememberMe;
    }

    /**
     * Validate form data.
     */
    public function validate(): bool
    {
        $this->errors = [];

        $this->validateEmailOrUsername();
        $this->validatePassword();

        return empty($this->errors);
    }

    /**
     * Get validation errors.
     *
     * @return array<string, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if field has error.
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    /**
     * Get error for specific field.
     */
    public function getError(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }

    private function validateEmailOrUsername(): void
    {
        if (empty($this->emailOrUsername)) {
            $this->errors['email_or_username'] = 'Email or username is required';
        }
    }

    private function validatePassword(): void
    {
        if (empty($this->password)) {
            $this->errors['password'] = 'Password is required';
        }
    }
}
