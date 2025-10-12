# Domain-Driven Design (DDD): Complete Guide

## Table of Contents
1. [What is Domain-Driven Design?](#what-is-domain-driven-design)
2. [Core Concepts](#core-concepts)
3. [Building Blocks](#building-blocks)
4. [Architecture Patterns](#architecture-patterns)
5. [Best Practices](#best-practices)
6. [Implementation Strategy](#implementation-strategy)
7. [Advantages and Disadvantages](#advantages-and-disadvantages)
8. [When to Use DDD](#when-to-use-ddd)
9. [Common Patterns](#common-patterns)
10. [FAQ - Frequently Asked Questions](#faq---frequently-asked-questions)
11. [Tools and Resources](#tools-and-resources)

## What is Domain-Driven Design?

Domain-Driven Design (DDD) is a software development approach that focuses on creating software that reflects a deep understanding of the business domain. It emphasizes collaboration between technical and domain experts to create a shared understanding of the business.

### Key Philosophy
- **Domain First**: The domain model should be the heart of the application
- **Ubiquitous Language**: Use the same terminology throughout the codebase as used by domain experts
- **Collaboration**: Technical and domain experts work together
- **Iterative**: Continuous refinement of the domain model

## Core Concepts

### 1. Domain
The **domain** is the sphere of knowledge and activity around which the application logic revolves. It's the business area you're working in.

**Examples:**
- E-commerce: Product catalog, orders, payments, shipping
- Banking: Accounts, transactions, loans, customers
- Healthcare: Patients, appointments, treatments, billing

### 2. Domain Model
A **domain model** is a system of abstractions that describes selected aspects of a domain and can be used to solve problems related to that domain.

### 3. Bounded Context
A **bounded context** is a logical boundary within which a particular domain model is defined and applicable. Each context has its own model and language.

**Example:**
```
┌─────────────────┐    ┌─────────────────┐
│   User Context  │    │  Order Context  │
│                 │    │                 │
│ - Registration  │    │ - Order Items   │
│ - Authentication│    │ - Pricing       │
│ - Profile       │    │ - Shipping      │
└─────────────────┘    └─────────────────┘
```

### 4. Ubiquitous Language
A **ubiquitous language** is a common vocabulary used by all team members to connect all activities of the team with the software.

**Example:**
- Business: "Customer places an order"
- Code: `Customer::placeOrder(Order $order)`
- Database: `orders` table with `customer_id`

## Building Blocks

### 1. Entities
**Entities** are objects that have a distinct identity that runs through time and different states.

```php
<?php

namespace App\Domains\User\Models;

class User
{
    private UserId $id;
    private Email $email;
    private Name $name;
    private UserStatus $status;
    
    public function __construct(UserId $id, Email $email, Name $name)
    {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
        $this->status = UserStatus::ACTIVE;
    }
    
    public function changeEmail(Email $newEmail): void
    {
        if (!$this->isEmailVerified()) {
            throw new EmailNotVerifiedException();
        }
        $this->email = $newEmail;
    }
    
    public function getId(): UserId
    {
        return $this->id;
    }
    
    public function isEmailVerified(): bool
    {
        return $this->email->isVerified();
    }
}
```

### 2. Value Objects
**Value Objects** are objects that are defined entirely by their attributes and have no identity.

```php
<?php

namespace App\Domains\User\ValueObjects;

class Email
{
    public function __construct(private string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException("Invalid email format: {$value}");
        }
    }
    
    public function value(): string
    {
        return $this->value;
    }
    
    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }
    
    public function __toString(): string
    {
        return $this->value;
    }
}
```

### 3. Aggregates
**Aggregates** are clusters of domain objects that can be treated as a single unit.

```php
<?php

namespace App\Domains\Order\Models;

class Order
{
    private OrderId $id;
    private CustomerId $customerId;
    private array $items = [];
    private OrderStatus $status;
    private Money $total;
    
    public function addItem(Product $product, int $quantity): void
    {
        if ($this->status !== OrderStatus::DRAFT) {
            throw new OrderCannotBeModifiedException();
        }
        
        $item = new OrderItem($product, $quantity);
        $this->items[] = $item;
        $this->recalculateTotal();
    }
    
    public function confirm(): void
    {
        if (empty($this->items)) {
            throw new EmptyOrderException();
        }
        
        $this->status = OrderStatus::CONFIRMED;
        DomainEvents::dispatch(new OrderConfirmed($this));
    }
    
    private function recalculateTotal(): void
    {
        $total = Money::zero();
        foreach ($this->items as $item) {
            $total = $total->add($item->getSubtotal());
        }
        $this->total = $total;
    }
}
```

### 4. Domain Services
**Domain Services** contain domain logic that doesn't naturally fit into entities or value objects.

```php
<?php

namespace App\Domains\User\Services;

class UserRegistrationService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher,
        private EventDispatcherInterface $eventDispatcher
    ) {}
    
    public function registerUser(RegisterUserCommand $command): User
    {
        // Check if email already exists
        if ($this->userRepository->emailExists($command->email)) {
            throw new EmailAlreadyTakenException($command->email);
        }
        
        // Create user with hashed password
        $hashedPassword = $this->passwordHasher->hash($command->password);
        $user = new User(
            UserId::generate(),
            $command->email,
            $command->name,
            $hashedPassword
        );
        
        // Save user
        $this->userRepository->save($user);
        
        // Dispatch domain event
        $this->eventDispatcher->dispatch(new UserRegistered($user));
        
        return $user;
    }
}
```

### 5. Repositories
**Repositories** provide an abstraction for accessing domain objects.

```php
<?php

namespace App\Domains\User\Contracts;

interface UserRepositoryInterface
{
    public function findById(UserId $id): ?User;
    public function findByEmail(Email $email): ?User;
    public function save(User $user): void;
    public function delete(UserId $id): void;
    public function findAll(): array;
    public function findByStatus(UserStatus $status): array;
}
```

### 6. Domain Events
**Domain Events** represent something significant that has happened in the domain.

```php
<?php

namespace App\Domains\User\Events;

class UserRegistered
{
    public function __construct(
        public readonly User $user,
        public readonly DateTime $occurredAt
    ) {}
}

class UserEmailChanged
{
    public function __construct(
        public readonly User $user,
        public readonly Email $oldEmail,
        public readonly Email $newEmail,
        public readonly DateTime $occurredAt
    ) {}
}
```

### 7. Factories
**Factories** encapsulate the creation of complex objects and aggregates.

```php
<?php

namespace App\Domains\Order\Factories;

class OrderFactory
{
    public function createOrder(CustomerId $customerId, array $items): Order
    {
        $order = new Order(OrderId::generate(), $customerId);
        
        foreach ($items as $itemData) {
            $product = $this->productRepository->findById($itemData['productId']);
            $order->addItem($product, $itemData['quantity']);
        }
        
        return $order;
    }
}
```

## Architecture Patterns

### 1. Layered Architecture
```
┌─────────────────────────────────────┐
│           Presentation Layer        │ ← Controllers, Views, APIs
├─────────────────────────────────────┤
│          Application Layer          │ ← Use Cases, Commands, Queries
├─────────────────────────────────────┤
│            Domain Layer             │ ← Entities, Value Objects, Services
├─────────────────────────────────────┤
│        Infrastructure Layer         │ ← Repositories, External Services
└─────────────────────────────────────┘
```

### 2. Hexagonal Architecture (Ports and Adapters)
```
                    ┌─────────────────┐
                    │   Application   │
                    │     Core        │
                    └─────────────────┘
                           │
        ┌──────────────────┼──────────────────┐
        │                  │                  │
   ┌─────────┐        ┌─────────┐        ┌─────────┐
   │Primary  │        │Domain   │        │Secondary│
   │Adapters │        │Services │        │Adapters │
   │(Drivers)│        │         │        │(Driven) │
   └─────────┘        └─────────┘        └─────────┘
```

### 3. Command Query Responsibility Segregation (CQRS)
```php
// Commands (Write Operations)
class RegisterUserCommand
{
    public function __construct(
        public Email $email,
        public Name $name,
        public Password $password
    ) {}
}

class RegisterUserCommandHandler
{
    public function handle(RegisterUserCommand $command): void
    {
        $this->userService->registerUser($command);
    }
}

// Queries (Read Operations)
class UserQueryService
{
    public function getUserById(UserId $id): UserView
    {
        return $this->userReadRepository->findById($id);
    }
    
    public function searchUsers(string $query): array
    {
        return $this->userReadRepository->search($query);
    }
}
```

## Best Practices

### 1. Dependency Direction
- **Rule**: Dependencies point inward toward the domain
- **Practice**: Domain never depends on infrastructure
- **Example**: Repository interface in domain, implementation in infrastructure

### 2. Rich Domain Model
```php
// ✅ Good: Rich domain model
class Order
{
    public function calculateTotal(): Money
    {
        $total = Money::zero();
        foreach ($this->items as $item) {
            $total = $total->add($item->getSubtotal());
        }
        return $total->applyDiscount($this->discount);
    }
    
    public function canBeCancelled(): bool
    {
        return $this->status === OrderStatus::PENDING && 
               $this->createdAt->diffInHours(now()) < 24;
    }
}

// ❌ Bad: Anemic domain model
class Order
{
    public array $items;
    public float $discount;
    public string $status;
    // No business logic
}
```

### 3. Event-Driven Architecture
```php
// Domain events for loose coupling
class UserService
{
    public function registerUser(RegisterUserCommand $command): void
    {
        $user = new User($command->email, $command->name);
        $this->userRepository->save($user);
        
        // Fire domain event
        DomainEvents::dispatch(new UserRegistered($user));
    }
}

// Event handlers for side effects
class SendWelcomeEmailHandler
{
    public function handle(UserRegistered $event): void
    {
        $this->emailService->sendWelcomeEmail($event->user);
    }
}

class LogUserRegistrationHandler
{
    public function handle(UserRegistered $event): void
    {
        Log::info('User registered', [
            'user_id' => $event->user->getId(),
            'email' => $event->user->getEmail(),
        ]);
    }
}
```

### 4. Specification Pattern
```php
interface SpecificationInterface
{
    public function isSatisfiedBy($candidate): bool;
}

class UserIsActiveSpecification implements SpecificationInterface
{
    public function isSatisfiedBy(User $user): bool
    {
        return $user->getStatus() === UserStatus::ACTIVE;
    }
}

class UserEmailVerifiedSpecification implements SpecificationInterface
{
    public function isSatisfiedBy(User $user): bool
    {
        return $user->isEmailVerified();
    }
}

// Usage
$activeUsers = $this->userRepository->findBySpecification(
    new UserIsActiveSpecification()
);
```

### 5. Factory Pattern for Complex Objects
```php
class UserFactory
{
    public function createFromRegistration(RegisterUserCommand $command): User
    {
        $userId = UserId::generate();
        $email = new Email($command->email);
        $name = new Name($command->name);
        $password = new Password($command->password);
        
        return new User($userId, $email, $name, $password);
    }
    
    public function createAdminUser(string $email, string $name): User
    {
        $user = $this->createFromRegistration(new RegisterUserCommand($email, $name, 'temp'));
        $user->promoteToAdmin();
        return $user;
    }
}
```

## Implementation Strategy

### Phase 1: Foundation
1. **Identify Bounded Contexts**
   - User Management
   - Authentication
   - Order Processing
   - Payment Processing

2. **Create Ubiquitous Language**
   - Glossary of terms
   - Consistent naming
   - Domain-specific vocabulary

3. **Set Up Project Structure**
```
app/
├── Domains/
│   ├── User/
│   │   ├── Models/
│   │   ├── Services/
│   │   ├── Repositories/
│   │   ├── Events/
│   │   ├── Listeners/
│   │   └── ValueObjects/
│   └── Order/
│       ├── Models/
│       ├── Services/
│       └── ...
├── Shared/
│   ├── Http/
│   └── Exceptions/
└── Providers/
```

### Phase 2: Core Domain
1. **Implement Entities**
   - Start with core business entities
   - Add business logic to entities
   - Ensure proper encapsulation

2. **Create Value Objects**
   - Identify concepts that don't have identity
   - Make them immutable
   - Add validation logic

3. **Define Repositories**
   - Create interfaces in domain
   - Implement in infrastructure
   - Use dependency injection

### Phase 3: Advanced Features
1. **Add Domain Events**
   - Identify significant business events
   - Create event classes
   - Implement event handlers

2. **Implement Domain Services**
   - Move complex business logic from entities
   - Keep services stateless
   - Use dependency injection

3. **Add Specifications**
   - Create reusable business rules
   - Use for complex queries
   - Keep domain logic in domain

## Advantages and Disadvantages

### Advantages

#### 1. Business Alignment
- ✅ Code reflects business reality
- ✅ Easier communication with stakeholders
- ✅ Business logic is explicit and testable
- ✅ Domain experts can understand the code

#### 2. Maintainability
- ✅ Clear separation of concerns
- ✅ Changes are localized to specific domains
- ✅ Easy to understand and modify
- ✅ Reduced coupling between components

#### 3. Testability
- ✅ Domain logic can be tested in isolation
- ✅ Mocking is easier with clear interfaces
- ✅ Business rules are explicit
- ✅ Unit tests are more meaningful

#### 4. Scalability
- ✅ Different domains can scale independently
- ✅ Microservices can be extracted from domains
- ✅ Team organization follows domain boundaries
- ✅ Technology choices can vary by domain

#### 5. Flexibility
- ✅ Easy to change implementation details
- ✅ Technology choices can vary by domain
- ✅ Legacy systems can be gradually replaced
- ✅ Easy to add new features

### Disadvantages

#### 1. Complexity
- ❌ Steep learning curve
- ❌ More abstractions and layers
- ❌ Can be overkill for simple applications
- ❌ Requires understanding of DDD concepts

#### 2. Development Time
- ❌ Initial setup takes longer
- ❌ More code to write and maintain
- ❌ Requires more planning upfront
- ❌ Can slow down initial development

#### 3. Team Requirements
- ❌ Requires domain expertise
- ❌ Team needs to understand DDD concepts
- ❌ Collaboration between devs and domain experts
- ❌ May require additional training

#### 4. Over-Engineering Risk
- ❌ Can lead to unnecessary abstractions
- ❌ May complicate simple problems
- ❌ Risk of analysis paralysis
- ❌ Can create too many layers

## When to Use DDD

### Good Candidates
- ✅ Complex business domains
- ✅ Long-lived applications
- ✅ Multiple teams working on different areas
- ✅ High business logic complexity
- ✅ Need for domain expertise
- ✅ Applications with changing requirements
- ✅ Systems with multiple bounded contexts

### Poor Candidates
- ❌ Simple CRUD applications
- ❌ Short-term projects
- ❌ Data-driven applications
- ❌ Small teams with limited domain knowledge
- ❌ Prototypes or proof-of-concepts
- ❌ Applications with simple business rules

## Common Patterns

### 1. Aggregate Pattern
```php
class Order // Aggregate Root
{
    private OrderId $id;
    private array $items = []; // Entities within aggregate
    
    public function addItem(Product $product, int $quantity): void
    {
        $item = new OrderItem($this->id, $product, $quantity);
        $this->items[] = $item;
    }
    
    public function getItems(): array
    {
        return $this->items; // Return copy, not reference
    }
}
```

### 2. Repository Pattern
```php
interface UserRepositoryInterface
{
    public function findById(UserId $id): ?User;
    public function save(User $user): void;
    public function delete(UserId $id): void;
}

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(UserId $id): ?User
    {
        $userModel = UserModel::find($id->value());
        return $userModel ? $this->toDomainEntity($userModel) : null;
    }
    
    private function toDomainEntity(UserModel $model): User
    {
        return new User(
            new UserId($model->id),
            new Email($model->email),
            new Name($model->name)
        );
    }
}
```

### 3. Domain Event Pattern
```php
class User
{
    private array $domainEvents = [];
    
    public function changeEmail(Email $newEmail): void
    {
        $oldEmail = $this->email;
        $this->email = $newEmail;
        
        $this->addDomainEvent(new UserEmailChanged($this, $oldEmail, $newEmail));
    }
    
    private function addDomainEvent(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }
    
    public function getDomainEvents(): array
    {
        return $this->domainEvents;
    }
    
    public function clearDomainEvents(): void
    {
        $this->domainEvents = [];
    }
}
```

### 4. Specification Pattern
```php
interface SpecificationInterface
{
    public function isSatisfiedBy($candidate): bool;
    public function and(SpecificationInterface $spec): SpecificationInterface;
    public function or(SpecificationInterface $spec): SpecificationInterface;
    public function not(): SpecificationInterface;
}

class CompositeSpecification implements SpecificationInterface
{
    public function and(SpecificationInterface $spec): SpecificationInterface
    {
        return new AndSpecification($this, $spec);
    }
    
    public function or(SpecificationInterface $spec): SpecificationInterface
    {
        return new OrSpecification($this, $spec);
    }
    
    public function not(): SpecificationInterface
    {
        return new NotSpecification($this);
    }
}
```

## FAQ - Frequently Asked Questions

### Q1: What exactly are Value Objects?

**A:** Value Objects are objects that are defined entirely by their attributes and have no identity. They represent concepts in your domain that are important but don't need to be tracked individually.

**Key Characteristics:**
- **No Identity**: Two value objects with the same attributes are considered equal
- **Immutable**: Once created, they cannot be changed
- **Self-Validating**: They contain their own validation logic
- **Composable**: Can be combined to form more complex value objects

**Examples:**
```php
// ✅ Good Value Objects
class Email
{
    public function __construct(private string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException();
        }
    }
}

class Money
{
    public function __construct(
        private float $amount,
        private Currency $currency
    ) {}
    
    public function add(Money $other): Money
    {
        if (!$this->currency->equals($other->currency)) {
            throw new CurrencyMismatchException();
        }
        return new Money($this->amount + $other->amount, $this->currency);
    }
}

// ❌ Not Value Objects (these are entities)
class User { } // Has identity
class Order { } // Has identity
```

### Q2: Is it necessary to add all properties as Value Objects?

**A:** No, not all properties need to be Value Objects. Use Value Objects when:

**✅ Use Value Objects for:**
- Properties with business rules or validation
- Properties that represent domain concepts
- Properties that are used together frequently
- Properties that need to be immutable

**❌ Don't use Value Objects for:**
- Simple primitive values without business logic
- Properties that are just data containers
- Properties that change frequently
- Properties without domain significance

**Examples:**

```php
class User
{
    // ✅ Good Value Objects - have business rules
    private Email $email;           // Email validation
    private Password $password;     // Password strength rules
    private Money $salary;          // Currency and amount logic
    
    // ✅ Simple primitives - no business rules needed
    private string $firstName;      // Just a string
    private int $age;              // Just a number
    private bool $isActive;        // Just a boolean
    
    // ✅ Complex Value Object - multiple related properties
    private Address $address;      // Street, city, postal code together
    
    // ❌ Over-engineering - simple string doesn't need Value Object
    // private FirstName $firstName; // Too much ceremony for simple string
}
```

### Q3: When should I create a new Bounded Context?

**A:** Create a new Bounded Context when:

**✅ Create New Context When:**
- Different teams work on different areas
- Different domain experts understand different parts
- Different business rules apply
- Different data models are needed
- Different languages/terminology are used

**Examples:**
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   User Context  │    │  Order Context │    │Payment Context │
│                 │    │                │    │                │
│ - Registration  │    │ - Order Items  │    │ - Transactions │
│ - Authentication│    │ - Pricing      │    │ - Refunds      │
│ - Profile Mgmt  │    │ - Shipping     │    │ - Billing      │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

**❌ Don't Create New Context When:**
- Everything is tightly coupled
- Same team works on everything
- Same business rules apply
- Same data model works fine

### Q4: How do I decide between Entity and Value Object?

**A:** Use this decision tree:

```
Does the object have a unique identity?
├─ YES → Entity
│   ├─ Does it change over time? → YES → Entity
│   └─ Does it need to be tracked? → YES → Entity
└─ NO → Value Object
    ├─ Is it defined by its attributes? → YES → Value Object
    ├─ Should it be immutable? → YES → Value Object
    └─ Does it have business rules? → YES → Value Object
```

**Examples:**

```php
// ✅ Entity - has identity, changes over time
class User
{
    private UserId $id;        // Unique identity
    private Email $email;      // Can change
    private Name $name;        // Can change
}

// ✅ Value Object - no identity, immutable
class Email
{
    private string $value;    // Defined by value
    // Cannot change - immutable
}

// ✅ Entity - aggregate root
class Order
{
    private OrderId $id;      // Unique identity
    private array $items;     // Can add/remove items
}

// ✅ Value Object - part of aggregate
class OrderItem
{
    private ProductId $productId;
    private int $quantity;
    private Money $price;
    // Immutable once created
}
```

### Q5: How many Domain Services should I have?

**A:** Use Domain Services sparingly. Follow these guidelines:

**✅ Create Domain Service When:**
- Logic doesn't belong to any single entity
- Logic involves multiple entities
- Logic is complex business rules
- Logic is stateless

**❌ Don't Create Domain Service When:**
- Logic belongs to a single entity
- Logic is simple CRUD operations
- Logic is infrastructure concerns

**Examples:**

```php
// ✅ Good Domain Service - complex business logic
class OrderPricingService
{
    public function calculateOrderTotal(Order $order, Customer $customer): Money
    {
        $subtotal = $order->calculateSubtotal();
        $discount = $this->calculateDiscount($customer, $subtotal);
        $tax = $this->calculateTax($subtotal, $customer->getAddress());
        return $subtotal->subtract($discount)->add($tax);
    }
}

// ❌ Bad Domain Service - simple CRUD
class UserService
{
    public function saveUser(User $user): void
    {
        $this->userRepository->save($user); // This belongs in repository
    }
}

// ✅ Better - put logic in entity
class User
{
    public function changeEmail(Email $newEmail): void
    {
        if (!$this->isEmailVerified()) {
            throw new EmailNotVerifiedException();
        }
        $this->email = $newEmail;
    }
}
```

### Q6: How do I handle database relationships in DDD?

**A:** Focus on domain relationships, not database relationships:

**✅ Domain-First Approach:**
```php
class Order // Aggregate Root
{
    private OrderId $id;
    private CustomerId $customerId; // Reference, not object
    private array $items = [];      // Entities within aggregate
    
    public function addItem(ProductId $productId, int $quantity): void
    {
        // Business logic here
        $item = new OrderItem($this->id, $productId, $quantity);
        $this->items[] = $item;
    }
}

class OrderItem // Entity within aggregate
{
    private OrderId $orderId;
    private ProductId $productId;
    private int $quantity;
}
```

**❌ Database-First Approach:**
```php
class Order
{
    private Customer $customer;     // Loading entire customer
    private Collection $items;      // Loading all items
    // This creates tight coupling and performance issues
}
```

### Q7: How do I handle validation in DDD?

**A:** Validation should happen at multiple levels:

**✅ Multi-Level Validation:**

```php
// 1. Value Object validation
class Email
{
    public function __construct(private string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException();
        }
    }
}

// 2. Entity validation
class User
{
    public function changeEmail(Email $newEmail): void
    {
        if (!$this->isEmailVerified()) {
            throw new EmailNotVerifiedException();
        }
        $this->email = $newEmail;
    }
}

// 3. Domain Service validation
class UserRegistrationService
{
    public function registerUser(RegisterUserCommand $command): User
    {
        if ($this->userRepository->emailExists($command->email)) {
            throw new EmailAlreadyTakenException();
        }
        // Create user...
    }
}

// 4. Application layer validation (Laravel Form Requests)
class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users',
            'name' => 'required|string|max:255',
            'password' => 'required|min:8',
        ];
    }
}
```

### Q8: How do I handle errors and exceptions in DDD?

**A:** Use domain-specific exceptions that represent business problems:

**✅ Domain Exceptions:**

```php
// Base domain exception
abstract class DomainException extends Exception
{
    protected string $errorCode;
    protected array $context;
    
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}

// Specific domain exceptions
class EmailAlreadyTakenException extends DomainException
{
    protected string $errorCode = 'EMAIL_ALREADY_TAKEN';
    
    public function __construct(string $email)
    {
        parent::__construct("Email {$email} is already taken");
        $this->context = ['email' => $email];
    }
}

class InsufficientFundsException extends DomainException
{
    protected string $errorCode = 'INSUFFICIENT_FUNDS';
    
    public function __construct(Money $required, Money $available)
    {
        parent::__construct("Insufficient funds");
        $this->context = [
            'required' => $required->toArray(),
            'available' => $available->toArray()
        ];
    }
}
```

### Q9: How do I test DDD code?

**A:** Test at different levels with different strategies:

**✅ Testing Strategy:**

```php
// 1. Value Object Tests
class EmailTest extends TestCase
{
    public function test_valid_email_creates_value_object(): void
    {
        $email = new Email('test@example.com');
        $this->assertEquals('test@example.com', $email->value());
    }
    
    public function test_invalid_email_throws_exception(): void
    {
        $this->expectException(InvalidEmailException::class);
        new Email('invalid-email');
    }
}

// 2. Entity Tests
class UserTest extends TestCase
{
    public function test_user_can_change_email_when_verified(): void
    {
        $user = new User(UserId::generate(), new Email('old@example.com'), new Name('John'));
        $user->verifyEmail();
        
        $user->changeEmail(new Email('new@example.com'));
        
        $this->assertEquals('new@example.com', $user->getEmail()->value());
    }
    
    public function test_user_cannot_change_email_when_not_verified(): void
    {
        $user = new User(UserId::generate(), new Email('old@example.com'), new Name('John'));
        
        $this->expectException(EmailNotVerifiedException::class);
        $user->changeEmail(new Email('new@example.com'));
    }
}

// 3. Domain Service Tests
class UserRegistrationServiceTest extends TestCase
{
    public function test_registration_creates_user_and_dispatches_event(): void
    {
        Event::fake();
        $repository = Mockery::mock(UserRepositoryInterface::class);
        $service = new UserRegistrationService($repository, $this->passwordHasher, $this->eventDispatcher);
        
        $repository->shouldReceive('emailExists')->andReturn(false);
        $repository->shouldReceive('save')->once();
        
        $command = new RegisterUserCommand('test@example.com', 'John Doe', 'password123');
        $user = $service->registerUser($command);
        
        Event::assertDispatched(UserRegistered::class);
        $this->assertInstanceOf(User::class, $user);
    }
}
```

### Q10: How do I migrate from an existing application to DDD?

**A:** Use a gradual migration strategy:

**✅ Migration Strategy:**

```php
// Phase 1: Extract Value Objects
// Before
class User
{
    public string $email;
    public string $password;
}

// After
class User
{
    public Email $email;      // Extract to Value Object
    public Password $password; // Extract to Value Object
}

// Phase 2: Extract Domain Services
// Before
class UserController
{
    public function register(Request $request)
    {
        // All logic in controller
        if (User::where('email', $request->email)->exists()) {
            return response()->json(['error' => 'Email exists'], 422);
        }
        $user = User::create($request->all());
        Mail::to($user->email)->send(new WelcomeEmail($user));
        return response()->json($user);
    }
}

// After
class UserController
{
    public function register(RegisterRequest $request)
    {
        $command = new RegisterUserCommand(
            new Email($request->email),
            new Name($request->name),
            new Password($request->password)
        );
        
        $user = $this->userRegistrationService->registerUser($command);
        return response()->json($user);
    }
}

// Phase 3: Extract Repositories
// Before
class UserService
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}

// After
class UserService
{
    public function findByEmail(Email $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }
}
```

### Q11: How do I handle performance in DDD?

**A:** Use appropriate patterns for performance:

**✅ Performance Strategies:**

```php
// 1. Lazy Loading
class Order
{
    private ?Customer $customer = null;
    
    public function getCustomer(): Customer
    {
        if ($this->customer === null) {
            $this->customer = $this->customerRepository->findById($this->customerId);
        }
        return $this->customer;
    }
}

// 2. Read Models (CQRS)
class UserReadModel
{
    public int $id;
    public string $email;
    public string $name;
    public string $status;
    // Optimized for reading, not domain logic
}

// 3. Specification Pattern for Complex Queries
class ActiveUsersSpecification implements SpecificationInterface
{
    public function isSatisfiedBy(User $user): bool
    {
        return $user->getStatus() === UserStatus::ACTIVE;
    }
}

// Usage in repository
$activeUsers = $this->userRepository->findBySpecification(
    new ActiveUsersSpecification()
);
```

### Q12: How do I handle legacy code integration?

**A:** Use Anti-Corruption Layer pattern:

**✅ Anti-Corruption Layer:**

```php
// Legacy system interface
class LegacyUserService
{
    public function getUserData(int $legacyId): array
    {
        // Calls legacy system
        return $this->legacyApi->getUser($legacyId);
    }
}

// Anti-corruption layer
class UserAdapter
{
    public function __construct(private LegacyUserService $legacyService) {}
    
    public function findUser(UserId $id): ?User
    {
        $legacyData = $this->legacyService->getUserData($id->value());
        
        if (!$legacyData) {
            return null;
        }
        
        // Convert legacy data to domain model
        return new User(
            $id,
            new Email($legacyData['email_address']),
            new Name($legacyData['full_name'])
        );
    }
}

// Domain repository uses adapter
class UserRepository implements UserRepositoryInterface
{
    public function __construct(private UserAdapter $adapter) {}
    
    public function findById(UserId $id): ?User
    {
        return $this->adapter->findUser($id);
    }
}
```

## Tools and Resources

### Books
- **"Domain-Driven Design" by Eric Evans** - The original DDD book
- **"Implementing Domain-Driven Design" by Vaughn Vernon** - Practical implementation guide
- **"Domain-Driven Design Distilled" by Vaughn Vernon** - Concise overview

### Online Resources
- [Domain-Driven Design Community](https://www.domainlanguage.com/)
- [DDD Reference](https://www.domainlanguage.com/ddd/reference/)
- [Martin Fowler's DDD Articles](https://martinfowler.com/tags/domain%20driven%20design.html)

### Tools
- **EventStorming** - Collaborative modeling technique
- **Bounded Context Canvas** - Tool for defining bounded contexts
- **Domain Storytelling** - Technique for understanding domain processes

### PHP-Specific Resources
- **Laravel DDD Packages**:
  - `spatie/laravel-event-sourcing`
  - `spatie/laravel-event-projector`
  - `beyondcode/laravel-prose`

## Conclusion

Domain-Driven Design is a powerful approach for complex business applications that provides:

- **Clear business alignment**
- **Maintainable architecture**
- **Testable code**
- **Scalable design**

However, it requires:
- **Domain expertise**
- **Team commitment**
- **Initial investment**
- **Ongoing discipline**

The key to successful DDD implementation is to start small, focus on the core domain, and gradually expand as the team becomes more comfortable with the concepts. Remember that DDD is not a silver bullet - it's a tool that should be used when the complexity of the domain justifies the additional structure and ceremony.

Your current Laravel implementation shows good DDD practices with proper domain separation, repository patterns, domain events, and value objects. Continue building on this foundation to create a robust, maintainable, and business-aligned application.
