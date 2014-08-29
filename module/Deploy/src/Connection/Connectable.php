<?php

namespace Deploy\Connection;

interface Connectable
{
    public function connect();

    public function disconnect();
}
