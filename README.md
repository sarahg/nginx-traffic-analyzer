# nginx traffic analyzer

Parse nginx access logs and return your top visitor IPs and user agents.

## Requirements
* nginx-access log(s) from your host's webserver
* PHP 5.4+ installed on your local machine
* a little working knowledge of using the command-line

## Usage
 
Fire up the app using your terminal:
1. Clone this repository: `git clone https://github.com/sarahg/nginx-traffic-analyzer`
2. Move into the app directory: `cd nginx-traffic-analyzer`
3. Start up your PHP webserver: `php -S localhost:8000`
4. In a browser, open this page: http://localhost:8000

Retrieve your logs:
1. Download nginx access logs from your webserver(s).

2. Move your nginx access logs into the "logs" directory of this repository. This app will run a combined report
 of all log files in this directory.

_If your site is on Pantheon, you can use `misc/collect-pantheon-logs.sh` to retrieve your logs. 
 Add your site UUID to that script, then run it from the app directory: `./misc/pantheon-collect-logs.sh`_
 
_You can also download Pantheon logs [via SFTP](https://pantheon.io/docs/logs#access-logs-via-sftp)._

3. Hit the "Analyze IPs" and/or "Analyze User Agents" buttons to run reports.

Note: There are lots of different ways to format nginx logs. If your webserver uses a different
format, you might have to adjust the search command arguments in the `reportAttributes()` function in 
inc/analyze.php. This tool works with logs formatted like this:

```
time_combined '$remote_addr - $remote_user [$time_local]  '
    '"$request" $status $body_bytes_sent '
    '"$http_referer" "$http_user_agent" $request_time '
    '"$http_x_forwarded_for"';
```

## So what do I do with these results?

### IP addresses
If you notice an IP driving a disproportionate or abnormal amount of traffic, you might want to block it. Before blocking it, you should check where it comes from -- you don't want to block something useful (e.g, GoogleBot, or yourself). 
 
Click the "Lookup" link next to each IP to view [WHOIS](https://en.wikipedia.org/wiki/WHOIS) information for the IP.

#### How to block IPs
You can effectively block IPs at the application level with PHP code. In Drupal, this code should go in `settings.php`; in WordPress, this should go in `wp-config.php` in order to run before anything else.

You can create a code snippet for IP blocking by selecting IPs on the list, and a PHP code snippet will appear at the bottom of the column.
 You can copy/paste this into your CMS settings file.

Drupal provides a module called [Ban](https://www.drupal.org/docs/8/core/modules/ban/overview) that can block IPs entered into the admin interface, but blocking IPs with PHP code before the CMS bootstraps is significantly more performant. WordPress is similar -- there are certainly plugins for blocking IPs, but using PHP code in `wp-config.php` will be less of a performance hit.

You can also block IPs using a CDN like Fastly or Cloudflare, or with a web application firewall (WAF).

### User agents
Respectable bots with distinguishable user agents can be blocked by modifying your [robots.txt file](https://support.google.com/webmasters/answer/6062596?hl=en).

Some bots are not respectable. If you're able to detect these by User Agent but they don't respect robots.txt, you can block them with PHP ([example](https://stackoverflow.com/a/1358031)) in your CMS config file, similar to blocking IPs.

Like IPs, these can also be blocked using a CDN or WAF. 