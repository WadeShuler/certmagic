# CertMagic
Easily generate self-signed certificates for all of your local development domains.

> **WARNING** This is a pre-release and currently for my use. Only use if you know what you are doing and can help with perfecting this script. **This has only been tested on OSX High Sierra with XAMPP 7.2.4!** If your not on High Sierra (especially if not on a Mac), then comment out the 2 calls to `sudo security xxxx` within the `makecert` file. OSX uses a Keychain that we have to add our self signed certs to. Windows/Linux might work without doing anything extra. If your on an older OSX version, the commands may be different. In which case, comment out the calls and manually add the cert to your keychain. In the future, I would like to iron out these differences and have this script handle a few OSX versions, Windows, and Linux. For now, it's created for OSX High Sierra.

## READ BEFORE USE
This was built for Mac OSX High Sierra and may not work with other versions. This uses special commands to automatically remove/add your certificate to your Keychain. That command may be different on older OSX versions. That command also requires root access. Feel free to inspect the `makecert` file and review the 2 calls to `sudo security xxxx` command. You can comment them out and run them manually, or manually add the certificate to your Keychain yourself, if your not comfortable with a script needing to run `sudo`.

This will add `CertMagic:` to the beginning of the certificate. This is so it can find it later, to delete it, before regenerating it again.

## Why use this?

If your like me, you want to use `https` on your local development and are tired of seeing the not secure warnings.

Before this script, you had two options:

1. Create a new individual certificate for each new local dev site. (dumb)

2. Add your new domain to the end of your `DNS.x` list in your config and regenerate the cert again. (dumber)

With this script, you can add a new domain with a self-signed certificate to your local development environment in under a minute!

If a week later you start a new project and need to add a new development domain, simply add it to the `domains` array in `config.php`, run `./makecert` again, and it will regenerate the certificate again with the new domain added to the list automatically. Update your `vhost` and `/etc/hosts` file, restart Apache, and your set.

## Installation

I recommend installing this in your `XAMPP/xampfiles` directory. At least that's where I tested at. I am not sure if the certs will work from anywhere outside of the XAMPP dir (will test later).

    cd /Applications/XAMPP/xamppfiles
    git clone git@github.com:WadeShuler/certmagic.git

Edit `config.php` and replace the dummy data with your own. The `domains` array is the magic sauce. It will loop through them and automatically add the appropriate `DNS.1`, `DNS.2`, etc into the cert.

Then run:

    ./makecert

It will prompt you for your cert info. The defaults are populated from the config, so you can just hit enter through each one.

### Add domains to vhosts (if not already there)

You still need to manually add your domains into your vhost file. This script does not do that for you!

    # https for localhost
    <VirtualHost *:443>
        ServerName localhost
        DocumentRoot "/Applications/XAMPP/xamppfiles/htdocs"
        SSLEngine on
        SSLCertificateFile "/Applications/XAMPP/xamppfiles/certmagic/certs/server.crt"
        SSLCertificateKeyFile "/Applications/XAMPP/xamppfiles/certmagic/certs/server.key"
    </VirtualHost>

    # example for laravel.local
    <VirtualHost laravel.local:443>
        ServerName laravel.local
        ServerAlias www.laravel.local
        DocumentRoot "/Applications/XAMPP/xamppfiles/htdocs/laravel/public"
        SSLEngine on
        SSLCertificateFile "/Applications/XAMPP/xamppfiles/certmagic/certs/server.crt"
        SSLCertificateKeyFile "/Applications/XAMPP/xamppfiles/certmagic/certs/server.key"
    </VirtualHost>

    # examples for yii2 with subdomains (based off my Yii2-Members-System)
    # yii2 main website
    <VirtualHost yii2-dashboard.local:443>
        ServerName yii2-dashboard.local
        ServerAlias www.yii2-dashboard.local
        DocumentRoot "/Applications/XAMPP/xamppfiles/htdocs/yii2-dashboard/mainsite/web"
        SSLEngine on
        SSLCertificateFile "/Applications/XAMPP/xamppfiles/certmagic/certs/server.crt"
        SSLCertificateKeyFile "/Applications/XAMPP/xamppfiles/certmagic/certs/server.key"
    </VirtualHost>

    # yii2 admin (backend) subdomain
    <VirtualHost admin.yii2-dashboard.local:443>
        ServerName admin.yii2-dashboard.local
        DocumentRoot "/Applications/XAMPP/xamppfiles/htdocs/yii2-dashboard/backend/web"
        SSLEngine on
        SSLCertificateFile "/Applications/XAMPP/xamppfiles/certmagic/certs/server.crt"
        SSLCertificateKeyFile "/Applications/XAMPP/xamppfiles/certmagic/certs/server.key"
    </VirtualHost>

    #yii2 users (frontend) sub-domain
    <VirtualHost users.yii2-dashboard.local:443>
        ServerName users.yii2-dashboard.local
        DocumentRoot "/Applications/XAMPP/xamppfiles/htdocs/yii2-dashboard/frontend/web"
        SSLEngine on
        SSLCertificateFile "/Applications/XAMPP/xamppfiles/certmagic/certs/server.crt"
        SSLCertificateKeyFile "/Applications/XAMPP/xamppfiles/certmagic/certs/server.key"
    </VirtualHost>

### Add domains to your hosts file:

Don't forget to add your domain names to your `/etc/hosts`. This script does not do that for you!

    sudo nano /etc/hosts

Here is an example of some host file entries, based off the domains I used above:

    127.0.0.1    localhost
    127.0.0.1    laravel.local
    127.0.0.1    yii2-dashboard.local
    127.0.0.1    users.yii2-dashboard.local
    127.0.0.1    admin.yii2-dashboard.local

    # generic examples
    127.0.0.1    domain.local
    127.0.0.1    sub.domain.local
    127.0.0.1    example.local
    127.0.0.1    sub.example.local
    ...

Use `ctrl+o` followed by `ctrl+x` to save and exit nano. Alternatively, you can use your favorite editor, such as Atom via `sudo atom /etc/hosts`. Enter your password and it will open in Atom.

### Restart Apache

If you generated the cert, added domains (and referenced cert) to your VirtualHost, and added the domains to your hosts file, all that is left to do is restart Apache.

### Done :)

Your local domains should be green and "secure" when using "https".

---

## Example of generated DNS entries in config:

For those who know a bit about the certificate config files, here is how it references your domains:

    [alternate_names]
    DNS.1 = localhost
    DNS.2 = domain.local
    DNS.3 = *.domain.local
    DNS.4 = example.local
    DNS.5 = *.example.local

## Screenshots

![Localhost][ss-localhost]
![Laravel][ss-laravel]
![Yii2][ss-yii2]

[ss-localhost]: https://github.com/WadeShuler/certmagic/raw/master/docs/images/localhost.png "Localhost"
[ss-laravel]: https://github.com/WadeShuler/certmagic/raw/master/docs/images/laravel.png "Laravel"
[ss-yii2]: https://github.com/WadeShuler/certmagic/raw/master/docs/images/yii2.png "Yii2"
