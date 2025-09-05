

This is a modification to Relaticle CRM - an open source Laravel/Filament CRM tool. 

Relaticle links: https://relaticle.com/ 

I am **not affiliated with Relaticle** and I make no claims to be. 

---
This is a written guide to installing the Plugin `relaticle-mods` - setting it out because there is a few steps to take before its working as it should be. 😄 I will do my best to be clear and concise in my instruction. 

### Prep

First thing is you need an active install of Relaticle, I cannot be sure about the installation in question, but the steps I give will all be as if you are installing this on a regular cPanel, with all its limitations and quirks. (I also suck at writing, so I am just doing this as if I am speaking, will clean up afterwards).

So again things you need to make sure are set: 

- Have Relaticle installed. 
- Your cPanel has PHP 8.4 (there is a little share here, so don't worry if your server says 8.2, you are fine, as long as the domain you are installing this to can be managed via MultiPHP its fine.)
- Filament installed and working - again, if you have Relaticle this is a given, but fair to just mention it. 
- Access to a terminal, unfortunately this is the way I am doing it. I believe this can be done with SSH as well? I am not certain and don't bet on my assumption about it, terminal is what I know. Of this I can assure you its 100%. 

Now that you have made sure of those little things step are: 

- In terminal `cd` to the domain with your Relaticle installation. 
- Once you have done that, double check if you need the "hack" or not, the hack being Relaticle/Filament require PHP 8.3+ - but from my reading most cPanels have PHP 8.2 :) sucks, I know, but fear not if you run `php -v` in your terminal and you are lucky enough to have 8.3+ congrats, you can run the commands fine, if you see `8.2.9` - sorry - I see it too, but its alright, we have a fix. 
	- If you have `8.2.9`  or less than `8.3` then you will need to directly run the commands with the PHP Binary for 8.4, its not a big deal, nothing weird, just can't easy mode with `php artisan migrate` it now becomes `/opt/cpanel/ea-php84/root/usr/bin/php artisan migrate` the first part is you directly telling the terminal "Hey, I need you to use PHP 8.4 to run this, not the one you detect here, but this one." 
	- Because the environment I installed this in is `8.2` for the server I will be running the explicit binary for 8.4 if you don't need to just use the truncated version. 


#### Installation steps. 

I will list the terminal commands step by step and the actions you need to take: 


`cd` into the domain as mentioned above, now make sure you're there then proceed with the commands: 
1.  Install the plugin `composer require ofthewildfire/relaticle-mods`
	1. If there are deprecation errors you are fine, many packages for Relaticle itself out a little out of date, but its fine. It will not affect the installed product. 
2. Navigate to the "File Manager" in cPanel and find your installation folder `public_html/installation_folder.com`
3. Once inside your installation folder, navigate to `bootstrap/providers` and edit `providers` and the following line to the bottom of the array:

```php

Ofthewildfire\RelaticleModsPlugin\RelaticleModsServiceProvider::class,

```

4. This will tell the App "Hey, this thing provides stuff for us to use". Important stuff. 
5. Now you need to Register your plugin (same as OctoberCMS actually if you have used that) - go to `app/Providers/Filament/AppPanelProvider` and edit the `AppPanelProvider` without this registration the Panel (what you see) won't know what you're talking about with regards to the addition of new resources, to register add this to the `plugins` array: 

```php
\Ofthewildfire\RelaticleModsPlugin\RelaticleModsPlugin::make(),
```

Relaticle has their own plugin there, the Custom Fields, add it below, keep the trailing comma its fine in PHP. 

6. Now for the putting the pieces together, you need to add relationships to a few models, the models we are using in the Plugin, namely: 

- Task
- Note
- People

Events and Projects are *our own* so we control those, but the relationships above are Relaticle core, we do not, so we have to add our relationships there, not hard just copy from here will give it all below: 

#### app/Models/Task

First one is the task model, you need to add the following, these add relationships either to existing to our own or our own to existing.  

```php

public function projects(): MorphToMany

{

    return $this->morphToMany(Projects::class, 'taskable');

}

  

public function events(): MorphToMany

{

    return $this->morphToMany(Events::class, 'taskable');

}

  

public function ideas(): MorphToMany

{

    return $this->morphToMany(Ideas::class, 'taskable');

}

```


#### app/Models/Note

```php
public function projects(): MorphToMany

{

    return $this->morphToMany(Projects::class, 'noteable');

}

  

public function events(): MorphToMany

{

    return $this->morphToMany(Events::class, 'noteable');

}

  

public function ideas(): MorphToMany

{

    return $this->morphToMany(Ideas::class, 'noteable');

}
```

and finally to: 

#### app/Models/People

```php
public function ideas()

{

    return $this->belongsToMany(

        \Ofthewildfire\RelaticleModsPlugin\Models\Ideas::class,

        'idea_people',

        'people_id',

        'idea_id'

    );

}

  



public function projects()

{

    return $this->belongsToMany(

        \Ofthewildfire\RelaticleModsPlugin\Models\Projects::class,

        'project_team_members',

        'people_id',

        'projects_id'

    );

}

  

public function events()

{

    return $this->belongsToMany(

        \Ofthewildfire\RelaticleModsPlugin\Models\Events::class,

        'event_people',

        'people_id',

        'event_id'

    );

}
```


These add our relationships to Relaticle. Making sure everything works together, not against each other. 

After that, `php artisan migrate` and everything should be running smoothly. 🙏

---

Notes: I am always eager to learn and readily admit I am learning still, so if you see something that could be done better don't hesitate to let me know. Its how I learn. 🌟🌟

---
Enjoy and have a good day. 
