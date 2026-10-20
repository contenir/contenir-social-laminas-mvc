<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\Tests\Unit\Factory;

use Contenir\Social\Laminas\Mvc\Factory\ConfigReader;
use Contenir\Social\Laminas\Mvc\Tests\TestAsset\ArrayContainer;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class ConfigReaderTest extends TestCase
{
    #[Test]
    public function hasNoSettingsOrOptionsWithoutConfig(): void
    {
        $reader = ConfigReader::from(new ArrayContainer(['config' => 'none']));

        static::assertSame([[], 'social'], [$reader->settings(), $reader->options()->block]);
    }

    #[Test]
    public function ignoresConfigOfTheWrongShape(): void
    {
        $reader = ConfigReader::from(new ArrayContainer([
            'config' => [
                'settings'        => 'none',
                'contenir_social' => ['options' => 'none'],
            ],
        ]));

        static::assertSame([[], 'social'], [$reader->settings(), $reader->options()->block]);
    }

    #[Test]
    public function ignoresSocialConfigThatIsNotAnArray(): void
    {
        $reader = ConfigReader::from(new ArrayContainer(['config' => ['contenir_social' => 'none']]));

        static::assertSame('social', $reader->options()->block);
    }

    #[Test]
    public function readsTheDefaultOptions(): void
    {
        $reader = ConfigReader::from(new ArrayContainer([
            'config' => [
                'contenir_social' => ['options' => ['block' => 'footer']],
            ],
        ]));

        static::assertSame('footer', $reader->options()->block);
    }

    #[Test]
    public function readsTheSitesSettings(): void
    {
        $reader = ConfigReader::from(new ArrayContainer(['config' => ['settings' => ['social' => []]]]));

        static::assertSame(['social' => []], $reader->settings());
    }
}
