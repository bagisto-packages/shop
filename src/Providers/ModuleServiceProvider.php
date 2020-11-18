<?php

namespace BagistoPackages\Shop\Providers;

use BagistoPackages\Shop\Tree;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Validator;
use BagistoPackages\Shop\Models\Customer;
use Konekt\Concord\BaseBoxServiceProvider;
use \BagistoPackages\Shop\Models as Models;
use \BagistoPackages\Shop\Facades as Facades;
use BagistoPackages\Ui\ViewRenderEventManager;
use \BagistoPackages\Shop\Observers as Observers;
use BagistoPackages\Shop\View\Compilers\BladeCompiler;
use \BagistoPackages\Shop\Console\Commands as Commands;
use \BagistoPackages\Shop\Http\Middleware as Middleware;

class ModuleServiceProvider extends BaseBoxServiceProvider
{
    protected $models = [
        Models\Channel::class,
        Models\CoreConfig::class,
        Models\Country::class,
        Models\CountryTranslation::class,
        Models\CountryState::class,
        Models\CountryStateTranslation::class,
        Models\Currency::class,
        Models\CurrencyExchangeRate::class,
        Models\Locale::class,
        Models\SubscribersList::class,

        Models\Attribute::class,
        Models\AttributeFamily::class,
        Models\AttributeGroup::class,
        Models\AttributeOption::class,
        Models\AttributeOptionTranslation::class,
        Models\AttributeTranslation::class,

        Models\BookingProduct::class,
        Models\BookingProductDefaultSlot::class,
        Models\BookingProductAppointmentSlot::class,
        Models\BookingProductEventTicket::class,
        Models\BookingProductEventTicketTranslation::class,
        Models\BookingProductRentalSlot::class,
        Models\BookingProductTableSlot::class,
        Models\Booking::class,

        Models\CartRule::class,
        Models\CartRuleTranslation::class,
        Models\CartRuleCustomer::class,
        Models\CartRuleCoupon::class,
        Models\CartRuleCouponUsage::class,

        Models\CatalogRule::class,
        Models\CatalogRuleProduct::class,
        Models\CatalogRuleProductPrice::class,

        Models\Category::class,
        Models\CategoryTranslation::class,

        Models\Cart::class,
        Models\CartAddress::class,
        Models\CartItem::class,
        Models\CartPayment::class,
        Models\CartShippingRate::class,

        Models\Customer::class,
        Models\CustomerCompareProduct::class,
        Models\CustomerAddress::class,
        Models\CustomerGroup::class,
        Models\Wishlist::class,

        Models\Admin::class,
        Models\Role::class,

        Models\InventorySource::class,

        Models\TaxCategory::class,
        Models\TaxMap::class,
        Models\TaxRate::class,

        Models\Product::class,
        Models\ProductAttributeValue::class,
        Models\ProductFlat::class,
        Models\ProductImage::class,
        Models\ProductInventory::class,
        Models\ProductOrderedInventory::class,
        Models\ProductReview::class,
        Models\ProductSalableInventory::class,
        Models\ProductDownloadableSample::class,
        Models\ProductDownloadableLink::class,
        Models\ProductGroupedProduct::class,
        Models\ProductBundleOption::class,
        Models\ProductBundleOptionTranslation::class,
        Models\ProductBundleOptionProduct::class,
        Models\ProductCustomerGroupPrice::class,

        Models\Order::class,
        Models\OrderItem::class,
        Models\DownloadableLinkPurchased::class,
        Models\OrderAddress::class,
        Models\OrderPayment::class,
        Models\OrderComment::class,
        Models\Invoice::class,
        Models\InvoiceItem::class,
        Models\Shipment::class,
        Models\ShipmentItem::class,
        Models\Refund::class,
        Models\RefundItem::class,

        Models\CmsPage::class,
        Models\CmsPageTranslation::class
    ];

    public function boot()
    {
        parent::boot();

        $router = $this->app['router'];

        config()->set('database.connections.mysql.strict', false);

        config()->set('auth.providers', array_merge(config('auth.providers'), [
            'customer' => [
                'driver' => 'eloquent',
                'model' => Customer::class
            ]
        ]));

        config()->set('auth.guards', array_merge(config('auth.guards'), [
            'customer' => [
                'driver' => 'session',
                'provider' => 'customer'
            ]
        ]));

        config()->set('auth.passwords', array_merge(config('auth.passwords'), [
            'customers' => [
                'provider' => 'customer',
                'table' => 'customer_password_resets',
                'expire' => 60,
            ]
        ]));

        $this->app->bind(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \BagistoPackages\Shop\Exceptions\Handler::class
        );

        $router->aliasMiddleware('theme', Middleware\Theme::class);
        $router->aliasMiddleware('admin', Middleware\Bouncer::class);
        $router->aliasMiddleware('locale', Middleware\Locale::class);
        $router->aliasMiddleware('currency', Middleware\Currency::class);
        $router->aliasMiddleware('customer', Middleware\RedirectIfNotCustomer::class);

        Validator::extend('slug', 'BagistoPackages\Shop\Contracts\Validations\Slug@passes');
        Validator::extend('code', 'BagistoPackages\Shop\Contracts\Validations\Code@passes');
        Validator::extend('decimal', 'BagistoPackages\Shop\Contracts\Validations\Decimal@passes');

        Models\ProductProxy::observe(Observers\ProductObserver::class);
        Models\CategoryProxy::observe(Observers\CategoryObserver::class);

        Paginator::defaultView('shop::partials.pagination');
        Paginator::defaultSimpleView('shop::partials.pagination');

        Event::listen('bagisto.shop.layout.body.after', static function (ViewRenderEventManager $viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('shop::blade.tracer.style');
        });

        Event::listen('bagisto.admin.layout.head', static function (ViewRenderEventManager $viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('shop::blade.tracer.style');
        });

        Event::listen('checkout.order.save.after', 'BagistoPackages\Shop\Listeners\Order@afterPlaceOrder');
        Event::listen('checkout.order.save.after', 'BagistoPackages\Shop\Listeners\Order@manageCartRule');
        Event::listen('checkout.cart.collect.totals.before', 'BagistoPackages\Shop\Listeners\Cart@applyCartRules');
        Event::listen('catalog.product.update.after', 'BagistoPackages\Shop\Listeners\Product@createProductRuleIndex');
        Event::listen('catalog.attribute.create.after', 'BagistoPackages\Shop\Listeners\ProductFlat@afterAttributeCreatedUpdated');
        Event::listen('catalog.attribute.update.after', 'BagistoPackages\Shop\Listeners\ProductFlat@afterAttributeCreatedUpdated');
        Event::listen('catalog.attribute.delete.before', 'BagistoPackages\Shop\Listeners\ProductFlat@afterAttributeDeleted');
        Event::listen('catalog.product.create.after', 'BagistoPackages\Shop\Listeners\ProductFlat@afterProductCreatedUpdated');
        Event::listen('catalog.product.update.after', 'BagistoPackages\Shop\Listeners\ProductFlat@afterProductCreatedUpdated');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'shop');

        $this->composeView();

        $this->publishes([
            __DIR__ . '/../Resources/config/imagecache.php' => config_path('imagecache.php'),
            __DIR__ . '/../Resources/config/themes.php' => config_path('themes.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views' => resource_path('views/themes/default'),
            __DIR__ . '/../Resources/lang' => resource_path('lang/vendor/shop'),
        ]);

        $this->publishes([
            __DIR__ . '/../../publishable/assets' => public_path('themes/default/assets'),
        ], 'public');
    }

    public function register()
    {
        $this->registerConfig();

        $this->registerCore();
        $this->registerCart();
        $this->registerBouncer();
        $this->registerPayment();
        $this->registerShipping();

        $this->registerCommands();
        $this->registerBladeCompiler();

        parent::register();
    }

    protected function registerRoutes($routes): void
    {
        $path = __DIR__ . '/../Resources/routes';

        $routeFiles = collect(File::glob($path . '/*.php'))
            ->map(function ($file) {
                return File::name($file);
            })
            ->all();

        foreach ($routeFiles as $file) {
            Route::group([
                'middleware' => ['web'],
                'as' => $this->shortName() . '.',
                'namespace' => sprintf('%s\\%s', $this->getNamespaceRoot(), str_replace('/', '\\', $this->convention->controllersFolder())),
            ], sprintf('%s/%s.php', $path, $file));
        }
    }


    protected function composeView()
    {
        view()->composer('shop::customers.account.partials.sidemenu', function ($view) {
            $tree = Tree::create();

            foreach (config('menu.customer') as $item) {
                $tree->add($item, 'menu');
            }

            $tree->items = core()->sortItems($tree->items);

            $view->with('menu', $tree);
        });
    }

    protected function registerConfig()
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/Resources/config/system.php', 'core');
        $this->mergeConfigFrom(dirname(__DIR__) . '/Resources/config/themes.php', 'themes');
        $this->mergeConfigFrom(dirname(__DIR__) . '/Resources/config/carriers.php', 'carriers');
        $this->mergeConfigFrom(dirname(__DIR__) . '/Resources/config/menu.php', 'menu.customer');
        $this->mergeConfigFrom(dirname(__DIR__) . '/Resources/config/imagecache.php', 'imagecache');
        $this->mergeConfigFrom(dirname(__DIR__) . '/Resources/config/productTypes.php', 'product_types');
        $this->mergeConfigFrom(dirname(__DIR__) . '/Resources/config/paymentMethods.php', 'paymentmethods');
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\Install::class,
                Commands\PriceUpdate::class,
                Commands\BookingCron::class,
                Commands\PriceRuleIndex::class,
                Commands\ExchangeRateUpdate::class,
            ]);
        }
    }

    public function registerBladeCompiler()
    {
        $this->app->singleton('blade.compiler', function ($app) {
            return new BladeCompiler($app['files'], $app['config']['view.compiled']);
        });
    }

    protected function registerCore()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('core', Facades\Core::class);

        $this->app->singleton('core', function () {
            return app()->make(\BagistoPackages\Shop\Core::class);
        });
    }

    protected function registerCart()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('cart', Facades\Cart::class);

        $this->app->singleton('cart', function () {
            return new \BagistoPackages\Shop\Facades\Cart();
        });

        $this->app->bind('cart', \BagistoPackages\Shop\Cart::class);
    }

    protected function registerBouncer()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('Bouncer', Facades\Bouncer::class);

        $this->app->singleton('bouncer', function () {
            return new \BagistoPackages\Shop\Bouncer();
        });
    }

    protected function registerPayment()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('payment', Facades\Payment::class);

        $this->app->singleton('payment', function () {
            return new \BagistoPackages\Shop\Payment();
        });
    }

    protected function registerShipping()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('shipping', Facades\Shipping::class);

        $this->app->singleton('shipping', function () {
            return new \BagistoPackages\Shop\Shipping();
        });
    }
}
