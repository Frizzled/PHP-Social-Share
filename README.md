PHP SocialShare
=======

## How it works
Instantiate SocialShare when you want to make a server-side redirect to a social network to share URL.

## Example
```
new SocialShare(
	[
		'url' => 'https://github.com/Frizzled/PHP-Social-Share',
		'title' => 'Check out SocialShare!',
		'hashtags' => '#PHP #SocialShare'
	],
	SocialShare::TWITTER
);
```

Page will redirect via headers after class is called.