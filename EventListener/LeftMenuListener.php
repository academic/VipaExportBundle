<?php

namespace Vipa\ExportBundle\EventListener;

use Vipa\CoreBundle\Acl\AuthorizationChecker;
use Vipa\JournalBundle\Event\MenuEvent;
use Vipa\JournalBundle\Event\MenuEvents;
use Vipa\JournalBundle\Service\JournalService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LeftMenuListener implements EventSubscriberInterface
{
    /**
     * @var  AuthorizationChecker
     */
    private $checker;

    /**
     * @var  JournalService
     */
    private $journalService;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * LeftMenuListener constructor.
     * @param AuthorizationChecker $checker
     * @param JournalService $journalService
     */
    public function __construct(AuthorizationChecker $checker, JournalService $journalService, TokenStorageInterface $tokenStorage)
    {
        $this->checker          = $checker;
        $this->journalService   = $journalService;
        $this->tokenStorage     = $tokenStorage;
    }


    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            MenuEvents::LEFT_MENU_INITIALIZED => 'onLeftMenuInitialized',
        );
    }

    /**
     * @param MenuEvent $menuEvent
     */
    public function onLeftMenuInitialized(MenuEvent $menuEvent)
    {
        $journal = $this->journalService->getSelectedJournal();
        if ($this->checker->isGranted('EDIT', $journal)) {
            $menuItem = $menuEvent->getMenuItem();
            $menuItem->addChild(
                'title.export',
                [
                    'route' => 'vipa_data_export',
                    'extras' => [
                        'icon' => 'download'
                    ],
                    'routeParameters' => [
                        'journalId' => $journal->getId()
                    ],
                    'attributes' => [
                        'class' => 'li-separator'
                    ],
                ]
            );
        }
    }

}
