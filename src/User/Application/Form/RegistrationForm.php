<?php

declare(strict_types=1);

namespace Minimal\User\Application\Form;

/**
 * Registration form data and validation.
 */
class RegistrationForm
{
    private array $errors = [];

    public function __construct(
        private string $email = '',
        private string $username = '',
        private string $password = '',
        private string $passwordConfirm = '',
        private string $firstName = '',
        private string $lastName = ''
    ) {
    }

    /**
     * Create from request data.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            email: (string) ($data['email'] ?? ''),
            username: (string) ($data['username'] ?? ''),
            password: (string) ($data['password'] ?? ''),
            passwordConfirm: (string) ($data['password_confirm'] ?? ''),
            firstName: (string) ($data['first_name'] ?? ''),
            lastName: (string) ($data['last_name'] ?? '')
        );
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getPasswordConfirm(): string
    {
        return $this->passwordConfirm;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * Validate form data.
     */
    public function validate(): bool
    {
        $this->errors = [];

        $this->validateEmail();
        $this->validateUsername();
        $this->validatePassword();
        $this->validatePasswordConfirm();
        $this->validateFirstName();
        $this->validateLastName();

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

    private function validateEmail(): void
    {
        if (empty($this->email)) {
            $this->errors['email'] = 'Email is required';
            return;
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Invalid email format';
        }
    }

    private function validateUsername(): void
    {
        if (empty($this->username)) {
            $this->errors['username'] = 'Username is required';
            return;
        }

        if (strlen($this->username) < 3) {
            $this->errors['username'] = 'Username must be at least 3 characters';
            return;
        }

        if (strlen($this->username) > 50) {
            $this->errors['username'] = 'Username must be less than 50 characters';
            return;
        }

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $this->username)) {
            $this->errors['username'] = 'Username can only contain letters, numbers, underscores and hyphens';
        }
    }

    private function validatePassword(): void
    {
        if (empty($this->password)) {
            $this->errors['password'] = 'Password is required';
            return;
        }

        if (strlen($this->password) < 8) {
            $this->errors['password'] = 'Password must be at least 8 characters';
            return;
        }

        // Optional: Add more password strength requirements
        if (!preg_match('/[A-Z]/', $this->password)) {
            $this->errors['password'] = 'Password must contain at least one uppercase letter';
            return;
        }

        if (!preg_match('/[a-z]/', $this->password)) {
            $this->errors['password'] = 'Password must contain at least one lowercase letter';
            return;
        }

        if (!preg_match('/[0-9]/', $this->password)) {
            $this->errors['password'] = 'Password must contain at least one number';
        }
    }

    private function validatePasswordConfirm(): void
    {
        if (empty($this->passwordConfirm)) {
            $this->errors['password_confirm'] = 'Password confirmation is required';
            return;
        }

        if ($this->password !== $this->passwordConfirm) {
            $this->errors['password_confirm'] = 'Passwords do not match';
        }
    }

    private function validateFirstName(): void
    {
        if (empty($this->firstName)) {
            $this->errors['first_name'] = 'First name is required';
            return;
        }

        if (strlen($this->firstName) > 100) {
            $this->errors['first_name'] = 'First name must be less than 100 characters';
        }
    }

    private function validateLastName(): void
    {
        if (empty($this->lastName)) {
            $this->errors['last_name'] = 'Last name is required';
            return;
        }

        if (strlen($this->lastName) > 100) {
            $this->errors['last_name'] = 'Last name must be less than 100 characters';
        }
    }
}
