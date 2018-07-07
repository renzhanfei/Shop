<?php
/**
 * Created by PhpStorm.
 * User: minhao
 * Date: 2015-09-28
 * Time: 19:18
 */

namespace Oasis\Mlib\UnitTesting;

use Oasis\Mlib\Event\Event;
use Oasis\Mlib\Event\EventDispatcherInterface;
use Oasis\Mlib\Event\EventDispatcherTrait;

class DummyEventDispatcher implements EventDispatcherInterface
{
    use EventDispatcherTrait;
}

class EventTest extends \PHPUnit_Framework_TestCase
{
    /** @var EventDispatcherInterface */
    protected $dummy_dispatcher;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $mocked_subscriber;

    protected function setUp()
    {
        $this->dummy_dispatcher  = new DummyEventDispatcher();
        $this->mocked_subscriber = $this->getMockBuilder("stdClass")
                                        ->setMethods([
                                                         'func',
                                                     ])
                                        ->getMock();
    }

    public function testDispatch()
    {
        $this->mocked_subscriber->expects($this->once())
                                ->method('func')
                                ->with($this->isInstanceOf("Oasis\\Mlib\\Event\\Event"));

        $this->dummy_dispatcher->addEventListener('visit', [$this->mocked_subscriber, 'func']);
        $this->dummy_dispatcher->dispatch(new Event('visit'));
    }

    public function testDispatchString()
    {
        $this->mocked_subscriber->expects($this->once())
                                ->method('func')
                                ->with($this->isInstanceOf("Oasis\\Mlib\\Event\\Event"));

        $this->dummy_dispatcher->addEventListener('visit', [$this->mocked_subscriber, 'func']);
        $this->dummy_dispatcher->dispatch('visit');
    }

    public function testRemoveListener()
    {
        $this->mocked_subscriber->expects($this->never())
                                ->method('func')
                                ->with($this->isInstanceOf("Oasis\\Mlib\\Event\\Event"));

        $this->dummy_dispatcher->addEventListener('visit', [$this->mocked_subscriber, 'func']);
        $this->dummy_dispatcher->removeEventListener('visit', [$this->mocked_subscriber, 'func']);
        $this->dummy_dispatcher->dispatch('visit');
    }

    public function testParent()
    {
        $parent = new DummyEventDispatcher();
        $this->dummy_dispatcher->setParentEventDispatcher($parent);

        $this->mocked_subscriber->expects($this->once())
                                ->method('func')
                                ->with($this->isInstanceOf("Oasis\\Mlib\\Event\\Event"));

        $parent->addEventListener('visit', [$this->mocked_subscriber, 'func']);
        $this->dummy_dispatcher->dispatch('visit');
    }

    public function testEventCapturingInsteadOfBubbling()
    {
        $parent = new DummyEventDispatcher();
        $this->dummy_dispatcher->setParentEventDispatcher($parent);

        $this->mocked_subscriber->expects($this->exactly(2))
                                ->method('func')
                                ->with($this->isInstanceOf("Oasis\\Mlib\\Event\\Event"));

        $this->dummy_dispatcher->addEventListener('visit', [$this->mocked_subscriber, 'func']);
        $parent->addEventListener('visit', [$this->mocked_subscriber, 'func']);
        $this->dummy_dispatcher->dispatch(new Event('visit', null, false));
    }

    public function testParentWhenStoppedInChild()
    {
        $parent = new DummyEventDispatcher();
        $this->dummy_dispatcher->setParentEventDispatcher($parent);

        $this->mocked_subscriber->expects($this->never())
                                ->method('func')
                                ->with($this->isInstanceOf("Oasis\\Mlib\\Event\\Event"));

        $parent->addEventListener('visit', [$this->mocked_subscriber, 'func']);
        $this->dummy_dispatcher->addEventListener(
            'visit',
            function (Event $e) {
                $e->stopPropogation();
            });
        $this->dummy_dispatcher->dispatch('visit');
    }

    public function testWhenImmdediatelyStoppedInChild()
    {
        $this->mocked_subscriber->expects($this->never())
                                ->method('func')
                                ->with($this->isInstanceOf("Oasis\\Mlib\\Event\\Event"));

        $this->dummy_dispatcher->addEventListener('visit', [$this->mocked_subscriber, 'func']);
        $this->dummy_dispatcher->addEventListener(
            'visit',
            function (Event $e) {
                $e->stopImmediatePropogation();
            },
            -1);
        $this->dummy_dispatcher->dispatch('visit');
    }
}
