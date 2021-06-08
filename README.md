# MediaWiki GM Screen

This extension provides a mechanism for creating wiki pages with hidden notes.
These pages exist in a dedicated namespace and can be associated with a page in
the main namespace (similar to a talk page). For example, the game master for a
tabletop roleplaying game may have a page that is visible to the players with
information about a character or location, as well as an additional page for
that character or location with information only visible to the GM.

## Features

A namespace is created to hold pages only visible to users in a specified
group. For users in that group, pages in the main namespace display a tab to
access the page of the same name in the GM namespace, and the contents of the
GM page are optionally displayed at the end of the associated main page.

## Security

GM Screen relies on [the Lockdown extension](https://www.mediawiki.org/wiki/Extension:Lockdown)
for namespace permissions; be aware of [the security considerations](https://www.mediawiki.org/wiki/Security_issues_with_authorization_extensions)
of using MediaWiki for this purpose. A best effort is made to protect hidden
information, but a malicious user may trivially see, at minimum, the titles of
hidden pages.

In addition to the protections provided by Lockdown, changes to pages in the GM
namespace are hidden from the Recent Changes page.

## Installation

First, ensure that [Lockdown](https://www.mediawiki.org/wiki/Extension:Lockdown#Installation)
is installed.

Place this extension in a `GmScreen` directory in your installation's
`extensions` directory. In LocalSettings.php, define a
[namespace](https://www.mediawiki.org/wiki/Manual:Using_custom_namespaces) for
GM notes and set the group permissions appropriately.

```php
wfLoadExtension( 'Lockdown' );

define('NS_GM', 3000); # Custom namespaces start at 3000; change as appropriate
$wgExtraNamespaces[NS_GM] = 'GM'; # Set the namespace name as desired
$wgNonincludableNamespaces[] = NS_GM; # Don't allow transclusion for this namespace
$wgGroupPermissions['gm']['read'] = true; # Create a user group
$wgNamespacePermissionLockdown[NS_GM]['read'] = [ 'gm' ]; # Restrict access to the GM namespace

wfLoadExtension( 'GmScreen' );

$wgGmScreenNamespace = NS_GM; # This should be the namespace created above
#$wgGmScreenText = 'GM Notes'; # Optional: set the text for the page tab (default 'GM')
#$wgGmScreenEmbedPage = false; # Optional: for GM, show notes on main page (default true)
```

Add the GM(s) to the specified group (`gm` in this example). They should then
see the GM tab and be able to create and view pages in the new namespace.
