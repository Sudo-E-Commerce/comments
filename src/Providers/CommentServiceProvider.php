<?php
 
namespace Sudo\Comment\Providers;
 
use Illuminate\Support\ServiceProvider;
use File;
class CommentServiceProvider extends ServiceProvider
{
    /**
     * Register config file here
     * alias => path
     */
    private $configFile = [
        'SudoComment' => 'SudoComment.php',
    ];

    /**
     * Register commands file here
     * alias => path
     */
    protected $commands = [
        //
    ];

	/**
     * Register bindings in the container.
     */
    public function register()
    {
        // Đăng ký config cho từng Module
        $this->mergeConfig();
        // boot commands
        $this->commands($this->commands);
    }

	public function boot()
	{
		$this->registerModule();

        $this->publish();
	}

	private function registerModule() {
		$modulePath = __DIR__.'/../../';
        $moduleName = 'Comment';

        // boot route
        if (File::exists($modulePath."routes/routes.php")) {
            $this->loadRoutesFrom($modulePath."/routes/routes.php");
        }

        // boot migration
        if (File::exists($modulePath . "migrations")) {
            $this->loadMigrationsFrom($modulePath . "migrations");
        }

        // boot languages
        if (File::exists($modulePath . "resources/lang")) {
            $this->loadTranslationsFrom($modulePath . "resources/lang", $moduleName);
            $this->loadJSONTranslationsFrom($modulePath . 'resources/lang');
        }

        // boot views
        if (File::exists($modulePath . "resources/views")) {
            $this->loadViewsFrom($modulePath . "resources/views", $moduleName);
        }

	    // boot all helpers
        if (File::exists($modulePath . "helpers")) {
            // get all file in Helpers Folder 
            $helper_dir = File::allFiles($modulePath . "helpers");
            // foreach to require file
            foreach ($helper_dir as $key => $value) {
                $file = $value->getPathName();
                require $file;
            }
        }
	}

    /*
    * publish dự án ra ngoài
    * publish config File
    * publish assets File
    */
    public function publish()
    {
        if ($this->app->runningInConsole()) {
            $assets = [
                __DIR__.'/../../resources/assets' => public_path('platforms/comments'),
            ];
            $config = [
                __DIR__.'/../../config/SudoComment.php' => config_path('SudoComment.php'),
            ];
            $view = [
                __DIR__.'/../../resources/views/list.blade.php' => resource_path('views/packages/comments/list.blade.php'),
                __DIR__.'/../../resources/views/item.blade.php' => resource_path('views/packages/comments/item.blade.php'),
                __DIR__.'/../../resources/views/add.blade.php' => resource_path('views/packages/comments/add.blade.php'),
            ];
            $all = array_merge($assets, $config, $view);
            // Chạy riêng
            $this->publishes($all, 'sudo/comment');
            $this->publishes($assets, 'sudo/comment/assets');
            $this->publishes($config, 'sudo/comment/config');
            $this->publishes($view, 'sudo/comment/view');
            // Khởi chạy chung theo core
            $this->publishes($all, 'sudo/core');
            $this->publishes($assets, 'sudo/core/assets');
            $this->publishes($config, 'sudo/core/config');
            $this->publishes($view, 'sudo/core/view');
        }
    }

    /*
    * Đăng ký config cho từng Module
    * $this->configFile
    */
    public function mergeConfig() {
        foreach ($this->configFile as $alias => $path) {
            $this->mergeConfigFrom(__DIR__ . "/../../config/" . $path, $alias);
        }
    }
}