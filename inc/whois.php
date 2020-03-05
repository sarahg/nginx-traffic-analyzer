<?php

// Run "whois" for a given IP address.
echo('<pre>' . shell_exec('whois ' . $_GET['ip']) . '</pre>');