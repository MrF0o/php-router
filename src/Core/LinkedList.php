<?php
namespace Mrfoo\Router\Core;


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