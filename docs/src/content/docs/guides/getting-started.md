---
title: Getting Started
description: Complete guide to setting up and extending the Laravel Starter Kit
---

This guide will walk you through setting up a new project using the Laravel Starter Kit and provide
recommendations for extending it to fit your application-specific needs.

## Installation & Initial Setup

### Clone and Setup

Follow the installation steps from the [README](https://github.com/mindtwo/laravel-starter-kit):

```bash
git clone git@github.com:mindtwo/laravel-starter-kit.git your-project-name
cd your-project-name
just --list # Check out available tasks
just setup
npm i
npm run build
```

Your application should now be running at `https://your-project-name.test`.

## Post-Setup Configuration

After the initial setup, customize these configurations for your specific project:

### Security Headers

**Important:** The `secure-headers` configuration must be customized for every project. For the most
part, this will be updated and extended as new requirements or dependencies come in.

Edit `config/secure-headers.php`:

```php
// Adjust CSP directives based on your external resources
'content-security-policy' => [
    'script-src' => [
        'self',
        // Add your CDN domains, analytics providers, etc.
        // 'https://cdn.example.com',
        // 'https://www.googletagmanager.com',
    ],
    'style-src' => [
        'self',
        'unsafe-inline', // Required for Tailwind (consider removing in production)
        // Add your font/style CDNs
    ],
    'img-src' => [
        'self',
        'data:',
        // Add your image CDNs
    ],
    // ... customize other directives
],
```

## Architecture & Coding Patterns

This starter kit encourages clean, maintainable code through separation of concerns and explicit
design patterns.

### Core Principles

1. **Keep Controllers Slim**: Controllers should only handle HTTP concerns (pass through request
   data, handle responses). Validation belongs in form request classes.
2. **Keep Models Slim**: Models should define relationships, casts, and simple scopes
3. **Extract Business Logic**: Application logic belongs in service classes
4. **Avoid Magic**: Prefer dependency injection over facades; be explicit
5. **Follow Laravel Conventions**: Use events, listeners, jobs, mails, policies, observers

### Service Classes

Services contain your application's business logic and should be the primary place where
functionality is implemented.

**Example Structure:**

```php
namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private OrderRepository $orders,
        private PaymentService $payments,
        private NotificationService $notifications,
    ) {}

    public function createOrder(array $items, int $userId): Order
    {
        return DB::transaction(function () use ($items, $userId) {
            $order = Order::query()->create([
                'user_id' => $userId,
                'status' => 'pending',
            ]);

            // Attach items
            foreach ($items as $item) {
                $order->items()->create($item);
            }

            // Calculate total
            $order->update(['total' => $order->items->sum('subtotal')]);

            if ($this->orders->hasQualifiedForRaffle($userId)) {
                $this->createRaffleParticipation($userId);
            }

            $this->payments->process($order);
            $this->notifications->sendOrderConfirmation($order);

            return $order;
        });
    }
}
```

**Controller Usage:**

```php
class OrderController extends Controller
{
    public function __construct(
        private OrderService $orders,
    ) {}

    public function store(CreateOrderRequest $request): RedirectResponse
    {
        $order = $this->orders->createOrder(
            items: $request->validated('items'),
            userId: $request->user()->id
        );

        flash()->success('Order created successfully');

        return redirect()->route('orders.show', $order);
    }
}
```

### Repository Pattern (Simplified)

**Don't** create repositories for every model. Eloquent already provides this abstraction.

**Do** extract complex or reusable queries into repository classes:

```php
namespace App\Repositories;

use App\Models\Product;
use Chiiya\Common\Repositories\AbstractRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @extends AbstractRepository<Product>
 */
class ProductRepository extends AbstractRepository
{
    protected string $model = Product::class;

    /**
     * Get featured products with their related data
     */
    public function getFeaturedProducts(int $limit = 10): Collection
    {
        return $this->newQuery()
            ->where('is_featured', '=', true)
            ->where('status', '=', 'active')
            ->whereHas('inventory', fn($q) => $q->where('stock', '>', 0))
            ->with(['category', 'images', 'reviews' => fn (HasMany $q) =>
                $q->where('rating', '>=', 4)->latest()->limit(5)
            ])
            ->withAvg('reviews', 'rating')
            ->withCount('orders')
            ->orderByDesc('featured_at')
            ->limit($limit)
            ->get();
    }
}
```

**For simple queries, use Model::query() directly:**

```php
// Controller or Service - no repository needed
$activeProducts = Product::query()->where('status', '=', 'active')->latest()->paginate();
```

### Pipeline Pattern

Use pipelines for multi-step processes where data flows through stages:

```php
namespace App\Pipelines\Order;

use Illuminate\Pipeline\Pipeline;

class OrderProcessingPipeline
{
    public function __construct(
        private Pipeline $pipeline
    ) {}

    public function process(Order $order): Order
    {
        return $this->pipeline
            ->send($order)
            ->through([
                ValidateInventory::class,
                CalculateTaxes::class,
                ApplyDiscounts::class,
                ProcessPayment::class,
                UpdateInventory::class,
                SendNotifications::class,
            ])
            ->thenReturn();
    }
}

// Pipeline Stage Example
class ValidateInventory
{
    public function handle(Order $order, Closure $next): mixed
    {
        foreach ($order->items as $item) {
            if ($item->product->inventory->stock < $item->quantity) {
                throw new InsufficientStockException($item->product);
            }
        }

        return $next($order);
    }
}
```

### Presenter Classes

Extract complex presentation logic from Blade views:

```php
namespace App\Presenters;

use Chiiya\Common\Presenter\Presenter;

/**
 * @extends Presenter<Product>
 */
class ProductPresenter extends Presenter
{
    public function formattedPrice(): string
    {
        return number_format($this->entity->price, 2) . ' €';
    }

    public function discountPercentage(): ?int
    {
        if (! $this->entity->original_price) {
            return null;
        }

        return (int) round(
            (1 - $this->entity->price / $this->entity->original_price) * 100
        );
    }

    public function statusBadgeClass(): string
    {
        return match($this->entity->status) {
            'active' => 'badge-success',
            'draft' => 'badge-warning',
            'archived' => 'badge-secondary',
            default => 'badge-default',
        };
    }

    public function shareUrl(): string
    {
        return route('products.show', [
            'product' => $this->entity->slug,
            'ref' => 'share',
        ]);
    }
}
```

Set up your model:

```php
class Product extends Model
{
    /** @use PresentableTrait<ProductPresenter> */
    use PresentableTrait;

    public string $presenter = ProductPresenter::class;
}
```

**Usage in Blade:**

```blade
<div class="product-card">
  <h3>{{ $product->name }}</h3>
  <p class="price">{{ $product->present()->formattedPrice() }}</p>

  @if ($discount = $product->present()->discountPercentage())
    <span class="discount">-{{ $discount }}%</span>
  @endif

  <span class="{{ $product->present()->statusBadgeClass() }}">
    {{ $product->status }}
  </span>
</div>
```

### Standard Laravel Patterns

Use Laravel's built-in patterns appropriately:

**Events & Listeners:**

```php
// Event
class OrderPlaced
{
    public function __construct(public Order $order) {}
}

// Listener
class SendOrderConfirmation
{
    public function __construct(
        private Mailer $mailer
    ) {}

    public function handle(OrderPlaced $event): void
    {
        $this->mailer->to($event->order->user)->send(
            new OrderConfirmationMail($event->order)
        );
    }
}

// Dispatch in Service
event(new OrderPlaced($order));
```

**Jobs:**

```php
// For async/queued tasks
class ProcessOrderExport implements ShouldQueue
{
    public function __construct(
        public Order $order,
    ) {}

    public function handle(ExportService $service): void
    {
        $service->exportOrder($this->order);
    }
}

// Dispatch
$this->dispatch(new ProcessOrderExport($order));
```

**Observers:**

```php
// For model lifecycle events
class ProductObserver
{
    public function created(Product $product): void
    {
        // Generate slug, create default inventory, etc.
    }

    public function updating(Product $product): void
    {
        // Validate state transitions
    }
}

// Register on the model
#[ObservedBy(ProductObserver::class)]
class Product extends Model {}
```

**Policies:**

```php
// Authorization logic
class ProductPolicy
{
    public function update(User $user, Product $product): bool
    {
        return $user->id === $product->user_id
            || $user->hasRole('admin');
    }
}
```

### Dependency Injection

Always try to use dependency injection instead of facades:

```php
// ❌ Don't
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderService
{
    public function sendConfirmation(Order $order): void
    {
        Mail::to($order->user)->send(new OrderConfirmation($order));
        Log::debug('Mail sent');
    }
}

// ✅ Do
use Illuminate\Contracts\Mail\Mailer;
use Psr\Log\LoggerInterface;

class OrderService
{
    public function __construct(
        private Mailer $mailer,
        private LoggerInterface $logger,
    ) {}

    public function sendConfirmation(Order $order): void
    {
        $this->mailer->to($order->user)->send(new OrderConfirmation($order));
        $this->logger->debug('Mail sent');
    }
}
```

This makes testing easier and dependencies explicit.

### Directory Structure

Organize your code logically:

```
app/
├── Console/
├── Events/
├── Exceptions/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Jobs/
├── Listeners/
├── Mail/
├── Models/
├── Observers/
├── Pipelines/
├── Policies/
├── Presenters/
├── Providers/
├── Repositories/
└── Services/
    ├── Payment/
    ├── Notification/
    └── Export/
```

## Recommended Packages

### SVG Icons with Blade Icons

When using SVG icons, consider implementing them through
[Blade Icons](https://github.com/blade-ui-kit/blade-icons).

**Installation:**

```bash
composer require blade-ui-kit/blade-icons
```

**Using Icon Sets:**

```bash
# Install popular icon sets
composer require mallardduck/blade-lucide-icons
```

**Usage in Blade:**

```blade
<x-lucide-home class="w-5 h-5" />
```

**Custom Icons:**

1. Create a directory: `resources/svg/custom/`
2. Add your SVG files (e.g., `logo.svg`)
3. Register in `config/blade-icons.php`:

```php
'sets' => [
    'custom' => [
        'path' => 'resources/svg/custom',
        'prefix' => 'icon',
    ],
],
```

4. Use in Blade:

```blade
<x-icon-logo class="w-8 h-8" />
```

### Model Auditing

Track all model changes with [Laravel Auditing](https://github.com/owen-it/laravel-auditing).

**Installation:**

```bash
composer require owen-it/laravel-auditing
php artisan vendor:publish --provider "OwenIt\Auditing\AuditingServiceProvider" --tag="migrations"
php artisan migrate
```

**Usage:**

```php
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Product extends Model implements Auditable
{
    use AuditableTrait;

    // Customize which attributes to audit
    protected $auditInclude = ['name', 'price', 'status'];

    // Or exclude specific ones
    protected $auditExclude = ['updated_at'];
}
```

### API Design with Query Builder

Use [Spatie Laravel Query Builder](https://github.com/spatie/laravel-query-builder) for consistent
API filtering, sorting, and including relationships.

**Installation:**

```bash
composer require spatie/laravel-query-builder
```

**Usage:**

```php
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    public function index()
    {
        $products = QueryBuilder::for(Product::class)
            ->allowedFilters(['name', 'category', 'status'])
            ->allowedSorts(['name', 'price', 'created_at'])
            ->allowedIncludes(['category', 'reviews'])
            ->paginate()
            ->appends(request()->query());

        return response()->json($products);
    }
}
```

**API Call:**

```
GET /api/products?filter[status]=active&sort=-price&include=category
```

### Server-Side Analytics Events

Use [Laravel Event Store](https://github.com/antwerpes/laravel-event-store) to pass analytics events
to the frontend.

**Installation:**

```bash
composer require antwerpes/laravel-event-store
```

You must also add the middleware to your web group, at the end of the stack:

```php
protected $middlewareGroups = [
  'web' => [
    ...
    \Antwerpes\LaravelEventStore\Middleware\FlashEventStore::class,
  ],
];
```

**Usage:**

```php
use Antwerpes\LaravelEventStore\Facades\EventStore;

// In your service or controller
EventStore::push('product_viewed', [
    'product_id' => $product->id,
    'category' => $product->category->name,
]);

// In your Blade layout
{!! EventStore::dumpForGTM() !!}
```

### Filesystem Cleanup

Automatically clean temporary files with
[Laravel Directory Cleanup](https://github.com/spatie/laravel-directory-cleanup).

**Installation:**

```bash
composer require spatie/laravel-directory-cleanup
```

**Configuration:**

```php
// config/laravel-directory-cleanup.php
return [
    'directories' => [
        [
            'path' => storage_path('app/temp'),
            'deleteAllOlderThanMinutes' => 60 * 24, // 24 hours
        ],
        [
            'path' => storage_path('app/exports'),
            'deleteAllOlderThanMinutes' => 60 * 24 * 7, // 7 days
        ],
    ],
];
```

**Schedule:**

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('clean:directories')->daily();
}
```

### Sitemap Generation

Generate XML sitemaps with [Laravel Sitemap](https://github.com/spatie/laravel-sitemap).

**Installation:**

```bash
composer require spatie/laravel-sitemap
```

**Usage:**

```php
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

Sitemap::create()
    ->add(Url::create('/')->setPriority(1.0))
    ->add(Url::create('/about')->setPriority(0.8))
    ->add(Product::all()) // Implements Spatie\Sitemap\Contracts\Sitemapable
    ->writeToFile(public_path('sitemap.xml'));
```

**Scheduled Generation:**

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('sitemap:generate')->daily();
}
```

### Additional Recommended Packages

- **[spatie/laravel-permission](https://github.com/spatie/laravel-permission)**: Role and permission
  management
- **[spatie/laravel-activitylog](https://github.com/spatie/laravel-activitylog)**: Log user
  activities
- **[spatie/laravel-backup](https://github.com/spatie/laravel-backup)**: Database and file backups
- **[spatie/laravel-medialibrary](https://github.com/spatie/laravel-medialibrary)**: Associate files
  with models
- **[intervention/image](https://github.com/Intervention/image)**: Image manipulation
- **[league/flysystem-aws-s3-v3](https://github.com/thephpleague/flysystem-aws-s3-v3)**: S3 storage
  driver

## Getting Help

- Review the full [documentation](https://mindtwo.github.io/laravel-starter-kit)
- Check Laravel's official [documentation](https://laravel.com/docs)
- Reach out to the team for architectural questions

Happy coding!
