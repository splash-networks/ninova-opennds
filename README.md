# Mikrotik External Captive Portal

The following actions are required to use the code given in this repo:

## Portal Setup Using Git

Clone the repo using git. Copy the `.env.example` file to `.env` and set the values of the given environment variables in it:

```
cp .env.example .env
nano .env
```

Navigate to public folder:

`cd public`

Use [this](https://getcomposer.org/download/) link to install Composer. Then run `php composer.phar install` to install the packages given in `composer.json`.

## Apache Virtual Hosts

Apache virtual host can be setup on the portal server using the instructions given [here](https://gist.github.com/nasirhafeez/d47c9d68742227a23f1011455a190490#apache-site-setup).
