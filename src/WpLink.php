<?php

namespace WebTheory\WpLink;

use UnexpectedValueException;
use WebTheory\WpCliUtil\WpCliUtil;

class WpLink
{
    protected string $root;

    protected array $typePaths = [
        'wordpress-muplugin' => 'mu-plugins',
        'wordpress-plugin' => 'plugins',
        'wordpress-theme' => 'themes',
    ];

    public function __construct(string $root)
    {
        $this->root = $root;
    }

    public function makeLink(string $name, string $type): void
    {
        if (!isset($this->typePaths[$type])) {
            throw new UnexpectedValueException("{$type} not a valid type.");
        }

        $root = $this->root;
        $wordpress = WpCliUtil::getInstallPath() ?? 'wordpress';
        $dirname = $this->typePaths[$type];
        $destination = "$root/$wordpress/wp-content/$dirname";

        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }

        $link = preg_replace(
            '/\/+/',
            DIRECTORY_SEPARATOR,
            "{$destination}/{$name}"
        );

        shell_exec("rm -f $link");
        symlink($root, $link);
    }
}
