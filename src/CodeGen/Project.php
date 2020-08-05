<?php

declare(strict_types=1);
/**
 * @author Persi.Liao
 * @email xiangchu.liao@gmail.com
 * @link https://www.github.com/persiliao
 */

namespace PersiLiao\Utils\CodeGen;

use PersiLiao\Utils\Composer;
use PersiLiao\Utils\Str;

/**
 * Read composer.json autoload psr-4 rules to figure out the namespace or path.
 */
class Project
{
    public function className(string $path): string
    {
        return $this->namespace($path);
    }

    public function namespace(string $path): string
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        if($ext !== ''){
            $path = substr($path, 0, -(strlen($ext) + 1));
        }else{
            $path = trim($path, '/') . '/';
        }

        foreach($this->getAutoloadRules() as $prefix => $prefixPath){
            if($this->isRootNamespace($prefix) || strpos($path, $prefixPath) === 0){
                return $prefix . str_replace('/', '\\', substr($path, strlen($prefixPath)));
            }
        }
        throw new \RuntimeException("Invalid project path: {$path}");
    }

    protected function getAutoloadRules(): array
    {
        return data_get(Composer::getJsonContent(), 'autoload.psr-4', []);
    }

    protected function isRootNamespace(string $namespace): bool
    {
        return $namespace === '';
    }

    public function path(string $name, $extension = '.php'): string
    {
        if(Str::endsWith($name, '\\')){
            $extension = '';
        }

        foreach($this->getAutoloadRules() as $prefix => $prefixPath){
            if($this->isRootNamespace($prefix) || strpos($name, $prefix) === 0){
                return $prefixPath . str_replace('\\', '/', substr($name, strlen($prefix))) . $extension;
            }
        }

        throw new \RuntimeException("Invalid class name: {$name}");
    }
}
