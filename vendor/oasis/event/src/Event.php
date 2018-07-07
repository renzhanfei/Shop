<?php
/**
 * Created by PhpStorm.
 * User: minhao
 * Date: 2015-09-28
 * Time: 18:25
 */

namespace Oasis\Mlib\Event;

class Event
{
    /** @var EventDispatcherInterface */
    protected $target;
    /** @var EventDispatcherInterface */
    protected $currentTarget;

    protected $name;
    protected $context     = null;
    protected $bubbles     = true;
    protected $cancellable = true;
    protected $cancelled   = false;

    protected $propogationStopped            = false;
    protected $propogationStoppedImmediately = false;

    /**
     * Create an Event object
     *
     * @param string $name        name of the Event
     * @param mixed  $context     context of the Event
     * @param bool   $bubbles     whether the Event should bubble (to parent dispatcher)
     * @param bool   $cancellable is the Event cancellable
     */
    function __construct($name, $context = null, $bubbles = true, $cancellable = true)
    {
        $this->name        = $name;
        $this->context     = $context;
        $this->bubbles     = $bubbles;
        $this->cancellable = $cancellable;
    }

    public function stopImmediatePropogation()
    {
        $this->propogationStopped =
        $this->propogationStoppedImmediately = true;
    }

    public function stopPropogation()
    {
        $this->propogationStopped = true;
    }

    public function cancel()
    {
        if (!$this->cancellable) {
            throw new \LogicException("Cancelling an event which is not cancellable!");
        }

        if (!$this->cancelled) {
            $this->cancelled = true;
        }
    }

    /**
     * alias of Event::cancel()
     */
    public function preventDefault()
    {
        $this->cancel();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return boolean
     */
    public function doesBubble()
    {
        return $this->bubbles;
    }

    /**
     * @return boolean
     */
    public function isPropogationStopped()
    {
        return $this->propogationStopped;
    }

    /**
     * @return boolean
     */
    public function isPropogationStoppedImmediately()
    {
        return $this->propogationStoppedImmediately;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param EventDispatcherInterface $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getCurrentTarget()
    {
        return $this->currentTarget;
    }

    /**
     * @param EventDispatcherInterface $currentTarget
     */
    public function setCurrentTarget($currentTarget)
    {
        $this->currentTarget = $currentTarget;
    }

    /**
     * @return boolean
     */
    public function isCancelled()
    {
        return $this->cancelled;
    }

    /**
     * @param mixed|null $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

}
