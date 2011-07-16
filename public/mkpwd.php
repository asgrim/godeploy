<?php

// This is just a test script to generate an encrypted password until the UAC
// is in place...
echo crypt($_GET['pwd'], '$6$rounds=5000$' . substr(md5(microtime().rand()),0,16) . '$');