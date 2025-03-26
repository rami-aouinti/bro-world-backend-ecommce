<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PhpSpecToPHPUnit\Set\MigrationSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeDeclarationRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationRector;
use Rector\Visibility\Rector\ClassMethod\ExplicitPublicClassMethodRector;
use Sylius\Component\Addressing\Model\Zone;

return static function (RectorConfig $config): void
{
    $config->paths([
        __DIR__ . '/src/Sylius/Component/Addressing/spec',
    ]);

    $config->importNames();
    $config->removeUnusedImports();

    $config->sets([
        LevelSetList::UP_TO_PHP_82,
        MigrationSetList::PHPSPEC_TO_PHPUNIT,
    ]);
    $config->rules([
        AddParamTypeDeclarationRector::class,
        AddReturnTypeDeclarationRector::class,
        ExplicitPublicClassMethodRector::class,
    ]);

    // After executing:
    // vendor/bin/rector process -c rector-spec-unit.php
    //
    // Some remaining adjustments are required (MacOS sed, other can simply use "sed -i" not "sed -i ''"):
    //
    // # Fix MockObject phpdoc
    // find src/Sylius/path_to_spec_folder/spec/ -type f -name "*Spec.php" -exec sed -i '' "s/\|MockObject /\&MockObject /g" {} +
    // # Fix shouldImplement to assertInstanceOf
    // find src/Sylius/path_to_tests_folder/spec/ -type f -name "*Spec.php" -exec sed -i '' 's/(\$this->[^\-]+)->shouldImplement\(([^)]+)\);/\self::assertInstanceOf(\2, \1);/g' {} +
    // # Fix $this->assert*( to self::assert*(
    // find src/Sylius/path_to_tests_folder/spec/ -type f -name "*Spec.php" -exec sed -i '' -E 's/\$this->assert([^\(]+)\(/self::assert\1(/g' {} +
    // vendor/bin/phpspec-to-phpunit rename-suffix src/Sylius/path_to_spec_folder/spec/
    // vendor/bin/ecs check src/Sylius/path_to_spec_folder/spec/ --fix
    // mv src/Sylius/path_to_spec_folder/spec/ src/Sylius/path_to_spec_folder/Tests/
    //
    // Finally, you will have to check for non setup 'setUp' method and add it manually to instantiate the missing context property.
    // Ex add this:
    //
    //    private TheCurrentTestedClass $currentClass;
    //
    //    protected function setUp(): void
    //    {
    //        $this->currentClass = new TheCurrentTestedClass();
    //    }
};
