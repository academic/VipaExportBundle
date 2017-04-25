<?php

namespace Vipa\ExportBundle\Service\Twig;

use Symfony\Component\Routing\RouterInterface;

class ExportTwigExtension extends \Twig_Extension
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('doajLanguageTransform', array($this, 'doajLanguageTransform')),
        );
    }

    /**
     * @param $locale
     * @return string
     */
    public function doajLanguageTransform($locale)
    {
        $transformArray = [
            'tr' => 'tur',
            'en' => 'eng',
            'fr' => 'fre',
            'de' => 'ger',
        ];
        return $transformArray[$locale];
    }

    public function getName()
    {
        return 'export_twig_extension';
    }
}
