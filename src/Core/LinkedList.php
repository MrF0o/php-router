<?php

namespace Mrfoo\PHPRouter\Core;


class LinkedList
{
    private ?ListNode $head;

    public function __construct()
    {
        $this->head = null;
    }

    public function add(Route $route): void
    {
        $node = new ListNode($route);

        if ($this->head === null) {
            $this->head = $node;
            return;
        }

        $current = $this->head;

        while ($current->next !== null) {
            $current = $current->next;
        }

        $current->next = $node;
    }


    public function search(URI $uri): ?Route
    {
        $current = $this->head;

        while ($current !== null) {
            if ($current->value->match($uri)) {
                return $current->value;
            }
            $current = $current->next;
        }

        return null;
    }

    public function mergeAtTail(LinkedList $list)
    {
        $current = $this->head;

        while ($current->next !== null)
            $current = $current->next;

        $list->forEach(function (Route $r) {
            $this->add($r);
        });

        return null;
    }

    public function forEach($callback)
    {
        $current = $this->head;

        while ($current !== null) {

            call_user_func($callback, $current->value);

            $current = $current->next;
        }
    }
}

class ListNode
{
    public Route $value;
    public ?ListNode $next;

    public function __construct(Route $value)
    {
        $this->value = $value;
        $this->next = null;
    }
}
